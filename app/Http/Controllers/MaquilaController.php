<?php
namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Pallet;
use App\Models\Container;
use App\Models\MaquilaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaquilaController extends Controller
{
    public function index(Request $request)
    {
        $stationFilter = $request->input('station');

        // 1. Iniciamos la consulta base de Contenedores que tengan tarimas cerradas
        $query = Container::whereHas('pallets', fn($q) => $q->closed());

        // --- APLICACIÓN DE FILTROS ---

        // Filtro por Contenedor
        if ($request->filled('container')) {
            $query->where('container_seal_number', 'like', '%' . $request->container . '%');
        }

        // Filtro por Fecha (Usando la fecha de creación del contenedor)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtro por Código de Tarima
        if ($request->filled('pallet')) {
            $query->whereHas('pallets', function($q) use ($request) {
                $q->where('pallet_code', 'like', '%' . $request->pallet . '%');
            });
        }

        // Filtro por Artículo (Busca en SKU, Barcode o Descripción dentro de las cajas de las tarimas)
        if ($request->filled('article')) {
            $query->whereHas('pallets.boxes.containerItem', function($q) use ($request) {
                $q->where('product_code', 'like', '%' . $request->article . '%')
                ->orWhere('barcode', 'like', '%' . $request->article . '%')
                ->orWhere('product_description', 'like', '%' . $request->article . '%');
            });
        }

        // --- CARGA DE RELACIONES (Eager Loading) ---
        // Aquí también aplicamos los filtros para que la vista solo cargue las tarimas que coinciden
        $containers = $query->with(['pallets' => function($q) use ($request) {
            $q->closed()->with(['boxes.containerItem', 'location']);
            
            // Si el usuario buscó una tarima específica, solo le enviamos esa tarima a la vista
            if ($request->filled('pallet')) {
                $q->where('pallet_code', 'like', '%' . $request->pallet . '%');
            }
            
            // Si el usuario buscó un artículo, solo traemos las tarimas que lo contengan
            if ($request->filled('article')) {
                $q->whereHas('boxes.containerItem', function($subQ) use ($request) {
                    $subQ->where('product_code', 'like', '%' . $request->article . '%')
                        ->orWhere('barcode', 'like', '%' . $request->article . '%')
                        ->orWhere('product_description', 'like', '%' . $request->article . '%');
                });
            }
        }])
        ->orderBy('id', 'desc')
        ->get();

        // 2. Mantenemos los KPIs globales (estos no se filtran por la búsqueda, muestran el estatus real de la maquila)
        $stationCounts = [
            'sin_iniciar' => Pallet::closed()->whereNull('maquila_started_at')->count(),
            1 => Pallet::atStation(1)->count(),
            2 => Pallet::atStation(2)->count(),
            3 => Pallet::atStation(3)->count(),
            'completado' => Pallet::maquilaCompleted()->count(),
        ];

        return view('maquila.index', compact('containers', 'stationCounts', 'stationFilter'));
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

    public function printLabel(Pallet $pallet)
    {
        
        $pallet->load('boxes', 'container.items');
        
        
        return view('maquila.label-2x4', compact('pallet'));
    }
}
