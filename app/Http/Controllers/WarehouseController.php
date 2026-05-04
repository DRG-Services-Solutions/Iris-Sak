<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Pallet;
use App\Models\PalletTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    /**
     * Mapa de localidades con tarimas asignadas.
     */
    public function locations(Request $request)
    {
        $query = Location::active()->withCount('pallets');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('zone', 'like', "%{$search}%");
            });
        }

        if ($zone = $request->input('zone')) {
            $query->where('zone', $zone);
        }

        $locations = $query->orderBy('zone')->orderBy('code')->paginate(20)->withQueryString();
        $zones = Location::active()->whereNotNull('zone')->distinct()->pluck('zone');

        // Tarimas sin localidad asignada
        $unassignedPallets = Pallet::closed()->unassigned()
            ->with('container')->orderBy('pallet_code')->get();

        return view('warehouse.locations', compact('locations', 'zones', 'unassignedPallets'));
    }

    /**
     * Crear localidad.
     */
    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'code'     => 'required|string|max:30|unique:locations,code',
            'name'     => 'required|string|max:100',
            'zone'     => 'nullable|string|max:30',
            'aisle'    => 'nullable|string|max:10',
            'level'    => 'nullable|string|max:10',
            'position' => 'nullable|string|max:10',
            'type'     => 'required|in:rack,piso,andén,tránsito,otro',
        ]);

        Location::create($validated);
        return back()->with('success', "Localidad {$validated['code']} creada.");
    }

    /**
     * Asignar tarima a localidad.
     */
    public function assignPallet(Request $request)
    {
        $validated = $request->validate([
            'pallet_id'   => 'required|exists:pallets,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $pallet = Pallet::findOrFail($validated['pallet_id']);
        $location = Location::findOrFail($validated['location_id']);

        $pallet->assignToLocation($location);

        return back()->with('success', "Tarima {$pallet->pallet_code} asignada a {$location->code}.");
    }

    /**
     * Transferir tarima a otra localidad.
     */
    public function transferPallet(Request $request, Pallet $pallet)
    {
        $validated = $request->validate([
            'to_location_id' => 'required|exists:locations,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $toLocation = Location::findOrFail($validated['to_location_id']);
        $fromCode = $pallet->location?->code ?? 'Sin asignar';

        $pallet->assignToLocation($toLocation, Auth::id());

        if ($validated['notes'] ?? null) {
            $transfer = $pallet->transfers()->latest()->first();
            $transfer?->update(['notes' => $validated['notes']]);
        }

        return back()->with('success', "Tarima {$pallet->pallet_code} transferida de {$fromCode} a {$toLocation->code}.");
    }

    /**
     * Ver detalle de una localidad con sus tarimas.
     */
    public function showLocation(Location $location)
    {
        $location->load(['pallets.boxes.containerItem', 'pallets.container']);

        $allLocations = Location::active()->where('id', '!=', $location->id)->orderBy('code')->get();

        return view('warehouse.location-detail', compact('location', 'allLocations'));
    }

    /**
     * Historial de transferencias.
     */
    public function transfers(Request $request)
    {
        $transfers = PalletTransfer::with([
            'pallet', 'fromLocation', 'toLocation', 'transferredBy'
        ])->latest()->paginate(25);

        return view('warehouse.transfers', compact('transfers'));
    }
}
