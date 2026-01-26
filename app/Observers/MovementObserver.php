<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\ProductInstance;

class MovementObserver
{
    public function created(Movement $movement): void
    {
        // 1. Si el movimiento está ligado a una instancia específica
        if ($movement->product_instance_id) {
            $instance = ProductInstance::find($movement->product_instance_id);

            if ($movement->type === 'out') {
                $instance->update(['status' => 'sold_or_shipped']);
            } elseif ($movement->type === 'adjustment') {
                $instance->update(['status' => 'quarantine_or_review']);
            }
        }

        // 2. Si aún mantienes un conteo global en el modelo Product
        $movement->product->decrement('stock', $movement->quantity);
    }
}