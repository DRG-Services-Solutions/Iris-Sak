<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Product;
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
        public function showHistory(WorkOrder $workOrder) // Recibe la orden por Route Model Binding
        {
            // 1. Autorizar si el usuario puede ver esta orden en primer lugar
            $this->authorize('view', $workOrder);

            // 2. Obtener los IDs de todas las instancias asociadas a esta orden
            $instanceIds = $workOrder->productInstances()->pluck('id');
            // pluck('id') crea un array solo con los IDs: [1, 5, 12, ...]

            // 3. Buscar en ActivityLog:
            //    - Entradas donde work_order_id sea el de esta orden
            //    - O entradas donde product_instance_id esté en la lista de IDs de instancias de esta orden
            //    - Cargar relaciones útiles para mostrar info (usuario, instancia, producto de la instancia)
            //    - Ordenar por más reciente primero
            //    - Paginar resultados
            $activityLogs = ActivityLog::with(['user', 'productInstance.product']) // Carga ansiosa
                            ->where('work_order_id', $workOrder->id)
                            ->orWhereIn('product_instance_id', $instanceIds)
                            ->latest() // Ordena por created_at DESC
                            ->paginate(25); // Muestra 25 logs por página

            // 4. Pasar la orden y los logs a la nueva vista
            return view('work_orders.history', compact('workOrder', 'activityLogs'));
        }

        public function releaseOrder(WorkOrder $workOrder): RedirectResponse
        {
            $this->authorize('release', $workOrder);
            // --- Verificación Opcional (Prevenir doble liberación) ---
            if ($workOrder->status === 'Enviado' || $workOrder->completed_at !== null) {
                return redirect()->route('dashboard')->with('warning', "La Orden {$workOrder->folio} ya fue enviada anteriormente.");
            }

            // --- Usar una transacción por si algo falla ---
            try {
                DB::transaction(function () use ($workOrder) {

                    $finalStatus = 'Enviado'; // O 'Completado', 'Shipped', etc.
                    $finalProcess = 'Embarcado'; // O simplemente mantener el último?
                    $finalStation = 'SALIDA'; // O 'Embarque', 'Cliente'

                    // 1. (Opcional pero recomendado) Actualizar estado de las instancias asociadas
                    // Asumimos que todas las instancias de esta orden también se marcan como 'Enviado'
                    $workOrder->productInstances()->update(['status' => $finalStatus]);
                    // Nota: Esto no dispara eventos individuales en los modelos ProductInstance.
                    // Si necesitáramos logs individuales por instancia aquí, habría que iterar y guardar.

                    // 2. Actualizar la Orden de Trabajo
                    $workOrder->update([
                        'status' => $finalStatus,
                        'process' => $finalProcess,
                        'station' => $finalStation,
                        'completed_at' => now(), // Marcamos la hora de finalización
                    ]);

                    // 3. Registrar la acción en el Log
                    ActivityLog::create([
                        'user_id' => Auth::id(),
                        'product_instance_id' => null, // Acción sobre la orden
                        'work_order_id' => $workOrder->id,
                        'action' => 'ORDER_RELEASED', // O 'ORDER_SHIPPED', 'ORDER_COMPLETED'
                        'details' => [
                            'final_status' => $finalStatus,
                            'final_process' => $finalProcess,
                            'final_station' => $finalStation,
                            'instance_count' => $workOrder->productInstances()->count() // Contar cuántas instancias se asociaron
                        ]
                    ]);
                });

                // 4. Redirección si todo fue bien
                return redirect()->route('dashboard') // Redirigir al dashboard o a una lista de órdenes
                                ->with('success', "Orden {$workOrder->folio} marcada como '{$workOrder->status}' exitosamente.");

            } catch (\Exception $e) {
                report($e); // Loguea el error real para depuración
                // 5. Redirección si algo falló
                // Idealmente, redirigir a una página donde estaba el botón,
                // pero como no la tenemos, volvemos al dashboard con error.
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

        // Solo muestra la vista del formulario
        return view('work_orders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
       
        $validated = $request->validate([]);

        // 2. Preparar datos adicionales para la nueva orden
        // Generación simple de Folio (ej. WO-00001)
        // Intenta obtener el último ID de forma segura o usa 0 si no hay órdenes.
        $lastOrder = WorkOrder::latest('id')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $folio = 'WO-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        $userId = Auth::id(); // ID del usuario logueado
        $startTime = now(); // Fecha y hora actual
        $initialStatus = 'Pendiente Escaneo'; // Estado inicial

        // 3. Crear la Orden de Trabajo en la BD
        $newOrder = WorkOrder::create([
            'folio' => $folio,
            'user_id' => $userId,
            'process' => 'Seleccion/Picking',
            'station' => '01',
            'status' => $initialStatus,
            'started_at' => $startTime,
        ]);

        // 4. Redirigir a la pantalla de escaneo (pasando el objeto WorkOrder o su ID)
        // ¡IMPORTANTE! Esta ruta 'work_orders.scanning' aún no existe.
        // La crearemos en el siguiente paso. Laravel dará error si intentas
        // crear una orden ahora mismo hasta que definamos esa ruta y su método.
        return redirect()->route('work_orders.scanning', $newOrder) // Pasamos el objeto entero, Laravel extraerá el ID para la URL
                         ->with('success', "Orden de Trabajo {$newOrder->folio} creada. Comience a escanear.");
    }

    /**
     * Muestra la pantalla de escaneo para una orden de trabajo.
     * (Método vacío por ahora, lo implementaremos después)
     */

     public function showScanningScreen(WorkOrder $workOrder)
     {
        $this->authorize('addInstance', $workOrder);

        // Cargamos la orden Y TAMBIÉN sus instancias relacionadas
        // Usamos with('product') para cargar también la información del producto (catálogo)
        // y evitar consultas N+1 en la vista. Usamos latest() para ordenarlas.
            $instances = $workOrder->productInstances() // Usa la relación que definimos
            ->with('product')       // Carga ansiosa de la relación 'product'
            ->latest()              // Ordena por fecha de creación descendente
            ->get();                // Obtiene la colección

        // Pasamos ambas variables a la vista
        return view('work_orders.scanning', compact('workOrder', 'instances'));

     }


     /**
 * Process a scanned barcode for a given work order.
 * Creates a ProductInstance and returns JSON response.
 */
public function processScan(Request $request, WorkOrder $workOrder): JsonResponse
{
    $this->authorize('addInstance', $workOrder);
    // 1. Validar la entrada (el código de barras)
    $validated = $request->validate([
        'barcode' => [
            'required',
            'string',
            Rule::exists('products', 'barcode'), // Asegura que el barcode exista en la tabla products
        ],
    ]);

    try {
        // 2. Encontrar el Producto (tipo de herramienta) por su barcode
        // Usamos firstOrFail para que lance una excepción si no lo encuentra (aunque 'exists' ya lo valida)
        $product = Product::where('barcode', $validated['barcode'])->firstOrFail();

        // 3. Crear la Instancia del Producto (ProductInstance)
        // El EPC se genera automáticamente en el evento 'creating' del modelo ProductInstance
        $instance = ProductInstance::create([
            'product_id' => $product->id,
            'work_order_id' => $workOrder->id, // <-- Añadir esta línea
            'status' => 'Check-In',
            'current_station' => $workOrder->station,
            'user_id' => Auth::id(),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(), // El usuario que realizó la acción (escaneo)
            'product_instance_id' => $instance->id, // El ID de la instancia recién creada
            'work_order_id' => $workOrder->id, // El ID de la orden de trabajo actual
            'action' => 'INSTANCE_CREATED_VIA_SCAN', // Acción descriptiva
            'details' => [ // Guardamos contexto útil como JSON
                'epc' => $instance->epc, // El EPC que se autogeneró
                'initial_status' => $instance->status,
                'initial_station' => $instance->current_station,
                'product_id' => $instance->product_id,
                'product_name' => $product->name // Nombre del producto (para conveniencia)
            ]
        ]);

        //$modelo = $product->model_name ?? 'N/A'; // asumiendo que se tiene un campo 'model_name' en Product, o ajústalo
        //$numeroDeSerieParaEtiqueta = $instance->serial_number_for_label ?? $instance->epc; // Decide qué mostrar como S/N
        $productname = $product->name; // Asumiendo que $product ya fue obtenido
        $epc = $instance->epc;
        $zplCommands = "
        ^XA
        ^PW812
        ^LL0406
        ^LH0,0
        ^MMT
        ^RFW,H^FD{$epc}^FS
        ^FO50,50^A0N,35,35^FB712,2,0,C,0^FH\^FD{$productname}^FS
        ^FO50,130^A0N,28,28^FB712,1,0,C,0^FH\^FDEPC: {$epc}^FS
        ^FO330,180^BQN,2,6^FH\^FDLA,{$epc}^FS
        ^PQ1,0,1,N
        ^XZ
        ";
        // Ajusta la IP y el puerto si son diferentes a los que usabas en C#
        $printerIp = '192.168.0.199';
        $printerPort = 9100;
        $printSuccess = $this->sendZplToPrinter($zplCommands, $printerIp, $printerPort);

        if (!$printSuccess) {
            Log::warning("Falló la impresión/codificación ZPL para EPC: {$epc} en orden {$workOrder->folio}");
        }
        // 4. (Opcional por ahora) Asociar la instancia a la orden de trabajo
        // Esto podría hacerse aquí o al finalizar. Podría ser una relación ManyToMany.
        // $workOrder->productInstances()->attach($instance->id); // Ejemplo si tuvieras la relación

        // 5. Devolver una respuesta JSON exitosa con los datos de la instancia creada
        // Usamos load('product') para incluir también los datos del producto (ej. nombre) en la respuesta
        return response()->json([
            'success' => true,
            'message' => 'Instancia creada e impresión ZPL enviada.', // Mensaje actualizado
            'instance' => $instance->load('product') // Carga la relación para tener datos del producto
        ]);

    } catch (\Exception $e) {
        // Manejo básico de errores: devolver un JSON de error
        // En producción, querrías loguear el error $e->getMessage()
        report($e); // Informa el error al log de Laravel
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar el escaneo: ' . $e->getMessage() // O un mensaje más genérico
        ], 500); // Código de error de servidor
    }
}

