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
    // ===================================================================
    // EMPAQUE EN CAJAS
    // ===================================================================

    /**
     * Vista principal de empaque para un contenedor.
     */
    public function packing(Container $container)
    {
        $container->load([
            'items.boxes',
            'boxes.containerItem',
            'boxes.pallet',
            'boxes.creator',
        ]);

        $stats = [
            'total_boxes'       => $container->boxes->count(),
            'boxes_open'        => $container->boxes->where('status', 'abierta')->count(),
            'boxes_closed'      => $container->boxes->where('status', 'cerrada')->count(),
            'boxes_on_pallet'   => $container->boxes->where('status', 'en_tarima')->count(),
            'total_packed_pcs'  => $container->boxes->sum('quantity'),
        ];

        return view('containers.packing', compact('container', 'stats'));
    }

    /**
     * Crear cajas para un artículo del contenedor.
     * El usuario define: artículo, piezas por caja, cantidad de cajas.
     */
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
            $created = 0;
            for ($i = 1; $i <= $validated['box_count']; $i++) {
                $seq = $lastSeq + $i;
                Box::create([
                    'container_id'      => $container->id,
                    'container_item_id' => $item->id,
                    'box_code'          => Box::generateBoxCode($container, $seq),
                    'quantity'          => $validated['pieces_per_box'],
                    'status'            => 'cerrada',
                    'created_by'        => Auth::id(),
                    'closed_at'         => now(),
                ]);
                $created++;
            }

            DB::commit();

            $totalPcs = $created * $validated['pieces_per_box'];
            return back()->with('success', "Se crearon {$created} cajas ({$totalPcs} piezas) para: {$item->product_description}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear cajas: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar una caja (solo si no está en tarima).
     */
    public function destroyBox(Box $box)
    {
        if ($box->isAssignedToPallet()) {
            return back()->with('error', 'No se puede eliminar una caja asignada a tarima. Retírela primero.');
        }

        $code = $box->box_code;
        $box->delete();

        return back()->with('success', "Caja {$code} eliminada.");
    }

    // ===================================================================
    // ARMADO DE TARIMAS
    // ===================================================================

    /**
     * Vista principal de tarimas para un contenedor.
     */
    public function pallets(Container $container)
    {
        $container->load([
            'pallets.boxes.containerItem',
            'pallets.creator',
        ]);

        // Cajas disponibles (cerradas y sin tarima asignada)
        $availableBoxes = Box::where('container_id', $container->id)
            ->availableForPallet()
            ->with('containerItem')
            ->orderBy('box_code')
            ->get();

        $stats = [
            'total_pallets'    => $container->pallets->count(),
            'pallets_open'     => $container->pallets->where('status', 'abierta')->count(),
            'pallets_closed'   => $container->pallets->where('status', 'cerrada')->count(),
            'available_boxes'  => $availableBoxes->count(),
        ];

        return view('containers.pallets', compact('container', 'availableBoxes', 'stats'));
    }

    /**
     * Crear una tarima nueva.
     */
    public function createPallet(Request $request, Container $container)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $seq = $container->pallets()->count() + 1;

        $pallet = Pallet::create([
            'container_id' => $container->id,
            'pallet_code'  => Pallet::generatePalletCode($container, $seq),
            'status'       => 'abierta',
            'created_by'   => Auth::id(),
            'notes'        => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "Tarima {$pallet->pallet_code} creada.");
    }

    /**
     * Asignar cajas a una tarima.
     */
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

        return back()->with('success', "{$assigned} cajas asignadas a tarima {$pallet->pallet_code}.");
    }

    /**
     * Retirar una caja de su tarima.
     */
    public function removeBox(Box $box)
    {
        if (!$box->isAssignedToPallet()) {
            return back()->with('error', 'Esta caja no está asignada a ninguna tarima.');
        }

        $palletCode = $box->pallet->pallet_code;
        $box->removeFromPallet();

        return back()->with('success', "Caja {$box->box_code} retirada de tarima {$palletCode}.");
    }

    /**
     * Cerrar una tarima (ya no se le pueden agregar/quitar cajas).
     */
    public function closePallet(Pallet $pallet)
    {
        if ($pallet->boxes()->count() === 0) {
            return back()->with('error', 'No se puede cerrar una tarima sin cajas.');
        }

        $pallet->close();

        return back()->with('success', "Tarima {$pallet->pallet_code} cerrada con {$pallet->total_boxes} cajas y {$pallet->total_pieces} piezas.");
    }

    /**
     * Ver detalle/resumen de una tarima (imprimible como etiqueta maestra).
     */
    public function showPallet(Pallet $pallet)
    {
        $pallet->load(['container', 'boxes.containerItem', 'creator']);

        return view('containers.pallet-detail', compact('pallet'));
    }
}
