<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\WorkOrder;
use App\Models\ProductInstance;
use App\Models\ActivityLog;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Muestra el listado de conteos de inventario
     */
    public function index()
    {
        $this->authorize('viewAny', InventoryCount::class);
        $products = Product::withCount('instances')->paginate(10);
        $uniqueProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<', 20)->count();
        

        $barcodeProducts = Product::where('tracking_type', 'barcode')->count();

        $inventoryCounts = InventoryCount::with('user')
            ->latest('created_at')
            ->paginate(15);

        return view('inventory.index', compact('inventoryCounts', 'products', 'uniqueProducts', 'lowStockProducts', 'barcodeProducts'));
    }

    /**
     * Muestra el formulario para crear un nuevo conteo de inventario
     */
    public function create()
    {
        $this->authorize('create', InventoryCount::class);

        return view('inventory.create');
    }

    /**
     * Crea un nuevo conteo de inventario
     */
    public function store(Request $request)
    {
        $this->authorize('create', InventoryCount::class);

        $validated = $request->validate([
            'type' => 'required|in:general,ciclo,estacion',
            'station' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Generar folio único
            $folio = InventoryCount::generateNextFolio();

            // Crear el conteo de inventario
            $inventoryCount = InventoryCount::create([
                'user_id' => Auth::id(),
                'folio' => $folio,
                'type' => $validated['type'],
                'station' => $validated['station'] ?? null,
                'status' => 'en_proceso',
                'started_at' => Carbon::now(),
            ]);

            // Si es inventario por estación, calcular expected_count
            if ($validated['type'] === 'estacion' && $validated['station']) {
                $expectedCount = ProductInstance::where('current_station', $validated['station'])->count();
                $inventoryCount->update(['expected_count' => $expectedCount]);
            } elseif ($validated['type'] === 'general') {
                // Para inventario general, contar todos los items activos
                $expectedCount = ProductInstance::whereIn('status', ['Check-In', 'StandBy', 'En Proceso'])->count();
                $inventoryCount->update(['expected_count' => $expectedCount]);
            }

            // Registrar en el log de actividades
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'INVENTORY_COUNT_STARTED',
                'details' => [
                    'inventory_count_id' => $inventoryCount->id,
                    'folio' => $inventoryCount->folio,
                    'type' => $inventoryCount->type,
                    'station' => $inventoryCount->station,
                ]
            ]);

            DB::commit();

            return redirect()
                ->route('inventory.rfid-scan', $inventoryCount)
                ->with('success', "Conteo de inventario {$folio} iniciado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()
                ->withInput()
                ->with('error', 'Error al crear el conteo de inventario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra la interfaz de escaneo RFID para el conteo
     */
    public function showRfidScan(InventoryCount $inventoryCount)
    {
        $this->authorize('view', $inventoryCount);

        // Verificar que el conteo esté en proceso
        if ($inventoryCount->status !== 'en_proceso') {
            return redirect()
                ->route('inventory.show', $inventoryCount)
                ->with('error', 'Este conteo ya fue completado o cancelado.');
        }

        // Cargar instancias esperadas según el tipo de inventario
        $expectedInstances = $this->getExpectedInstances($inventoryCount);

        return view('inventory.rfid_scan', compact('inventoryCount', 'expectedInstances'));
    }

    /**
     * Procesa la verificación de items mediante RFID
     */
    public function verifyRfidItems(Request $request, InventoryCount $inventoryCount): JsonResponse
    {
        $this->authorize('view', $inventoryCount);

        // Validar EPCs detectados
        $validated = $request->validate([
            'detected_epcs' => 'present|array',
            'detected_epcs.*' => 'string|max:24',
        ]);

        $detectedEpcs = $validated['detected_epcs'] ?? [];

        // Obtener EPCs esperados según el tipo de inventario
        $expectedInstances = $this->getExpectedInstances($inventoryCount);
        $expectedEpcs = $expectedInstances->pluck('epc')->all();

        // Comparar listas
        $verifiedEpcs = array_values(array_intersect($expectedEpcs, $detectedEpcs));
        $missingEpcs = array_values(array_diff($expectedEpcs, $verifiedEpcs));
        $unexpectedEpcs = array_values(array_diff($detectedEpcs, $verifiedEpcs));

        // Obtener detalles de las instancias verificadas
        $verifiedInstances = ProductInstance::whereIn('epc', $verifiedEpcs)
            ->with('product')
            ->get()
            ->map(function ($instance) {
                return [
                    'epc' => $instance->epc,
                    'product_name' => $instance->product->name,
                    'status' => $instance->status,
                    'station' => $instance->current_station,
                ];
            });

        // Obtener detalles de las instancias faltantes
        $missingInstances = ProductInstance::whereIn('epc', $missingEpcs)
            ->with('product')
            ->get()
            ->map(function ($instance) {
                return [
                    'epc' => $instance->epc,
                    'product_name' => $instance->product->name,
                    'status' => $instance->status,
                    'station' => $instance->current_station,
                ];
            });

        // Verificar si hay items inesperados que existen en la BD
        $unexpectedInstances = ProductInstance::whereIn('epc', $unexpectedEpcs)
            ->with('product')
            ->get()
            ->map(function ($instance) {
                return [
                    'epc' => $instance->epc,
                    'product_name' => $instance->product->name,
                    'status' => $instance->status,
                    'station' => $instance->current_station,
                ];
            });

        $allVerified = empty($missingEpcs);

        return response()->json([
            'all_verified' => $allVerified,
            'verified_count' => count($verifiedEpcs),
            'missing_count' => count($missingEpcs),
            'unexpected_count' => count($unexpectedEpcs),
            'verified_instances' => $verifiedInstances,
            'missing_instances' => $missingInstances,
            'unexpected_instances' => $unexpectedInstances,
            'verified_epcs' => $verifiedEpcs,
            'missing_epcs' => $missingEpcs,
            'unexpected_epcs' => $unexpectedEpcs,
        ]);
    }

    /**
     * Completa el conteo de inventario
     */
    public function complete(Request $request, InventoryCount $inventoryCount)
    {
        $this->authorize('update', $inventoryCount);

        $validated = $request->validate([
            'detected_epcs' => 'required|array',
            'detected_epcs.*' => 'string|max:24',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $detectedEpcs = $validated['detected_epcs'];
            $expectedInstances = $this->getExpectedInstances($inventoryCount);
            $expectedEpcs = $expectedInstances->pluck('epc')->all();

            // Calcular discrepancias
            $verifiedEpcs = array_intersect($expectedEpcs, $detectedEpcs);
            $missingEpcs = array_diff($expectedEpcs, $verifiedEpcs);
            $unexpectedEpcs = array_diff($detectedEpcs, $verifiedEpcs);

            $discrepancyCount = count($missingEpcs) + count($unexpectedEpcs);

            // Actualizar el conteo de inventario
            $inventoryCount->update([
                'status' => 'completado',
                'found_count' => count($detectedEpcs),
                'discrepancy_count' => $discrepancyCount,
                'detected_epcs' => $detectedEpcs,
                'notes' => $validated['notes'] ?? $inventoryCount->notes,
                'completed_at' => Carbon::now(),
            ]);

            // Registrar en el log de actividades
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'INVENTORY_COUNT_COMPLETED',
                'details' => [
                    'inventory_count_id' => $inventoryCount->id,
                    'folio' => $inventoryCount->folio,
                    'expected_count' => $inventoryCount->expected_count,
                    'found_count' => count($detectedEpcs),
                    'discrepancy_count' => $discrepancyCount,
                    'missing_epcs' => array_values($missingEpcs),
                    'unexpected_epcs' => array_values($unexpectedEpcs),
                ]
            ]);

            DB::commit();

            return redirect()
                ->route('inventory.show', $inventoryCount)
                ->with('success', 'Conteo de inventario completado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()
                ->with('error', 'Error al completar el conteo: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles y resultados de un conteo de inventario
     */
    public function show(InventoryCount $inventoryCount)
    {
        $this->authorize('view', $inventoryCount);

        $inventoryCount->load('user');

        // Si está completado, obtener detalles de las discrepancias
        $verifiedInstances = null;
        $missingInstances = null;
        $unexpectedInstances = null;

        if ($inventoryCount->status === 'completado' && $inventoryCount->detected_epcs) {
            $expectedInstances = $this->getExpectedInstances($inventoryCount);
            $expectedEpcs = $expectedInstances->pluck('epc')->all();
            $detectedEpcs = $inventoryCount->detected_epcs;

            $verifiedEpcs = array_intersect($expectedEpcs, $detectedEpcs);
            $missingEpcs = array_diff($expectedEpcs, $verifiedEpcs);
            $unexpectedEpcs = array_diff($detectedEpcs, $verifiedEpcs);

            $verifiedInstances = ProductInstance::whereIn('epc', $verifiedEpcs)->with('product')->get();
            $missingInstances = ProductInstance::whereIn('epc', $missingEpcs)->with('product')->get();
            $unexpectedInstances = ProductInstance::whereIn('epc', $unexpectedEpcs)->with('product')->get();
        }

        return view('inventory.show', compact(
            'inventoryCount',
            'verifiedInstances',
            'missingInstances',
            'unexpectedInstances'
        ));
    }

    /**
     * Cancela un conteo de inventario
     */
    public function cancel(InventoryCount $inventoryCount)
    {
        $this->authorize('update', $inventoryCount);

        if ($inventoryCount->status !== 'en_proceso') {
            return back()->with('error', 'Solo se pueden cancelar conteos en proceso.');
        }

        DB::beginTransaction();
        try {
            $inventoryCount->update([
                'status' => 'cancelado',
                'completed_at' => Carbon::now(),
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'INVENTORY_COUNT_CANCELLED',
                'details' => [
                    'inventory_count_id' => $inventoryCount->id,
                    'folio' => $inventoryCount->folio,
                ]
            ]);

            DB::commit();

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Conteo de inventario cancelado.');

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Error al cancelar el conteo: ' . $e->getMessage());
        }
    }

    /**
     * Método auxiliar para obtener las instancias esperadas según el tipo de inventario
     */
    private function getExpectedInstances(InventoryCount $inventoryCount)
    {
        $query = ProductInstance::with('product');

        switch ($inventoryCount->type) {
            case 'estacion':
                if ($inventoryCount->station) {
                    $query->where('current_station', $inventoryCount->station);
                }
                break;

            case 'general':
                // Inventario general: todos los items activos
                $query->whereIn('status', ['Check-In', 'StandBy', 'En Proceso']);
                break;

            case 'ciclo':
                // Para conteo cíclico, podrías implementar lógica adicional
                // Por ahora, usar el mismo criterio que general
                $query->whereIn('status', ['Check-In', 'StandBy', 'En Proceso']);
                break;
        }

        return $query->get();
    }

    /**
     * Método existente - mantener compatibilidad
     */
    public function startWorkOrderInventory(WorkOrder $workOrder)
    {
        // Autorizar si el usuario puede inventariar esta orden
        // $this->authorize('inventory', $workOrder);

        // Cargar instancias y sus productos
        $workOrder->load(['productInstances.product', 'user']);

        // Esta vista será la que pregunte al usuario CÓMO quiere inventariar.
        return view('inventory.select_method', compact('workOrder'));
    }
}