private function sendZplToPrinter(string $zplData, string $printerIp, int $printerPort = 9100): bool
    {
        try {
            $socket = fsockopen("tcp://" . $printerIp, $printerPort, $errno, $errstr, 10); // 10 segundos timeout

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
    // Actualizamos la orden de trabajo a la siguiente etapa definida
    try {
        // Usar una transacción
        DB::transaction(function () use ($workOrder, $newStatus, $newProcess, $newStation) { // Pasamos las variables

            // Actualizamos la orden de trabajo a la siguiente etapa definida
            $workOrder->update([
                'status' => $newStatus,
                'process' => $newProcess,
                'station' => $newStation,
                // No ponemos completed_at aquí
            ]);

            // Registrar la acción en el Log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'product_instance_id' => null,
                'work_order_id' => $workOrder->id,
                'action' => 'ORDER_STEP_FINALIZED', // O SCANNING_COMPLETED
                'details' => [
                    'new_status' => $newStatus,     // <-- Ahora la variable existe
                    'new_process' => $newProcess,   // <-- Ahora la variable existe
                    'new_station' => $newStation,   // <-- Ahora la variable existe
                    'instance_count' => $workOrder->productInstances()->count() // Asume relación definida
                ]
            ]);
        }); // Fin transacción

        // Redirigir (usamos las variables también para el mensaje)
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
            // Obtenemos las órdenes, paginadas, y cargamos la relación con el usuario.
            // Ordenamos por la más reciente primero.
            $workOrders = WorkOrder::with('user') // Carga la relación 'user' para evitar N+1
                                ->latest()      // Ordena por created_at descendente
                                ->paginate(15); // Muestra 15 por página (o usa ->get() para todas)

            return view('work_orders.index', compact('workOrders'));
        }

        public function show(WorkOrder $workOrder) // Route Model Binding
        {
            $this->authorize('view', $workOrder);

            // Cargamos las instancias asociadas y, para cada instancia, su producto relacionado
            $workOrder->load(['productInstances.product', 'user']);
            // Alternativa para cargar instancias:
            // $instances = $workOrder->productInstances()->with('product')->get();

            return view('work_orders.show', compact('workOrder'));
            // Si usaste la alternativa: return view('work_orders.show', compact('workOrder', 'instances'));
        }


        /**
         * Verify detected RFID EPCs against expected instances for the work order.
                */
        public function verifyRfidItems(Request $request, WorkOrder $workOrder): JsonResponse
        {
            // 1. Autorización (¿Puede el usuario realizar esta acción en esta orden?)
            // Usamos 'view' como ejemplo, podrías crear un permiso 'verifyItems' en la policy si quieres más granularidad
            $this->authorize('view', $workOrder);

            // 2. Validar la entrada (esperamos un array de EPCs detectados)
            $validated = $request->validate([
                // 'detected_epcs' debe ser un array, y cada elemento (si los hay) debe ser un string
                'detected_epcs'   => 'present|array',
                'detected_epcs.*' => 'string|max:24', // Asumiendo max 24 chars para EPC
            ]);

            $detectedEpcs = $validated['detected_epcs'] ?? []; // Obtener EPCs detectados (o array vacío si no se envió)

            // 3. Obtener los EPCs esperados para esta orden desde la BD
            // Usamos pluck() para obtener solo un array de strings con los EPCs
            $expectedEpcs = $workOrder->productInstances()->pluck('epc')->all();

            // 4. Comparar las listas
            // array_intersect: Devuelve los elementos presentes en AMBAS listas (los verificados)
            $verifiedEpcs = array_values(array_intersect($expectedEpcs, $detectedEpcs));

            // array_diff: Devuelve los elementos de la primera lista que NO están en la segunda
            $missingEpcs = array_values(array_diff($expectedEpcs, $verifiedEpcs)); // Esperados que no se verificaron
            $unexpectedEpcs = array_values(array_diff($detectedEpcs, $verifiedEpcs)); // Detectados que no se esperaban

            // 5. Determinar si todos los esperados fueron verificados
            $allVerified = empty($missingEpcs); // Si no falta ninguno, todos fueron verificados

            // 6. Devolver respuesta JSON detallada
            return response()->json([
                'all_verified' => $allVerified,     // true si todos los esperados se encontraron
                'verified_epcs' => $verifiedEpcs,   // Lista de EPCs que sí coincidieron
                'missing_epcs' => $missingEpcs,     // Lista de EPCs esperados que faltaron en la lectura
                'unexpected_epcs' => $unexpectedEpcs // Lista de EPCs leídos que no pertenecen a esta orden
            ]);
        }

        public function listShippedOrdersForAudit()
{
    if (!Auth::user()->isAdmin()) { // Placeholder para autorización, idealmente una Policy/Gate
        abort(403, 'Acción no autorizada.');
    }

    $ordersToAudit = WorkOrder::where('status', 'Enviado')  // Solo órdenes ya liberadas/enviadas
                            ->where('is_audited', false) // Que no hayan sido auditadas aún
                            ->with('user')               // Cargar usuario para mostrar nombre
                            ->latest('updated_at')       // Las más recientemente 'Enviadas' primero
                            ->paginate(15);

    // Devolveremos a una nueva vista en una subcarpeta 'audit'
    return view('audit.list_orders', compact('ordersToAudit'));
}

