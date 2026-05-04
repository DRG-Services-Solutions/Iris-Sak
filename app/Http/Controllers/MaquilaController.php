<?php
namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Pallet;
use App\Models\MaquilaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaquilaController extends Controller
{
    public function index(Request $request)
    {
        $stationFilter = $request->input('station');

        $locations = Location::active()
            ->whereHas('pallets', fn($q) => $q->closed())
            ->with(['pallets' => fn($q) => $q->closed()->with(['boxes.containerItem', 'container'])])
            ->orderBy('zone')->orderBy('code')->get();

        $stationCounts = [
            'sin_iniciar' => Pallet::closed()->whereNotNull('location_id')->whereNull('maquila_started_at')->count(),
            1 => Pallet::atStation(1)->count(),
            2 => Pallet::atStation(2)->count(),
            3 => Pallet::atStation(3)->count(),
            'completado' => Pallet::maquilaCompleted()->count(),
        ];

        return view('maquila.index', compact('locations', 'stationCounts', 'stationFilter'));
    }

    public function moveToStation(Request $request, Pallet $pallet)
    {
        $validated = $request->validate([
            'station' => 'required|integer|in:1,2,3',
            'notes'   => 'nullable|string|max:500',
        ]);

        $pallet->moveToStation($validated['station'], Auth::id(), $validated['notes'] ?? null);
        return back()->with('success', "Tarima {$pallet->pallet_code} movida a Estación {$validated['station']}.");
    }

    public function complete(Request $request, Pallet $pallet)
    {
        $pallet->completeMaquila(Auth::id(), $request->input('notes'));
        return back()->with('success', "Maquila completada para {$pallet->pallet_code}.");
    }

    public function logs(Request $request)
    {
        $query = MaquilaLog::with(['pallet.location', 'changedBy'])->latest();
        if ($palletId = $request->input('pallet_id')) {
            $query->where('pallet_id', $palletId);
        }
        $logs = $query->paginate(30)->withQueryString();
        return view('maquila.logs', compact('logs'));
    }
}
