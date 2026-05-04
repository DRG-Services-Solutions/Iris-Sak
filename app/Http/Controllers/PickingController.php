<?php

namespace App\Http\Controllers;

use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\Pallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PickingController extends Controller
{
    public function index(Request $request)
    {
        $query = PickingOrder::with(['creator', 'assignee'])
            ->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('picking.index', compact('orders'));
    }

    public function create()
    {
        // Tarimas cerradas con localidad asignada = disponibles para surtir
        $availablePallets = Pallet::closed()
            ->whereNotNull('location_id')
            ->with(['container', 'location', 'boxes.containerItem'])
            ->orderBy('pallet_code')
            ->get();

        $users = User::orderBy('name')->get();

        return view('picking.create', compact('availablePallets', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'destination'  => 'required|string|max:255',
            'priority'     => 'required|in:normal,urgente',
            'assigned_to'  => 'nullable|exists:users,id',
            'pallet_ids'   => 'required|array|min:1',
            'pallet_ids.*' => 'exists:pallets,id',
            'notes'        => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $order = PickingOrder::create([
                'order_number' => PickingOrder::generateOrderNumber(),
                'client_name'  => $validated['client_name'],
                'destination'  => $validated['destination'],
                'priority'     => $validated['priority'],
                'created_by'   => Auth::id(),
                'assigned_to'  => $validated['assigned_to'] ?? null,
                'notes'        => $validated['notes'] ?? null,
            ]);

            foreach ($validated['pallet_ids'] as $palletId) {
                PickingOrderItem::create([
                    'picking_order_id' => $order->id,
                    'pallet_id'        => $palletId,
                ]);
            }

            DB::commit();
            return redirect()->route('picking.show', $order)
                ->with('success', "Orden de surtido {$order->order_number} creada con " . count($validated['pallet_ids']) . " tarimas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(PickingOrder $order)
    {
        $order->load([
            'items.pallet.location',
            'items.pallet.boxes.containerItem',
            'items.pallet.container',
            'items.pickedByUser',
            'creator', 'assignee', 'dispatch',
        ]);

        return view('picking.show', compact('order'));
    }

    /**
     * Iniciar surtido.
     */
    public function start(PickingOrder $order)
    {
        $order->start();
        return back()->with('success', 'Orden de surtido iniciada.');
    }

    /**
     * Marcar una tarima como preparada.
     */
    public function markItemPrepared(PickingOrderItem $item)
    {
        $item->markPrepared(Auth::id());

        // Si todos están preparados, completar la orden
        $order = $item->pickingOrder;
        if ($order->items()->where('status', '!=', 'preparado')->doesntExist()) {
            $order->complete();
        }

        return back()->with('success', "Tarima {$item->pallet->pallet_code} marcada como preparada.");
    }
}
