<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\Box;
use App\Models\InspectionLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ActivityLog;
use Illuminate\Support\Str;


class ContainerController extends Controller
{
    public function index(Request $request)
    {
        $query = Container::with('receivedByUser')
            ->withCount('items', 'inspectionLabels');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('container_number', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%")
                  ->orWhere('packing_list_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($customs = $request->input('customs_status')) {
            $query->where('customs_status', $customs);
        }

        $containers = $query->latest()->paginate(15)->withQueryString();

        return view('containers.index', compact('containers'));
    }

    public function create()
    {
        return view('containers.create');
    }

    /**
     * Guardar contenedor — si se sube un packing list XLSX, se extraen
     * automáticamente los datos del header y los items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'container_number'      => 'nullable|string|max:50',
            'supplier'              => 'nullable|string|max:255',
            'origin_country'        => 'nullable|string|max:100',
            'declared_qty'          => 'nullable|integer|min:0',
            'customs_status'        => 'required|in:pendiente,en_revision,liberado,retenido',
            'notes'                 => 'nullable|string|max:1000',
            'packing_list'          => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
            'container_seal_number' => 'nullable|string|max:100',
            'tax_id'                => 'nullable|string|max:100', // Agregado para soportar Tax ID
        ]);

        DB::beginTransaction();
        try {
            // Datos base — se pueden sobrescribir si el XLSX trae la info
            $containerData = [
                'container_number'      => $validated['container_number'] ?? 'TEMP-' . now()->format('YmdHis'),
                'supplier'              => $validated['supplier'] ?? null,
                'origin_country'        => $validated['origin_country'] ?? null,
                'declared_qty'          => $validated['declared_qty'] ?? 0,
                'customs_status'        => $validated['customs_status'],
                'notes'                 => $validated['notes'] ?? null,
                'received_by'           => Auth::id(),
                'received_at'           => now(),
                'status'                => 'abierto',
                'container_seal_number' => $validated['container_seal_number'] ?? null,
                'tax_id'                => $validated['tax_id'] ?? null,
            ];

            // Si hay packing list, extraer metadata del header primero
            if ($request->hasFile('packing_list')) {
                $file = $request->file('packing_list');
                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, ['xlsx', 'xls'])) {
                    $headerData = $this->extractHeaderFromXlsx($file->getRealPath());
                    // Sobrescribir con datos del XLSX si no vinieron del form
                    $containerData = array_merge($containerData, array_filter($headerData));
                }
            }

            // ===================================================================
            // VALIDACIÓN ESTRICTA DE DUPLICADOS
            // ===================================================================
            $hasDuplicateCondition = false;
            $duplicateQuery = Container::query();

            $duplicateQuery->where(function ($q) use ($containerData, &$hasDuplicateCondition) {
                // Solo validamos container_number si no es un temporal generado por el sistema
                if (!empty($containerData['container_number']) && !str_starts_with($containerData['container_number'], 'TEMP-')) {
                    $q->orWhere('container_number', $containerData['container_number']);
                    $hasDuplicateCondition = true;
                }
                if (!empty($containerData['packing_list_number'])) {
                    $q->orWhere('packing_list_number', $containerData['packing_list_number']);
                    $hasDuplicateCondition = true;
                }
                if (!empty($containerData['container_seal_number'])) {
                    $q->orWhere('container_seal_number', $containerData['container_seal_number']);
                    $hasDuplicateCondition = true;
                }
                if (!empty($containerData['tax_id'])) {
                    $q->orWhere('tax_id', $containerData['tax_id']);
                    $hasDuplicateCondition = true;
                }
            });

            // Si se ingresó al menos un dato rastreable y existe en BD, detenemos todo
            if ($hasDuplicateCondition && $duplicateQuery->exists()) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Alerta: Posible duplicado. El Contenedor, Sello, Packing List o Tax ID ya se encuentra registrado en el sistema. Verifique la información.');
            }
            // ===================================================================

            $container = Container::create($containerData);

            // Guardar archivo y parsear items
            if ($request->hasFile('packing_list')) {
                $path = $request->file('packing_list')
                    ->store('packing-lists/' . $container->id, 'public');
                $container->update(['packing_list_path' => $path]);

                $this->importPackingListItems($container, $request->file('packing_list'));
                $container->recalculateFromItems();
            }

            DB::commit();

            return redirect()->route('containers.show', $container)
                ->with('success', 'Contenedor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error al registrar el contenedor: ' . $e->getMessage());
        }
    }

    public function show(Container $container)
    {
        $container->load([
            'items.inspectionLabels',
            'inspectionLabels.inspector',
            'receivedByUser',
        ]);

        $stats = [
            'total_items'    => $container->items->count(),
            'conformes'      => $container->items->where('status', 'conforme')->count(),
            'con_diferencia' => $container->items->where('status', 'con_diferencia')->count(),
            'pendientes'     => $container->items->where('status', 'pendiente')->count(),
            'labels_total'   => $container->inspectionLabels->count(),
            'labels_printed' => $container->inspectionLabels->where('printed', true)->count(),
        ];

        return view('containers.show', compact('container', 'stats'));
    }

    // ===================================================================
    // PARSING DEL PACKING LIST (XLSX)
    // ===================================================================

    /**
     * Extrae metadatos del header del packing list (filas 1-14).
     * Formato esperado: Miniso/Sak Logistiks packing list.
     */
    private function extractHeaderFromXlsx(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $data = [];

        // Recorrer filas 1-14 buscando patrones conocidos
        for ($row = 1; $row <= 14; $row++) {
            $colA = trim((string) $sheet->getCell("A{$row}")->getValue());
            $colH = trim((string) $sheet->getCell("H{$row}")->getValue());

            $colA_upper = strtoupper($this->toAscii($colA));
            $colH_upper = strtoupper($this->toAscii($colH));

            // Packing List No (H4)
            if (str_contains($colH_upper, 'PACKING LIST NO')) {
                $data['packing_list_number'] = $this->extractAfterColon($colH);
            }

            // Container (H11)
            if (str_contains($colH_upper, 'CONTAINER')) {
                $data['container_number'] = $this->extractAfterColon($colH);
            }

            // Transport mode (H6)
            if (str_contains($colH_upper, 'MODE OF TRANSPORT')) {
                $data['transport_mode'] = $this->extractAfterColon($colH);
            }

            // Port of loading (H8)
            if (str_contains($colH_upper, 'PORT OF LOADING')) {
                $data['port_loading'] = $this->extractAfterColon($colH);
            }

            // Port of discharge (H9)
            if (str_contains($colH_upper, 'PORT OF DISCHARGE')) {
                $data['port_discharge'] = $this->extractAfterColon($colH);
            }

            // ETD (H12)
            if (str_contains($colH_upper, 'ETD')) {
                $etd = $this->extractAfterColon($colH);
                $data['etd'] = $this->parseDate($etd);
            }

            // ETA (H13)
            if (str_contains($colH_upper, 'ETA')) {
                $eta = $this->extractAfterColon($colH);
                $data['eta'] = $this->parseDate($eta);
            }

            // Seller / Supplier (A7)
            if (str_contains($colA_upper, 'SELLER')) {
                $data['supplier'] = $this->extractAfterColon($colA);
            }

            // Buyer (A4)
            if (str_contains($colA_upper, 'BUYER')) {
                $data['buyer'] = $this->extractAfterColon($colA);
            }
        }

        return $data;
    }

    /**
     * Importa los items del packing list desde el XLSX.
     * Los datos inician en la fila donde la columna A dice "ITEM NO." (header),
     * y los items van desde la fila siguiente hasta la fila que dice "TOTAL".
     */
    private function importPackingListItems(Container $container, $file): void
    {
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'csv') {
            $this->importFromCsv($container, $file);
            return;
        }

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $maxRow = $sheet->getHighestRow();

        // Buscar la fila de headers (donde A dice "ITEM NO." o similar)
        $headerRow = null;
        for ($r = 1; $r <= min(20, $maxRow); $r++) {
            $val = strtoupper(trim((string) $sheet->getCell("A{$r}")->getValue()));
            if (str_contains($val, 'ITEM') && str_contains($val, 'NO')) {
                $headerRow = $r;
                break;
            }
        }

        if (!$headerRow) {
            // Fallback: asumir fila 15 como en el formato conocido
            $headerRow = 15;
        }

        // Leer items desde headerRow+1 hasta TOTAL o fin
        for ($r = $headerRow + 1; $r <= $maxRow; $r++) {
            $colA = trim((string) $sheet->getCell("A{$r}")->getValue());

            // Si llegamos a TOTAL, terminamos
            if (strtoupper($colA) === 'TOTAL' || empty($colA)) {
                break;
            }

            $cartonNumbers = trim((string) $sheet->getCell("B{$r}")->getValue());
            $cartonCount = empty($cartonNumbers) ? 0 : count(array_filter(explode(',', $cartonNumbers)));

            ContainerItem::create([
                'container_id'         => $container->id,
                'item_number'          => (int) $colA,
                'product_code'         => trim((string) $sheet->getCell("C{$r}")->getValue()) ?: null,
                'barcode'              => trim((string) $sheet->getCell("D{$r}")->getValue()) ?: null,
                'product_description_cn' => trim((string) $sheet->getCell("E{$r}")->getValue()) ?: null,
                'product_description'  => trim((string) $sheet->getCell("F{$r}")->getValue()) ?: 'Sin descripción',
                'declared_qty'         => (int) ($sheet->getCell("G{$r}")->getValue() ?? 0),
                'cbm'                  => (float) ($sheet->getCell("H{$r}")->getValue() ?? 0),
                'net_weight_kg'        => (float) ($sheet->getCell("I{$r}")->getValue() ?? 0),
                'gross_weight_kg'      => (float) ($sheet->getCell("J{$r}")->getValue() ?? 0),
                'package_type'         => trim((string) $sheet->getCell("K{$r}")->getValue()) ?: null,
                'carton_numbers'       => $cartonNumbers ?: null,
                'carton_count'         => $cartonCount,
                'received_qty'         => 0,
            ]);
        }
    }