/**
 * Muestra la pantalla específica para la auditoría RFID de una orden.
 * Solo accesible por administradores, para órdenes 'Enviado' y no auditadas.
 */
public function showRfidAuditScreen(WorkOrder $workOrder)
{
    if (!Auth::user()->isAdmin() || $workOrder->status !== 'Enviado' || $workOrder->is_audited) {
        return redirect()->route('audit.work_orders.list_shipped') // Volver a la lista si no es válido
                         ->with('error', 'Esta orden no está lista o ya fue auditada.');
    }

    $workOrder->load(['productInstances.product', 'user']); // Cargar datos necesarios para la vista

    // Devolveremos a una nueva vista en una subcarpeta 'audit'
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
    $unexpectedEpcs = array_values(array_diff($detectedEpcs, $expectedEpcs)); // Correcto: detectados que no estaban en esperados

    // La auditoría es "exitosa" para habilitar el botón si TODOS los esperados fueron encontrados.
    // La presencia de inesperados se informa, pero no bloquea este tipo de éxito.
    $allExpectedItemsFound = empty($missingEpcs);

    return response()->json([
        // Usaremos este nuevo flag para la lógica del botón de finalizar auditoría
        'all_expected_items_found' => $allExpectedItemsFound,
        'verified_epcs' => $verifiedEpcs,
        'missing_epcs' => $missingEpcs,
        'unexpected_epcs' => $unexpectedEpcs, // Seguimos informando sobre estos
        'expected_count' => count($expectedEpcs),
        'detected_count' => count($detectedEpcs),
        // Mantenemos el 'all_verified' anterior por si lo usas en otro lado o para un reporte más estricto.
        // Este 'all_verified_strict' significa que se encontraron todos los esperados Y NINGUNO inesperado.
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
            $workOrder->update(['is_audited' => true]); //marc como auditada la work order

                       ActivityLog::create([
                'user_id' => Auth::id(),
                'work_order_id' => $workOrder->id,
                'action' => 'ORDER_AUDIT_COMPLETED', // Acción específica para auditoría completada
                'details' => [
                    'audited_by' => Auth::user()->name,
                    'result' => 'Auditoría RFID completada satisfactoriamente.',
                    // Podrías añadir más detalles si los tienes, como el conteo de items verificados
                    // que podrías pasar desde el JS en el request del formulario si fuera necesario.
                ]
            ]);
        });

         return redirect()->route('audit.work_orders.list') // De vuelta a la lista de auditorías
                          ->with('success', "Auditoría de Orden {$workOrder->folio} completada exitosamente.");
    } catch (\Exception $e) {
        report($e);
        // Si la auditoría es en una pantalla específica, redirigir ahí con error
        return redirect()->route('audit.work_orders.rfid_screen', $workOrder)
                         ->with('error', 'Error al procesar la finalización de la auditoría. Intente de nuevo.');
    }
}






}
