<?php

namespace App\Http\Controllers;

use App\Models\Pallet;
use App\Models\Location;
use Illuminate\Http\Request;

class PalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pallets = Pallet::with('container', 'location')->get();
        $locations = Location::all();
        $unassignedPallets = Pallet::whereNull('location_id')->get();
       
        return view('pallets.index', compact('pallets', 'locations', 'unassignedPallets'));
    }
    public function assignToLocation(Request $request, Pallet $pallet)
    {
        // 1. Validar el request
        $request->validate([
            'location_id' => 'required|exists:locations,id',
        ]);

        // 2. Obtener la localidad
        $location = Location::findOrFail($request->input('location_id'));

        // 3. Usar nuestro método inteligente en lugar de hasPallets()
        if (!$location->hasAvailableSpace()) {
            return redirect()->route('pallets.index')->with('error', 'La localidad seleccionada ya está llena o no tiene espacio disponible.');
        }

        // 4. Intentar asignar
        try {
            $pallet->assignToLocation($location);
            return redirect()->route('pallets.index')->with('success', 'Tarima asignada a la localidad exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('pallets.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pallet $pallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pallet $pallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pallet $pallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pallet $pallet)
    {
        //
    }

    
}
