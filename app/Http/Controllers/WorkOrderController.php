<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Product;
use App\Models\PrintJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use App\Models\ProductInstance;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class WorkOrderController extends Controller
{
        public function showHistory(WorkOrder $workOrder) 
        {
            $this->authorize('view', $workOrder);

            $instanceIds = $workOrder->productInstances()->pluck('id');

          
            $activityLogs = ActivityLog::with(['user', 'productInstance.product']) 
                            ->where('work_order_id', $workOrder->id)
                            ->orWhereIn('product_instance_id', $instanceIds)
                            ->latest() 
                            ->paginate(25); 

            // 4. Pasar la orden y los logs a la nueva vista
            return view('work_orders.history', compact('workOrder', 'activityLogs'));
        }

        public function releaseOrder(WorkOrder $workOrder): RedirectResponse
        {
            $this->authorize('release', $workOrder);
            if ($workOrder->status === 'Enviado' || $workOrder->completed_at !== null) {
                return redirect()->route('dashboard')->with('warning', "La Orden {$workOrder->folio} ya fue enviada anteriormente.");
            }

            try {
                DB::transaction(function () use ($workOrder) {

                    $finalStatus = 'Enviado'; 
                    $finalProcess = 'Embarcado'; 
                    $finalStation = 'SALIDA'; 

                   
                    $workOrder->productInstances()->update(['status' => $finalStatus]);
                   

                    $workOrder->update([
                        'status' => $finalStatus,
                        'process' => $finalProcess,
                        'station' => $finalStation,
                        'completed_at' => now(), 
                    ]);

                    ActivityLog::create([
                        'user_id' => Auth::id(),
                        'product_instance_id' => null, 
                        'work_order_id' => $workOrder->id,
                        'action' => 'ORDER_RELEASED', 
                        'details' => [
                            'final_status' => $finalStatus,
                            'final_process' => $finalProcess,
                            'final_station' => $finalStation,
                            'instance_count' => $workOrder->productInstances()->count()
                        ]
                    ]);
                });

                return redirect()->route('dashboard') 
                                ->with('success', "Orden {$workOrder->folio} marcada como '{$workOrder->status}' exitosamente.");

            } catch (\Exception $e) {
                report($e); 
                
                return redirect()->route('dashboard')
                                ->with('error', 'Error al intentar liberar la orden. Intente de nuevo.');
            }
        }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', WorkOrder::class);

        return view('work_orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
       
        $validated = $request->validate([]);

        
        $lastOrder = WorkOrder::latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $folio = 'SCAN-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        $userId = Auth::id(); 
        $startTime = now();
        $initialStatus = 'Pendiente Escaneo'; 

        $newOrder = WorkOrder::create([
            'folio' => $folio,
            'user_id' => $userId,
            'process' => 'Impresion/Escaneo',
            'station' => '01',
            'status' => $initialStatus,
            'started_at' => $startTime,
        ]);

       
        return redirect()->route('work_orders.scanning', $newOrder) 
                         ->with('success', "Orden de Trabajo {$newOrder->folio} creada. Comience a escanear.");
    }

    /**
     * Muestra la pantalla de escaneo para una orden de trabajo.
     * (Método vacío por ahora, lo implementaremos después)
     */

     public function showScanningScreen(WorkOrder $workOrder)
     {
        $this->authorize('addInstance', $workOrder);

       
            $instances = $workOrder->productInstances() 
            ->with('product')       
            ->latest()              
            ->get();                

        return view('work_orders.scanning', compact('workOrder', 'instances'));

     }


     /**
 * Process a scanned barcode for a given work order.
 * Creates a ProductInstance and returns JSON response.
 */
public function processScan(Request $request, WorkOrder $workOrder): JsonResponse
{
    $this->authorize('addInstance', $workOrder);
    
    $validated = $request->validate([
        'barcode' => [
            'required',
            'string',
            Rule::exists('products', 'barcode'), 
        ],
    ]);

    try {
        $product = Product::where('barcode', $validated['barcode'])->firstOrFail();

        $instancesInOrder = $workOrder->productInstances()
            ->where('product_id', $product->id)
            ->count();

        $stockDisponible = $product->getRawOriginal('stock');
        
        // Validación de barrera de seguridad
        if ($instancesInOrder >= $stockDisponible) {
            return response()->json([
                'success' => false,
                'message' => "Límite alcanzado: el producto '{$product->name}' tiene {$stockDisponible} piezas disponibles y ya se escanearon {$instancesInOrder} en esta orden."
            ], 422);
        }

        // Creación de la instancia
        $instance = ProductInstance::create([
            'product_id' => $product->id,
            'work_order_id' => $workOrder->id, 
            'status' => 'Check-In',
            'current_station' => $workOrder->station,
            'user_id' => Auth::id(),
        ]);

        // Registro de actividad
        ActivityLog::create([
            'user_id' => Auth::id(), 
            'product_instance_id' => $instance->id, 
            'work_order_id' => $workOrder->id, 
            'action' => 'INSTANCE_CREATED_VIA_SCAN', 
            'details' => [ 
                'epc' => $instance->epc, 
                'initial_status' => $instance->status,
                'initial_station' => $instance->current_station,
                'product_id' => $instance->product_id,
                'product_name' => $product->name 
            ]
        ]);

        // Preparar ZPL
        $productname = $product->name; 
        $epc = $instance->epc;
        $ean13 = $product->barcode;
        $zplCommands = "
        ^XA
        ^PW812
        ^LL0406
        ^LH0,0
        ^MMT
        ^CI0
        ^RFW,H^FD{$epc}^FS
        ^FO50,50^A0N,35,35^FB712,2,0,C,0^FH\^FD{$productname}^FS
        ^FO50,130^A0N,28,28^FB712,1,0,C,0^FH\^FDEPC: {$epc}^FS
        ^FO20,200^BQN,2,5^FH\^FDLA,{$epc}^FS
        ^BY2,2,100^FT580,375^BEN,,Y,N^FD{$ean13}^FS
        ^PQ1,0,1,N
        ^XZ
        ";
        
        $printerIp = '10.20.1.227';

        // Guardar el trabajo en la cola de impresión de la base de datos
        PrintJob::create([
            'work_order_id' => $workOrder->id,
            'printer_ip' => $printerIp,
            'zpl_data' => trim($zplCommands), // Aplicamos trim para evitar saltos de línea basura al inicio
            'status' => 'pending'
        ]);
       
        return response()->json([
            'success' => true,
            'message' => 'Instancia creada. Etiqueta enviada a la cola de impresión.', 
            'instance' => $instance->load('product'),
            'current_count' => $instancesInOrder + 1,
            'stock_limit' => $stockDisponible
        ]);

    } catch (\Exception $e) {
        report($e); 
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar el escaneo: ' . $e->getMessage() 
        ], 500); 
    }
}

