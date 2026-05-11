<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\PickingOrder;
use App\Models\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;


class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispatch::with(['pickingOrder', 'dispatchedBy']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $dispatches = $query->latest()->paginate(15)->withQueryString();
        return view('dispatch.index', compact('dispatches'));
    }

    public function create(Request $request)
    {
        // Solo órdenes completadas que no tienen despacho aún
        $completedOrders = PickingOrder::where('status', 'completado')
            ->doesntHave('dispatch')
            ->with('items.pallet')
            ->get();

        // Si viene preseleccionada una orden
        $selectedOrder = null;
        if ($orderId = $request->input('picking_order_id')) {
            $selectedOrder = PickingOrder::with('items.pallet.boxes')->find($orderId);
        }

        return view('dispatch.create', compact('completedOrders', 'selectedOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'picking_order_id' => 'required|exists:picking_orders,id',
            'transport_type'   => 'required|in:5ta_rueda,torton,camioneta,otro',
            'driver_name'      => 'nullable|string|max:100',
            'plates'           => 'nullable|string|max:20',
            'destination'      => 'required|string|max:255',
            'notes'            => 'nullable|string|max:500',
        ]);

        $dispatch = Dispatch::create([
            'dispatch_number'  => Dispatch::generateDispatchNumber(),
            'picking_order_id' => $validated['picking_order_id'],
            'transport_type'   => $validated['transport_type'],
            'driver_name'      => $validated['driver_name'] ?? null,
            'plates'           => $validated['plates'] ?? null,
            'destination'      => $validated['destination'],
            'dispatched_by'    => Auth::id(),
            'notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('dispatch.show', $dispatch)
            ->with('success', "Despacho {$dispatch->dispatch_number} creado.");
    }

    public function show(Dispatch $dispatch)
    {
        $dispatch->load([
            'pickingOrder.items.pallet.boxes.containerItem',
            'pickingOrder.items.pallet.location',
            'pickingOrder.items.pallet.container',
            'dispatchedBy',
        ]);

        return view('dispatch.show', compact('dispatch'));
    }

    public function markLoaded(Dispatch $dispatch)
    {
        $dispatch->markLoaded();

        // Marcar items como cargados
        $dispatch->pickingOrder->items()->update(['status' => 'cargado']);

        return back()->with('success', 'Mercancía cargada al transporte.');
    }


    public function markDispatched(Dispatch $dispatch)
    {
        Log::info("[Despacho] Iniciando markDispatched para dispatch #{$dispatch->id} ({$dispatch->dispatch_number})");

        DB::beginTransaction();
        try {
            $dispatch->markDispatched();
            Log::info("[Despacho] Dispatch #{$dispatch->id} marcado como despachado");

            $order = $dispatch->pickingOrder;
            Log::info("[Despacho] Orden de picking #{$order->id} tiene {$order->items->count()} ítems");

            foreach ($order->items as $item) {
                Log::info("[Despacho] Procesando ítem #{$item->id} | pick_type: {$item->pick_type} | pallet_id: {$item->pallet_id}");

                if ($item->pick_type === 'full_pallet' && $item->pallet) {
                    $pallet = $item->pallet;
                    $oldLocationId = $pallet->location_id;

                    $pallet->update([
                        'location_id' => null,
                        'status'      => 'despachado'
                    ]);

                    $boxCount = $pallet->boxes()->count();
                    $pallet->boxes()->update([
                        'status' => 'despachada'
                    ]);

                    Log::info("[Despacho] FULL_PALLET: Tarima #{$pallet->id} embarcada | location_id: {$oldLocationId} → null | {$boxCount} cajas marcadas embarcadas");

                } elseif ($item->pick_type === 'partial') {
                    $affectedBoxes = Box::where('picking_order_id', $order->id)->count();

                    Box::where('picking_order_id', $order->id)
                        ->update([
                            'status' => 'despachada'
                        ]);

                    Log::info("[Despacho] PARTIAL: {$affectedBoxes} cajas de orden #{$order->id} marcadas embarcadas (tarima #{$item->pallet_id} sin cambios)");

                } else {
                    
                    Log::warning("[Despacho] Ítem #{$item->id} no procesado | pick_type: {$item->pick_type} | pallet presente: " . ($item->pallet ? 'sí' : 'no'));
                }
            }
            $pallet->update(['dispatched_at' => now()]);

            DB::commit();
            Log::info("[Despacho] ✅ Commit exitoso para dispatch #{$dispatch->id}");

            return back()->with('success', "Despacho {$dispatch->dispatch_number} confirmado. El inventario ha sido descontado y las localidades actualizadas.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[Despacho] ❌ Rollback en dispatch #{$dispatch->id}: {$e->getMessage()}", [
                'exception' => $e,
                'trace'     => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error crítico al procesar el inventario del despacho: ' . $e->getMessage());
        }
    }
}