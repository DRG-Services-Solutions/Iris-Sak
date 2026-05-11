<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\PickingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- Asegúrate de importar DB

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
        DB::beginTransaction();
        try {
            // 1. Marcamos el despacho como completado/embarcado
            $dispatch->markDispatched();
            
            $order = $dispatch->pickingOrder;

            // 2. Procesamos el inventario físico para sacarlo del almacén
            foreach ($order->items as $item) {
                if ($item->pick_type === 'full_pallet' && $item->pallet) {
                    // --- SE VA LA TARIMA COMPLETA ---
                    // Vaciamos la localidad para que el hueco del rack quede libre de inmediato.
                    // Cambiamos el estatus a 'embarcado' para que desaparezca de las vistas activas.
                    $item->pallet->update([
                        'location_id' => null,
                        'status'      => 'embarcado'
                    ]);
                    
                    // Todas las cajas de esta tarima se marcan como embarcadas
                    $item->pallet->boxes()->update([
                        'status' => 'embarcado'
                    ]);

                } elseif ($item->pick_type === 'partial') {
                    // --- SE VAN SOLO ALGUNAS CAJAS (TARIMA MIXTA) ---
                    // La tarima original NO libera la localidad porque sigue físicamente en el rack 
                    // con las cajas que no se solicitaron.
                    
                    // Solo "desaparecemos" las cajas exactas que el operador separó para esta orden
                    \App\Models\Box::where('picking_order_id', $order->id)
                        ->update([
                            'status' => 'embarcado'
                        ]);
                }
            }

            DB::commit();
            return back()->with('success', "Despacho {$dispatch->dispatch_number} confirmado. El inventario ha sido descontado y las localidades actualizadas.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico al procesar el inventario del despacho: ' . $e->getMessage());
        }
    }
}