private function sendZplToPrinter(string $zplData, string $printerIp, int $printerPort = 9100): bool
    {
        try {
            $socket = fsockopen("tcp://" . $printerIp, $printerPort, $errno, $errstr, 10); 

            if (!$socket) {
                Log::error("ZPL Print Error: No se pudo conectar a la impresora $printerIp:$printerPort - $errno: $errstr");
                return false;
            }

            fwrite($socket, $zplData);
            fclose($socket);
            Log::info("ZPL enviado exitosamente a $printerIp:$printerPort");
            return true;

        } catch (\Exception $e) {
            Log::error("ZPL Print Exception: " . $e->getMessage());
            return false;
        }
    }


public function finalizeOrder(WorkOrder $workOrder): RedirectResponse
{
    $newStatus = 'StandBy';
    $newProcess = 'Almacenaje/StandBy';
    $newStation = '02';

    if ($workOrder->station === $newStation && $workOrder->status === $newStatus) {
        return redirect()->route('work_orders.scanning', $workOrder)->with('warning', 'La orden ya se encuentra en este estado/estación.');
   }
    if ($workOrder->status === 'Enviado' || $workOrder->completed_at !== null) {
        return redirect()->route('work_orders.scanning', $workOrder)->with('error', 'La orden ya ha sido enviada y no puede modificarse.');
    }
    try {
        DB::transaction(function () use ($workOrder, $newStatus, $newProcess, $newStation) { 

            $workOrder->update([
                'status' => $newStatus,
                'process' => $newProcess,
                'station' => $newStation,
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'product_instance_id' => null,
                'work_order_id' => $workOrder->id,
                'action' => 'ORDER_STEP_FINALIZED', 
                'details' => [
                    'new_status' => $newStatus,     
                    'new_process' => $newProcess,   
                    'new_station' => $newStation,  
                    'instance_count' => $workOrder->productInstances()->count() 
                ]
            ]);
        }); 

        return redirect()->route('work_orders.index')
                         ->with('success', "Orden {$workOrder->folio} finalizada en esta estación y movida a {$newProcess} ({$newStation}).");

    } catch (\Exception $e) {
        report($e);
        return redirect()->route('dashboard')
                         ->with('error', 'Error al finalizar la orden. Intente de nuevo.');
    }
}

        public function index()
        {
            $workOrders = WorkOrder::with('user') 
                                ->latest()      
                                ->paginate(15); 

            return view('work_orders.index', compact('workOrders'));
        }

        public function show(WorkOrder $workOrder) 
        {
            $this->authorize('view', $workOrder);

            $workOrder->load(['productInstances.product', 'user']);

            return view('work_orders.show', compact('workOrder'));
        }


        /**
         * Verify detected RFID EPCs against expected instances for the work order.
                */
        public function verifyRfidItems(Request $request, WorkOrder $workOrder): JsonResponse
        {
            $this->authorize('view', $workOrder);

            $validated = $request->validate([
                'detected_epcs'   => 'present|array',
                'detected_epcs.*' => 'string|max:24', 
            ]);

            $detectedEpcs = $validated['detected_epcs'] ?? []; 

            $expectedEpcs = $workOrder->productInstances()->pluck('epc')->all();

            $verifiedEpcs = array_values(array_intersect($expectedEpcs, $detectedEpcs));

            $missingEpcs = array_values(array_diff($expectedEpcs, $verifiedEpcs)); 
            $unexpectedEpcs = array_values(array_diff($detectedEpcs, $verifiedEpcs)); 

            $allVerified = empty($missingEpcs); 

            return response()->json([
                'all_verified' => $allVerified,     
                'verified_epcs' => $verifiedEpcs,   
                'missing_epcs' => $missingEpcs,     
                'unexpected_epcs' => $unexpectedEpcs 
            ]);
        }

        public function listShippedOrdersForAudit()
{
    if (!Auth::user()->isAdmin()) { 
        abort(403, 'Acción no autorizada.');
    }

    $ordersToAudit = WorkOrder::where('status', 'Enviado') 
                            ->where('is_audited', false) 
                            ->with('user')               
                            ->latest('updated_at')      
                            ->paginate(15);

    return view('audit.list_orders', compact('ordersToAudit'));
}

