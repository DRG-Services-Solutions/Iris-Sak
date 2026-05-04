<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\InventoryController; 
use App\Http\Controllers\MovementController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContainerController;
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    
    Route::resource('tenants', TenantController::class);
 
    
});

Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// --- Grupo principal que requiere autenticación ---
Route::middleware('auth')->group(function () {

    //Ruta de resource de Usuarios
    Route::resource('users', UserController::class);

    //Ruta de roles
    Route::resource('roles', RoleController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/products', ProductController::class);

    // Rutas de Órdenes de Trabajo (Work Orders)
    Route::get('/work-orders', [WorkOrderController::class, 'index'])->name('work_orders.index');
    Route::post('/work-orders', [WorkOrderController::class, 'store'])->name('work_orders.store');
    Route::get('/work-orders/create', [WorkOrderController::class, 'create'])->name('work_orders.create');
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work_orders.show');
    Route::get('/work-orders/{workOrder}/scanning', [WorkOrderController::class, 'showScanningScreen'])->name('work_orders.scanning');
    Route::post('/work-orders/{workOrder}/scan', [WorkOrderController::class, 'processScan'])->name('work_orders.scan');
    Route::put('/work-orders/{workOrder}/finalize', [WorkOrderController::class, 'finalizeOrder'])->name('work_orders.finalize');
    Route::put('/work-orders/{workOrder}/release', [WorkOrderController::class, 'releaseOrder'])->name('work_orders.release');
    Route::get('/work-orders/{workOrder}/history', [WorkOrderController::class, 'showHistory'])->name('work_orders.history');
    Route::post('/work-orders/{workOrder}/verify-rfid', [WorkOrderController::class, 'verifyRfidItems'])->name('work_orders.verify_rfid');
    Route::get('/inventory/work-order/{workOrder}/start', [InventoryController::class, 'startWorkOrderInventory'])->name('inventory.work_order.start');

    // --- NUEVO: Grupo de Rutas para Conteos de Inventario RFID ---
    Route::prefix('inventory')->name('inventory.')->group(function () {
        // Listado de conteos de inventario
        Route::get('/', [InventoryController::class, 'index'])->name('index');

        // Crear nuevo conteo
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');

        // Ver detalles de un conteo
        Route::get('/{inventoryCount}', [InventoryController::class, 'show'])->name('show');

        // Pantalla de escaneo RFID para el conteo
        Route::get('/{inventoryCount}/rfid-scan', [InventoryController::class, 'showRfidScan'])->name('rfid-scan');

        // Verificar items escaneados vía AJAX
        Route::post('/{inventoryCount}/verify-rfid', [InventoryController::class, 'verifyRfidItems'])->name('verify-rfid');

        // Completar el conteo
        Route::post('/{inventoryCount}/complete', [InventoryController::class, 'complete'])->name('complete');

        // Cancelar el conteo
        Route::post('/{inventoryCount}/cancel', [InventoryController::class, 'cancel'])->name('cancel');
    });

    // --- NUEVO: Grupo de Rutas para Auditoría RFID de Órdenes Enviadas ---
    Route::prefix('audit')->name('audit.')->group(function () {

        // Muestra la lista de órdenes "Enviadas" que están pendientes de auditar
        Route::get('/work-orders', [WorkOrderController::class, 'listShippedOrdersForAudit'])
            ->name('work_orders.list'); // Nombre de ruta: audit.work_orders.list

        // Muestra la pantalla específica para la auditoría RFID de una orden
        Route::get('/work-orders/{workOrder}/rfid-screen', [WorkOrderController::class, 'showRfidAuditScreen'])
            ->name('work_orders.rfid_screen'); // Nombre: audit.work_orders.rfid_screen

        // Procesa la data de los EPCs detectados por RFID durante la auditoría
        Route::post('/work-orders/{workOrder}/verify-items', [WorkOrderController::class, 'verifyRfidAuditItems'])
            ->name('work_orders.verify_items'); // Nombre: audit.work_orders.verify_items

        // Marca la auditoría como completa para una orden
        Route::post('/work-orders/{workOrder}/complete', [WorkOrderController::class, 'completeAudit'])
            ->name('work_orders.complete'); // Nombre: audit.work_orders.complete

    }); 

    // --- Rutas de Contenedores (Recepción + Etiquetado Aduana) ---
    Route::prefix('containers')->name('containers.')->group(function () {
        Route::get('/', [ContainerController::class, 'index'])->name('index');
        Route::get('/create', [ContainerController::class, 'create'])->name('create');
        Route::post('/', [ContainerController::class, 'store'])->name('store');
        Route::get('/{container}', [ContainerController::class, 'show'])->name('show');

        // Items del packing list
        Route::post('/{container}/items', [ContainerController::class, 'addItem'])->name('add-item');
        Route::patch('/items/{item}', [ContainerController::class, 'updateItemReceived'])->name('update-item');
        Route::patch('/items/{item}/notes', [ContainerController::class, 'updateItemNotes'])->name('update-item-notes');

        // Estatus
        Route::patch('/{container}/customs', [ContainerController::class, 'updateCustomsStatus'])->name('update-customs');
        Route::patch('/{container}/close', [ContainerController::class, 'close'])->name('close');

        // Etiquetado / Inspección aduanal
        Route::get('/{container}/inspection', [ContainerController::class, 'inspection'])->name('inspection');
        Route::post('/{container}/labels', [ContainerController::class, 'generateLabels'])->name('generate-labels');
        Route::patch('/labels/{label}', [ContainerController::class, 'updateLabelStatus'])->name('update-label');
        Route::post('/{container}/bulk-inspect', [ContainerController::class, 'bulkInspect'])->name('bulk-inspect');
        Route::post('/{container}/mark-printed', [ContainerController::class, 'markPrinted'])->name('mark-printed');
        Route::get('/containers/{container}/scan', [ContainerController::class, 'scanMode'])->name('containers.scan');

        // Empaque en cajas (Semana 2)
        Route::get('/{container}/packing', [\App\Http\Controllers\BoxController::class, 'packing'])->name('packing');
        Route::post('/{container}/boxes', [\App\Http\Controllers\BoxController::class, 'createBoxes'])->name('create-boxes');

        // Armado de tarimas (Semana 2)
        Route::get('/{container}/pallets', [\App\Http\Controllers\BoxController::class, 'pallets'])->name('pallets');
        Route::post('/{container}/pallets', [\App\Http\Controllers\BoxController::class, 'createPallet'])->name('create-pallet');
    });

    // Cajas (operaciones individuales)
    Route::patch('/boxes/{box}/update-qty', [\App\Http\Controllers\BoxController::class, 'updateBoxQuantity'])->name('boxes.update-qty');
    Route::delete('/boxes/{box}', [\App\Http\Controllers\BoxController::class, 'destroyBox'])->name('boxes.destroy');
    Route::patch('/boxes/{box}/remove', [\App\Http\Controllers\BoxController::class, 'removeBox'])->name('boxes.remove');

    // Tarimas (operaciones individuales)
    Route::post('/pallets/{pallet}/assign-boxes', [\App\Http\Controllers\BoxController::class, 'assignBoxes'])->name('pallets.assign-boxes');
    Route::patch('/pallets/{pallet}/close', [\App\Http\Controllers\BoxController::class, 'closePallet'])->name('pallets.close');
    Route::get('/pallets/{pallet}', [\App\Http\Controllers\BoxController::class, 'showPallet'])->name('pallets.show');

    // --- Localidades y Movimientos (Semana 3) ---
    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('/locations', [\App\Http\Controllers\WarehouseController::class, 'locations'])->name('locations');
        Route::post('/locations', [\App\Http\Controllers\WarehouseController::class, 'storeLocation'])->name('store-location');
        Route::get('/locations/{location}', [\App\Http\Controllers\WarehouseController::class, 'showLocation'])->name('show-location');
        Route::post('/assign-pallet', [\App\Http\Controllers\WarehouseController::class, 'assignPallet'])->name('assign-pallet');
        Route::post('/pallets/{pallet}/transfer', [\App\Http\Controllers\WarehouseController::class, 'transferPallet'])->name('transfer-pallet');
        Route::get('/transfers', [\App\Http\Controllers\WarehouseController::class, 'transfers'])->name('transfers');
    });

    // --- Lista de Surtido / Picking (Semana 3) ---
    Route::prefix('picking')->name('picking.')->group(function () {
        Route::get('/', [\App\Http\Controllers\PickingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\PickingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\PickingController::class, 'store'])->name('store');
        Route::get('/{order}', [\App\Http\Controllers\PickingController::class, 'show'])->name('show');
        Route::post('/{order}/start', [\App\Http\Controllers\PickingController::class, 'start'])->name('start');
        Route::patch('/items/{item}/prepared', [\App\Http\Controllers\PickingController::class, 'markItemPrepared'])->name('mark-prepared');
    });

    // --- Despacho y Carga (Semana 3) ---
    Route::prefix('dispatch')->name('dispatch.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DispatchController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\DispatchController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\DispatchController::class, 'store'])->name('store');
        Route::get('/{dispatch}', [\App\Http\Controllers\DispatchController::class, 'show'])->name('show');
        Route::patch('/{dispatch}/loaded', [\App\Http\Controllers\DispatchController::class, 'markLoaded'])->name('mark-loaded');
        Route::patch('/{dispatch}/dispatched', [\App\Http\Controllers\DispatchController::class, 'markDispatched'])->name('mark-dispatched');
    });

    Route::resource('movements', MovementController::class);
    Route::get('/api/products/scan/{barcode}', function ($barcode) {
        $product = \App\Models\Product::where('barcode', $barcode)->first();
        return $product ? response()->json($product) : response()->json(['error' => 'No encontrado'], 404);
        })->middleware('auth');

}); 

require __DIR__.'/auth.php';