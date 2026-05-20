<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerItem;
use App\Models\Box;
use App\Models\Pallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BoxController extends Controller
{
    public function packing(Container $container)
    {
        $container->load(['items.boxes', 'boxes.containerItem', 'boxes.pallet', 'boxes.creator']);

        // Solo cajas de reempaque para esta vista
        $repackedBoxes = $container->boxes->where('source', 'reempaque');

        $stats = [
            'total_boxes'       => $repackedBoxes->count(),
            'boxes_closed'      => $repackedBoxes->where('status', 'cerrada')->count(),
            'boxes_on_pallet'   => $repackedBoxes->where('status', 'en_tarima')->count(),
            'total_packed_pcs'  => $repackedBoxes->sum('quantity'),
            'total_expected_pcs'=> $repackedBoxes->sum('expected_qty'),
            'total_missing'     => $repackedBoxes->sum(fn($b) => $b->missing > 0 ? $b->missing : 0),
        ];

        return view('containers.packing', compact('container', 'stats', 'repackedBoxes'));
    }

    public function createBoxes(Request $request, Container $container)
    {
        $validated = $request->validate([
            'container_item_id' => 'required|exists:container_items,id',
            'pieces_per_box'    => 'required|integer|min:1',
            'box_count'         => 'required|integer|min:1|max:500',
        ]);

        $item = ContainerItem::findOrFail($validated['container_item_id']);
        $lastSeq = $container->boxes()->count();

        DB::beginTransaction();
        try {
            for ($i = 1; $i <= $validated['box_count']; $i++) {
                Box::create([
                    'container_id'      => $container->id,
                    'container_item_id' => $item->id,
                    'box_code'          => Box::generateBoxCode($container, $lastSeq + $i),
                    'source'            => 'reempaque',
                    'expected_qty'      => $validated['pieces_per_box'],
                    'quantity'          => $validated['pieces_per_box'],
                    'status'            => 'cerrada',
                    'created_by'        => Auth::id(),
                    'closed_at'         => now(),
                ]);
            }
            DB::commit();

            $totalPcs = $validated['box_count'] * $validated['pieces_per_box'];
            return back()->with('success', "Se crearon {$validated['box_count']} cajas ({$totalPcs} piezas).");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar la cantidad real de piezas en una caja.
     */
    public function updateBoxQuantity(Request $request, Box $box)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $box->update(['quantity' => $validated['quantity']]);
        return back()->with('success', "Caja {$box->box_code} actualizada: {$validated['quantity']} piezas.");
    }

    public function destroyBox(Box $box)
    {
        if ($box->isAssignedToPallet()) {
            return back()->with('error', 'No se puede eliminar una caja asignada a tarima.');
        }
        $code = $box->box_code;
        $box->delete();
        return back()->with('success', "Caja {$code} eliminada.");
    }

    // === TARIMAS ===

    public function pallets(Container $container)
    {
        $container->load(['pallets.boxes.containerItem', 'pallets.creator']);

        $availableBoxes = Box::where('container_id', $container->id)
            ->availableForPallet()->with('containerItem')->orderBy('box_code')->get();

        $stats = [
            'total_pallets'   => $container->pallets->count(),
            'pallets_open'    => $container->pallets->where('status', 'abierta')->count(),
            'pallets_closed'  => $container->pallets->where('status', 'cerrada')->count(),
            'available_boxes' => $availableBoxes->count(),
        ];

        return view('containers.pallets', compact('container', 'availableBoxes', 'stats'));
    }

    public function createPallet(Request $request, Container $container)
    {
        $seq = $container->pallets()->count() + 1;
        $pallet = Pallet::create([
            'container_id' => $container->id,
            'pallet_code'  => Pallet::generatePalletCode($container, $seq),
            'status'       => 'abierta',
            'created_by'   => Auth::id(),
            'notes'        => $request->input('notes'),
        ]);
        return back()->with('success', "Tarima {$pallet->pallet_code} creada.");
    }

    public function assignBoxes(Request $request, Pallet $pallet)
    {
        $validated = $request->validate([
            'box_ids'   => 'required|array|min:1',
            'box_ids.*' => 'exists:boxes,id',
        ]);

        $assigned = 0;
        foreach ($validated['box_ids'] as $boxId) {
            $box = Box::find($boxId);
            if ($box && !$box->isAssignedToPallet() && $box->container_id === $pallet->container_id) {
                $box->assignToPallet($pallet);
                $assigned++;
            }
        }
        return back()->with('success', "{$assigned} cajas asignadas a {$pallet->pallet_code}.");
    }

    public function assignBulk(Request $request, Pallet $pallet)
    {
        // 1. Validamos lo que viene del nuevo formulario (ID del producto y la cantidad)
        $validated = $request->validate([
            'container_item_id' => 'required|exists:container_items,id',
            'quantity'          => 'required|integer|min:1',
        ]);

        // 2. Buscamos las cajas disponibles (huérfanas) de ese producto específico
        $boxes = Box::where('container_id', $pallet->container_id)
            ->where('container_item_id', $validated['container_item_id'])
            ->whereNull('pallet_id') // Asumiendo que así sabes que están libres en BD
            ->take($validated['quantity']) // Tomamos EXACTAMENTE la cantidad que pidió el usuario
            ->get();

        // 3. Validamos que el sistema sí encontró suficientes cajas
        if ($boxes->count() < $validated['quantity']) {
            return back()->with('error', "Atención: Solo hay {$boxes->count()} cajas disponibles de este producto.");
        }

        // 4. Asignamos usando tu lógica existente
        $assigned = 0;
        foreach ($boxes as $box) {
            if (!$box->isAssignedToPallet()) {
                $box->assignToPallet($pallet);
                $assigned++;
            }
        }

        return back()->with('success', "{$assigned} cajas asignadas a {$pallet->pallet_code}.");
    }

    public function removeBox(Box $box)
    {
        if (!$box->isAssignedToPallet()) {
            return back()->with('error', 'Esta caja no está en ninguna tarima.');
        }
        $palletCode = $box->pallet->pallet_code;
        $box->removeFromPallet();
        return back()->with('success', "Caja {$box->box_code} retirada de {$palletCode}.");
    }

    /**
     * Retira múltiples cajas de una tarima en un solo request.
     */
    public function removeBulk(Request $request, Pallet $pallet)
    {
        $validated = $request->validate([
            'box_ids'   => 'required|array|min:1',
            'box_ids.*' => 'exists:boxes,id',
        ]);

        $removed = 0;
        foreach ($validated['box_ids'] as $boxId) {
            $box = Box::find($boxId);
            if ($box && $box->pallet_id === $pallet->id) {
                $box->removeFromPallet();
                $removed++;
            }
        }

        return back()->with('success', "{$removed} caja(s) retirada(s) de {$pallet->pallet_code}.");
    }

    public function closePallet(Pallet $pallet)
    {
        if ($pallet->boxes()->count() === 0) {
            return back()->with('error', 'No se puede cerrar una tarima sin cajas.');
        }
        $pallet->close();
        return back()->with('success', "Tarima {$pallet->pallet_code} cerrada.");
    }

    public function showPallet(Pallet $pallet)
    {
        $pallet->load(['container', 'boxes.containerItem', 'creator',]);
        return view('containers.pallet-detail', compact('pallet'));
    }
     public function printLabel(Pallet $pallet)
    {
        $pallet->load(['container', 'boxes.containerItem']);
        return view('containers.label-4x2', compact('pallet'));
    }
   
}