/**
 * Muestra la pantalla específica para la auditoría RFID de una orden.
 * Solo accesible por administradores, para órdenes 'Enviado' y no auditadas.
 */
public function showRfidAuditScreen(WorkOrder $workOrder)
{
    if (!Auth::user()->isAdmin() || $workOrder->status !== 'Enviado' || $workOrder->is_audited) {
        return redirect()->route('audit.work_orders.list_shipped') 
                         ->with('error', 'Esta orden no está lista o ya fue auditada.');
    }

    $workOrder->load(['productInstances.product', 'user']); 

    return view('audit.rfid_screen', compact('workOrder'));
}

/**
 * Procesa los EPCs detectados por RFID durante la auditoría.
 * Similar al verifyRfidItems, pero en el contexto de auditoría.
 * Solo accesible por administradores, para órdenes 'Enviado' y no auditadas.
 */
public function verifyRfidAuditItems(Request $request, WorkOrder $workOrder): JsonResponse
{
    if (!Auth::user()->isAdmin() || $workOrder->status !== 'Enviado' || $workOrder->is_audited) {
        return response()->json(['success' => false, 'message' => 'La orden no puede ser auditada en este estado.'], 403);
    }

    $validated = $request->validate([
        'detected_epcs'   => 'present|array',
        'detected_epcs.*' => 'string|max:24',
    ]);

    $detectedEpcs = $validated['detected_epcs'] ?? [];
    $expectedEpcs = $workOrder->productInstances()->pluck('epc')->all();

    $verifiedEpcs = array_values(array_intersect($expectedEpcs, $detectedEpcs));
    $missingEpcs = array_values(array_diff($expectedEpcs, $verifiedEpcs));
    $unexpectedEpcs = array_values(array_diff($detectedEpcs, $expectedEpcs)); 

    $allExpectedItemsFound = empty($missingEpcs);

    return response()->json([
        'all_expected_items_found' => $allExpectedItemsFound,
        'verified_epcs' => $verifiedEpcs,
        'missing_epcs' => $missingEpcs,
        'unexpected_epcs' => $unexpectedEpcs, 
        'expected_count' => count($expectedEpcs),
        'detected_count' => count($detectedEpcs),
        'all_verified_strict' => empty($missingEpcs) && empty($unexpectedEpcs) && (count($expectedEpcs) === count($verifiedEpcs))
    ]);
}
    
/**
 * Marca una orden como auditada y registra el evento.
 * (Aquí iría la lógica de generación de reporte en el futuro).
 * Solo accesible por administradores, para órdenes 'Enviado' y no auditadas,
 * y probablemente después de una verificación RFID exitosa.
 */
public function completeAudit(Request $request, WorkOrder $workOrder): RedirectResponse
{
     if (!Auth::user()->isAdmin() || $workOrder->status !== 'Enviado' || $workOrder->is_audited) {
         return redirect()->route('audit.work_orders.list_shipped')
                          ->with('error', 'No se pudo completar la auditoría. Estado de orden incorrecto.');
     }


    try {
        DB::transaction(function () use ($workOrder) {
            $workOrder->update(['is_audited' => true]); 

                ActivityLog::create([
                'user_id' => Auth::id(),
                'work_order_id' => $workOrder->id,
                'action' => 'ORDER_AUDIT_COMPLETED', 
                'details' => [
                    'audited_by' => Auth::user()->name,
                    'result' => 'Auditoría RFID completada satisfactoriamente.',
                ]
            ]);
        });

         return redirect()->route('audit.work_orders.list') 
                          ->with('success', "Auditoría de Orden {$workOrder->folio} completada exitosamente.");
    } catch (\Exception $e) {
        report($e);
        return redirect()->route('audit.work_orders.rfid_screen', $workOrder)
                         ->with('error', 'Error al procesar la finalización de la auditoría. Intente de nuevo.');
    }
}
}
