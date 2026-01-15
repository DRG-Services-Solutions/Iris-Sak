<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule; 



class ProductController extends Controller
{

    public function __construct()
    {
        // Usamos el middleware 'can' que automáticamente utiliza el Gate que definimos.
        // Esto protegerá TODOS los métodos (index, create, store, show, edit, update, destroy).
        $this->middleware('can:manage-products');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            // 1. Validar los datos del request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'barcode' => 'required|string|max:255|unique:products,barcode', 
            ]);


            Product::create($validated);

            // 3. Redirigir a alguna parte (usualmente al index) con un mensaje de éxito
            return redirect()->route('products.index')
                             ->with('success', '¡Herramienta creada exitosamente!'); // 'success' es la clave del mensaje flash
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validar los datos del request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'barcode' => [ 
                'required',
                'string',
                'max:255',
                Rule::unique('products')->ignore($product->id), 
            ],
        ]);
    
        // 2. Actualizar la herramienta existente con los datos validados
        $product->update($validated);
    
        // 3. Redirigir (usualmente al index) con un mensaje de éxito
        return redirect()->route('products.index')
                         ->with('success', '¡Herramienta actualizada exitosamente!');    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // 1. Eliminar la herramienta
        $product->delete();
    
        // 2. Redirigir con un mensaje de éxito
        return redirect()->route('products.index')
                         ->with('success', '¡Herramienta eliminada exitosamente!');
    }
  
   
}
