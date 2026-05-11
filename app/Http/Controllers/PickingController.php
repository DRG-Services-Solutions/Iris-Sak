<?php

namespace App\Http\Controllers;

use App\Models\PickingOrder;
use App\Models\PickingOrderItem;
use App\Models\Pallet;
use App\Models\User;
use App\Models\Box;
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
            ->whereNotNull('maquila_completed_at')
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
     * Procesar y marcar una tarima (o fracción) como preparada.
     */
    public function markItemPrepared(Request $request, PickingOrderItem $item)
    {
        // 1. Validamos qué tipo de surtido nos envía la vista
        $validated = $request->validate([
            'pick_type'         => 'required|in:full_pallet,partial',
            'container_item_id' => 'required_if:pick_type,partial|exists:container_items,id|nullable',
            'quantity'          => 'required_if:pick_type,partial|integer|min:1|nullable',
        ]);

        $order = $item->pickingOrder;
        $pallet = $item->pallet;

        DB::beginTransaction();
        try {
            if ($validated['pick_type'] === 'partial') {
                
                $boxesToPick = $pallet->boxes()
                    ->where('container_item_id', $validated['container_item_id'])
                    ->take($validated['quantity'])
                    ->get();

                if ($boxesToPick->count() < $validated['quantity']) {
                    return back()->with('error', 'No hay suficientes cajas de este artículo en la tarima.');
                }
                foreach ($boxesToPick as $box) {
                    $box->pallet_id = null; 
                    $box->picking_order_id = $order->id; 
                    $box->status = 'cerrada'; 
                    $box->save();
                }

                // Descontamos las cajas de la tarima original y las asignamos al carrito/orden
                Box::whereIn('id', $boxesToPick->pluck('id'))->update([
                    'pallet_id'        => null, 
                    'picking_order_id' => $order->id, 
                ]);

                // Actualizamos el registro de la orden para saber exactamente qué se pidió
                $item->update([
                    'pick_type'         => 'partial',
                    'container_item_id' => $validated['container_item_id'],
                    'quantity'          => $validated['quantity'],
                ]);

                $pallet->refresh(); 
                if ($pallet->boxes()->count() === 0) {
                    $pallet->update([
                        'location_id' => null,
                        'status'      => 'embarcado'
                    ]);
                }

            } else {
                
                // Si se lleva la tarima entera, todas sus cajas se vinculan a la orden de inmediato
                $pallet->boxes()->update([
                    'picking_order_id' => $order->id,
                ]);

                $item->update([
                    'pick_type' => 'full_pallet',
                ]);
            }

            // 2. Marcamos el ítem como preparado (reutilizamos tu función del modelo)
            $item->markPrepared(Auth::id());

            // 3. Revisamos si con este movimiento la orden ya quedó completa al 100%
            if ($order->items()->where('status', '!=', 'preparado')->doesntExist()) {
                $order->complete();
            }

            DB::commit();
            return back()->with('success', "Surtido registrado exitosamente para la tarima {$pallet->pallet_code}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el surtido: ' . $e->getMessage());
        }
    }
}
