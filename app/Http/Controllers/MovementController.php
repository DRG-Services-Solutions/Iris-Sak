<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Requests\UpdateMovementRequest;
use Illuminate\Support\Facades\DB;

class MovementController extends Controller
{

    public function __construct()
    {
        // Esto hace todo el trabajo por ti
        $this->authorizeResource(Movement::class, 'movement');
    }
    /**
     * Index con Eager Loading para evitar problemas de N+1.
     */
    public function index()
    {
        $movements = Movement::with('product')
            ->latest()
            ->paginate(20);

        return view('movements.index', compact('movements'));
    }

    /**
     * Inyección de dependencias para poblar selectores en la vista.
     */
    public function create()
    {
        $products = Product::select('id', 'name')->get();
        return view('movements.create', compact('products'));
    }

    /**
     * Persistencia delegando lógica de negocio al MovementObserver.
     */
    public function store(Request $request)
    {
        $items = json_decode($request->payload, true);
        
        DB::transaction(function () use ($items, $request) {
            foreach ($items as $item) {
                Movement::create([
                    'product_id' => $item['product_id'],
                    'type' => $request->type,
                    'quantity' => $item['quantity'],
                    'user_id' => auth()->id(),
                    'notes' => 'Procesamiento por lote'
                ]);
            }
        });

        return redirect()->route('movements.index')->with('success', 'Lote procesado con éxito');
    }

    /**
     * Read-only: Detalle del movimiento.
     */
    public function show(Movement $movement)
    {
        return view('movements.show', compact('movement'));
    }

    /**
     * Generalmente los movimientos de inventario son inmutables por auditoría,
     * pero habilitamos edit para ajustes menores de notas.
     */
    public function edit(Movement $movement)
    {
        return view('movements.edit', compact('movement'));
    }

    /**
     * Update restringido: Evitar modificar cantidades/tipos para no romper el stock histórico.
     */
    public function update(UpdateMovementRequest $request, Movement $movement)
    {
        $movement->update($request->only('notes'));

        return redirect()->route('movements.index')
            ->with('status', 'movement-updated');
    }

    /**
     * SoftDeletes recomendados aquí para mantener trazabilidad.
     */
    public function destroy(Movement $movement)
    {
        $movement->delete();

        return redirect()->route('movements.index')
            ->with('status', 'movement-deleted');
    }
}