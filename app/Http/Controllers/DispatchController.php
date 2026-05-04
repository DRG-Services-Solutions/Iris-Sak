<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\PickingOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $dispatch->markDispatched();
        return back()->with('success', "Despacho {$dispatch->dispatch_number} confirmado. Mercancía en camino.");
    }
}