    /**
     * Import fallback para CSV.
     */
    private function importFromCsv(Container $container, $file): void
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if (!$header) {
                $header = array_map('strtolower', array_map('trim', $row));
                continue;
            }

            if (count($row) < count($header)) continue;
            $data = @array_combine($header, $row);
            if (!$data) continue;

            ContainerItem::create([
                'container_id'         => $container->id,
                'product_code'         => $data['product code'] ?? $data['sku'] ?? $data['codigo'] ?? null,
                'barcode'              => $data['bar code'] ?? $data['barcode'] ?? null,
                'product_description'  => $data['product description (en)'] ?? $data['description'] ?? $data['descripcion'] ?? 'Sin descripción',
                'product_description_cn' => null, 
                'declared_qty'         => (int) ($data['quantity (pcs)'] ?? $data['qty'] ?? $data['cantidad'] ?? 0),
                'cbm'                  => (float) ($data['total measurement (cbm)'] ?? 0),
                'net_weight_kg'        => (float) ($data['total/n.w kg'] ?? 0),
                'gross_weight_kg'      => (float) ($data['total/g.w kg'] ?? 0),
                'package_type'         => $data['kind of package'] ?? null,
                'carton_numbers'       => $data['carton no.'] ?? $data['carton no'] ?? null,
            ]);
        }

        fclose($handle);
    }

    // ===================================================================
    // HELPERS
    // ===================================================================

    private function extractAfterColon(string $text): string
    {
        // Normalizar colons: fullwidth ： (U+FF1A) → ASCII :
        $text = str_replace('：', ':', $text);

        // Partir por el primer ":"
        $parts = explode(':', $text, 2);
        $value = isset($parts[1]) ? $parts[1] : $text;

        // Eliminar todo lo que NO sea ASCII imprimible (letras, números, puntuación básica)
        $value = $this->toAscii($value);

        return trim($value);
    }

    /**
     * Elimina caracteres no-ASCII de un string.
     * Deja solo: letras a-z A-Z, números 0-9, espacios y puntuación común.
     */
    private function toAscii(string $text): string
    {
        // Reemplazar saltos de línea por espacio
        $text = preg_replace('/[\r\n]+/', ' ', $text);

        // Dejar solo ASCII imprimible (0x20-0x7E)
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);

        // Colapsar espacios múltiples
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    private function parseDate(?string $dateStr): ?string
    {
        if (empty($dateStr)) return null;
        $dateStr = trim($dateStr);
        try {
            return \Carbon\Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    // ===================================================================
    // OPERACIONES SOBRE ITEMS
    // ===================================================================

    public function addItem(Request $request, Container $container)
    {
        $validated = $request->validate([
            'product_code'        => 'nullable|string|max:100',
            'barcode'             => 'nullable|string|max:100',
            'product_description' => 'required|string|max:500',
            'declared_qty'        => 'required|integer|min:1',
            'carton_count'        => 'required|integer|min:1',
            'package_type'        => 'nullable|string|max:50',
        ]);

        $validated['container_id'] = $container->id;
        $validated['item_number'] = ($container->items()->max('item_number') ?? 0) + 1;
        ContainerItem::create($validated);
        $container->recalculateFromItems();

        return back()->with('success', 'Artículo agregado correctamente.');
    }

    public function updateItemReceived(Request $request, ContainerItem $item)
    {
        $validated = $request->validate([
            'received_cartons' => 'required|integer|min:0',
        ]);

        $item->updateReceivedCartons($validated['received_cartons'], Auth::id());
        $item->container->recalculateFromItems();

        return back()->with('success', "Recepción actualizada: {$validated['received_cartons']} cajas ({$item->received_qty} pzas).");
    }

    /**
     * Guardar notas de un item (solo para faltante/sobrante).
     */
    public function updateItemNotes(Request $request, Container $container, ContainerItem $item)
    {
        $request->validate([
            // Cambiamos a 'nullable' para permitir borrar la nota
            'notes' => 'nullable|string|max:500', 
        ]);

        // Lógica para Deshacer: Si envían nota vacía y estaba como no recibido, revertimos a pendiente
        if (empty($request->notes) && $item->status === 'no_recibido') {
            $item->status = 'pendiente';
        }

        $item->notes = $request->notes;
        $item->save();

        return $this->cartonAdjustResponse($container, $item);
    }

    // ===================================================================
    // ESTATUS DEL CONTENEDOR
    // ===================================================================

    public function updateCustomsStatus(Request $request, Container $container)
    {
        $validated = $request->validate([
            'customs_status' => 'required|in:pendiente,en_revision,liberado,retenido',
        ]);

        $container->update($validated);
        return back()->with('success', 'Estatus de aduana actualizado.');
    }

    public function close(Container $container)
    {
        // Validar que no haya pendientes
        if (!$container->canClose()) {
            return back()->with('error', 'No se puede cerrar: hay artículos pendientes de revisar.');
        }

        DB::beginTransaction();
        try {
            // 1. Cambiamos el estatus del contenedor
            $container->update(['status' => 'cerrado']);

            // 2. Generamos las cajas físicas en el sistema
            foreach ($container->items as $item) {
                
                if ($item->received_cartons > 0) {
                    // Calculamos las piezas que lleva cada caja
                    $piezasPorCaja = $item->carton_count > 0 
                                    ? floor($item->declared_qty / $item->carton_count) 
                                    : 0;

                    for ($i = 0; $i < $item->received_cartons; $i++) {
                        
                        // Generar un código único (Ej: BX-14-2-001-A4F2)
                        // Combina ID Contenedor, ID Item, Secuencia y un string aleatorio para evitar colisiones
                        $secuencia = str_pad($i + 1, 3, '0', STR_PAD_LEFT);
                        $boxCode = "BX-{$container->id}-{$item->id}-{$secuencia}-" . strtoupper(Str::random(4));

                        Box::create([
                            'container_id'      => $container->id,
                            'container_item_id' => $item->id,
                            'box_code'          => $boxCode,
                            'source'            => 'contenedor',
                            'quantity'          => $piezasPorCaja,
                            'expected_qty'      => $piezasPorCaja,
                            'status'            => 'cerrada',
                            'closed_at'         => now(),
                            'created_by'        => Auth::id(),
                        ]);
                    }
                }
            }
            ActivityLog::create([
                        'action'  => 'CONTAINER_CLOSED',
                        'user_id' => Auth::id(),
                        'details' => [
                            'container_id'     => $container->id,
                            'container_number' => $container->container_number,
                            'total_boxes'      => $container->boxes()->count(),
                        ]
                    ]);
            DB::commit();
            
            return back()->with('success', 'Contenedor cerrado exitosamente. Las cajas han sido ingresadas al inventario.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cerrar el contenedor y generar cajas: ' . $e->getMessage());
        }
    }

    // ===================================================================
    // ETIQUETADO INICIAL (ADUANA)
    // ===================================================================

    public function inspection(Container $container)
    {
        $container->load(['items', 'inspectionLabels.inspector', 'inspectionLabels.containerItem']);
        return view('containers.inspection', compact('container'));
    }

    public function generateLabels(Request $request, Container $container)
    {
        $validated = $request->validate([
            'container_item_id' => 'required|exists:container_items,id',
            'quantity'          => 'required|integer|min:1|max:5000',
        ]);

        $item = ContainerItem::findOrFail($validated['container_item_id']);
        $lastPiece = $container->inspectionLabels()->max('piece_number') ?? 0;

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $validated['quantity']; $i++) {
                $pieceNumber = $lastPiece + $i;
                InspectionLabel::create([
                    'container_id'      => $container->id,
                    'container_item_id' => $item->id,
                    'label_code'        => InspectionLabel::generateLabelCode($container, $pieceNumber),
                    'piece_number'      => $pieceNumber,
                    'inspection_status' => 'pendiente',
                ]);
            }

            DB::commit();
            return back()->with('success', "Se generaron {$validated['quantity']} etiquetas.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar etiquetas: ' . $e->getMessage());
        }
    }

    public function updateLabelStatus(Request $request, InspectionLabel $label)
    {
        
        $validated = $request->validate([
            'inspection_status' => 'required|in:conforme,pendiente,con_diferencia',
            'notes'             => 'nullable|string|max:500',
        ]);

        $label->markAsInspected($validated['inspection_status'], Auth::id(), $validated['notes'] ?? null);
        return back()->with('success', "Etiqueta {$label->label_code} actualizada.");
    }

    public function bulkInspect(Request $request, Container $container)
    {

        if ($request->has('label_ids_string') && !empty($request->input('label_ids_string'))) {
            $request->merge([
                'label_ids' => explode(',', $request->input('label_ids_string'))
            ]);
        }
        
        $validated = $request->validate([
            'label_ids'         => 'required|array|min:1',
            'label_ids.*'       => 'exists:inspection_labels,id',
            'inspection_status' => 'required|in:conforme,pendiente,con_diferencia',
        ]);

        InspectionLabel::whereIn('id', $validated['label_ids'])
            ->where('container_id', $container->id)
            ->update([
                'inspection_status' => $validated['inspection_status'],
                'inspected_by'      => Auth::id(),
                'inspected_at'      => now(),
            ]);

        return back()->with('success', count($validated['label_ids']) . " etiquetas actualizadas.");
    }

    public function markPrinted(Request $request, Container $container)
    {
        if ($request->has('label_ids_string') && !empty($request->input('label_ids_string'))) {
            $request->merge([
                'label_ids' => explode(',', $request->input('label_ids_string'))
            ]);
        }

        $validated = $request->validate([
            'label_ids'   => 'required|array|min:1',
            'label_ids.*' => 'exists:inspection_labels,id',
        ]);

        InspectionLabel::whereIn('id', $validated['label_ids'])
            ->where('container_id', $container->id)
            ->update(['printed' => true]);

        return back()->with('success', 'Etiquetas marcadas como impresas.');
    }

    public function scanMode(Container $container)
    {
        // Cargamos solo lo necesario para el escaneo
        $container->load(['inspectionLabels' => function($query) {
            // Solo necesitamos las pendientes o con diferencia para escanear
            $query->whereIn('inspection_status', ['pendiente', 'con_diferencia']);
        }, 'inspectionLabels.containerItem']);

        return view('containers.scan', compact('container'));
    }
    public function scanBarcode(Request $request, Container $container)
    {
        // Validar que el contenedor esté abierto
        if ($container->status === 'cerrado') {
            return response()->json([
                'success' => false,
                'message' => 'El contenedor está cerrado, no se pueden registrar escaneos.',
            ], 422);
        }
    
        $request->validate([
            'barcode' => 'required|string|max:100',
        ]);
    
        $barcode = trim($request->barcode);
    
        // Buscar el item por barcode dentro de este contenedor
        $item = $container->items()->where('barcode', $barcode)->first();
    
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => "Barcode '{$barcode}' no encontrado en este contenedor.",
            ], 404);
        }
    
        // Incrementar cajas recibidas
        $item->increment('received_cartons');
        $item->refresh();
    
        return $this->cartonAdjustResponse($container, $item);
    }

    public function addCarton(Request $request, Container $container, ContainerItem $item)
    {
        if ($container->status === 'cerrado') {
            return response()->json(['success' => false, 'message' => 'Contenedor cerrado.'], 422);
        }
    
        $item->increment('received_cartons');
        $item->refresh();
    
        return $this->cartonAdjustResponse($container, $item);
    }
    
    /**
     * Remover 1 caja (botón - para corregir escaneo doble).
     */
        public function removeCarton(Request $request, Container $container, ContainerItem $item)
        {
            if ($container->status === 'cerrado') {
                return response()->json(['success' => false, 'message' => 'Contenedor cerrado.'], 422);
            }
        
            if ($item->received_cartons <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las cajas recibidas ya están en 0.',
                ], 422);
            }
        
            $item->decrement('received_cartons');
            $item->refresh();
        
            return $this->cartonAdjustResponse($container, $item);
        }

    private function cartonAdjustResponse(Container $container, ContainerItem $item): \Illuminate\Http\JsonResponse
    {
        // Recalcular valores derivados
        $receivedQty    = $item->received_cartons * $item->pieces_per_carton;
        $cartonDiff     = $item->carton_count - $item->received_cartons; // positivo = faltante
        $declaredQty    = $item->declared_qty;
    
        // Determinar estatus del item
        if ($item->status === 'no_recibido' && $item->received_cartons == 0) {
            $status = 'no_recibido'; // Preservar estado si no se está borrando la nota
        } elseif ($item->received_cartons === 0 && $item->carton_count > 0) {
            $status = 'pendiente';
        } elseif ($cartonDiff > 0) {
            $status = 'faltante';
        } elseif ($cartonDiff < 0) {
            $status = 'sobrante';
        } else {
            $status = 'conforme';
        }
    
        // Actualizar status en BD si difiere
        if ($item->status !== $status) {
            $item->update(['status' => $status]);
        }
    
        // Actualizar received_qty calculado si lo almacenas
        if ($item->received_qty !== $receivedQty) {
            $item->update(['received_qty' => $receivedQty]);
        }
    
        // Totales del contenedor (para KPIs)
        $container->load('items');
        $totalReceivedCartons = $container->items->sum('received_cartons');
        $totalReceivedQty     = $container->items->sum(fn ($i) => $i->received_cartons * $i->pieces_per_carton);
    
        // Actualizar totales en el contenedor si los almacenas
        $container->update([
            'received_qty' => $totalReceivedQty,
        ]);
    
        return response()->json([
            'success'                => true,
            'item_id'                => $item->id,
            'barcode'                => $item->barcode,
            'product_description'    => $item->product_description,
            'received_cartons'       => $item->received_cartons,
            'carton_count'           => $item->carton_count,
            'carton_difference'      => $cartonDiff,
            'received_qty'           => $receivedQty,
            'declared_qty'           => $declaredQty,
            'status'                 => $status,
            'total_received_cartons' => $totalReceivedCartons,
            'total_received_qty'     => $totalReceivedQty,
        ]);
    }

    public function markNotReceived(Request $request, Container $container, ContainerItem $item)
    {
        if ($container->status === 'cerrado') {
            return response()->json(['success' => false, 'message' => 'Contenedor cerrado.'], 422);
        }
    
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);
    
        $item->update([
            'received_cartons' => 0,
            'received_qty'     => 0,
            'status'           => 'no_recibido',
            'notes'            => $request->notes,
        ]);
    
        $item->refresh();
    
        // Recalcular totales del contenedor
        $container->load('items');
        $totalReceivedCartons = $container->items->sum('received_cartons');
        $totalReceivedQty     = $container->items->sum(fn ($i) => $i->received_cartons * $i->pieces_per_carton);
    
        $container->update(['received_qty' => $totalReceivedQty]);
    
        return response()->json([
            'success'                => true,
            'item_id'                => $item->id,
            'barcode'                => $item->barcode,
            'product_description'    => $item->product_description,
            'received_cartons'       => 0,
            'carton_count'           => $item->carton_count,
            'carton_difference'      => $item->carton_count, // todas faltantes
            'received_qty'           => 0,
            'declared_qty'           => $item->declared_qty,
            'status'                 => 'no_recibido',
            'total_received_cartons' => $totalReceivedCartons,
            'total_received_qty'     => $totalReceivedQty,
        ]);
    }

    /**
     * Establecer cantidad manual de cajas desde el input (AJAX).
     */
    public function setCartons(Request $request, Container $container, ContainerItem $item)
    {
        if ($container->status === 'cerrado') {
            return response()->json([
                'success' => false, 
                'message' => 'Contenedor cerrado.'
            ], 422);
        }

        $request->validate([
            'cartons' => 'required|integer|min:0',
        ]);
        $item->update([
            'received_cartons' => $request->cartons
        ]);

        return $this->cartonAdjustResponse($container, $item);
    }

    






}
