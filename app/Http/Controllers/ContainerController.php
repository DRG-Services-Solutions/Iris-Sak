<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\InspectionLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
            'container_number' => 'nullable|string|max:50',
            'supplier'         => 'nullable|string|max:255',
            'origin_country'   => 'nullable|string|max:100',
            'declared_qty'     => 'nullable|integer|min:0',
            'customs_status'   => 'required|in:pendiente,en_revision,liberado,retenido',
            'notes'            => 'nullable|string|max:1000',
            'packing_list'     => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        DB::beginTransaction();
        try {
            // Datos base — se pueden sobrescribir si el XLSX trae la info
            $containerData = [
                'container_number' => $validated['container_number'] ?? 'TEMP-' . now()->format('YmdHis'),
                'supplier'         => $validated['supplier'] ?? null,
                'origin_country'   => $validated['origin_country'] ?? null,
                'declared_qty'     => $validated['declared_qty'] ?? 0,
                'customs_status'   => $validated['customs_status'],
                'notes'            => $validated['notes'] ?? null,
                'received_by'      => Auth::id(),
                'received_at'      => now(),
                'status'           => 'abierto',
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

            // Evitar duplicado si el container_number ya existe
            if (Container::where('container_number', $containerData['container_number'])->exists()) {
                $containerData['container_number'] .= '-' . now()->format('His');
            }

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

            // Packing List No (H4)
            if (str_contains($colH, 'CONTAINER')) {
                $extracted = $this->extractAfterColon($colH);
                $data['container_number'] = preg_replace('/[^A-Za-z0-9]/', '', $extracted);
            }

            // Container (H11)
            if (str_contains($colH, 'CONTAINER')) {
                $data['container_number'] = $this->extractAfterColon($colH);
            }

            // Transport mode (H6)
            if (str_contains($colH, 'MODE OF TRANSPORT')) {
                $data['transport_mode'] = $this->extractAfterColon($colH);
            }

            // Port of loading (H8)
            if (str_contains($colH, 'PORT OF LOADING')) {
                $data['port_loading'] = $this->extractAfterColon($colH);
            }

            // Port of discharge (H9)
            if (str_contains($colH, 'PORT OF DISCHARGE')) {
                $data['port_discharge'] = $this->extractAfterColon($colH);
            }

            // ETD (H12)
            if (str_contains($colH, 'ETD')) {
                $etd = $this->extractAfterColon($colH);
                $data['etd'] = $this->parseDate($etd);
            }

            // ETA (H13)
            if (str_contains($colH, 'ETA')) {
                $eta = $this->extractAfterColon($colH);
                $data['eta'] = $this->parseDate($eta);
            }

            // Seller / Supplier (A7)
            if (str_contains(strtoupper($colA), 'SELLER')) {
                $data['supplier'] = $this->extractAfterColon($colA);
            }

            // Buyer (A4)
            if (str_contains(strtoupper($colA), 'BUYER')) {
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
        // Agregamos el modificador 'u' a la expresión regular para manejar bien el Unicode
        $parts = preg_split('/[:：]/u', $text, 2);
        $value = isset($parts[1]) ? $parts[1] : $text;

        // Limpieza profunda: Dejamos solo letras (\p{L}), números (\p{N}), 
        // espacios (\s) y signos de puntuación básicos (. - _ ,)
        // Esto destruye automáticamente los caracteres  invisibles
        $value = preg_replace('/[^\p{L}\p{N}\s\.\-\_,]/u', '', $value);

        return trim($value);
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
            'received_qty' => 'required|integer|min:0',
        ]);

        $item->update(['received_qty' => $validated['received_qty']]);
        $item->evaluateStatus();
        $item->container->recalculateFromItems();

        return back()->with('success', 'Cantidad recibida actualizada.');
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
        if (!$container->canClose()) {
            return back()->with('error', 'No se puede cerrar: hay artículos pendientes de revisar.');
        }

        $container->update(['status' => 'cerrado']);
        return back()->with('success', 'Contenedor cerrado exitosamente.');
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
}
