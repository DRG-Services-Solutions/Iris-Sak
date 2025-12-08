<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function startWorkOrderInventory(WorkOrder $workOrder)
        {
            // Autorizar si el usuario puede inventariar esta orden
            // $this->authorize('inventory', $workOrder); // Necesitaríamos una policy y método

            // Cargar instancias y sus productos
            $workOrder->load(['productInstances.product', 'user']);

            // Aquí es donde decidimos qué vista mostrar.
            // Esta vista será la que pregunte al usuario CÓMO quiere inventariar.
            return view('inventory.select_method', compact('workOrder'));
        }

   
}
