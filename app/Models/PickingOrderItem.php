<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickingOrderItem extends Model
{
    protected $fillable = [
        'picking_order_id', 'pallet_id', 'status',
        'pick_type', 'container_item_id', 'quantity', // <-- Nuevos campos
        'picked_by', 'picked_at', 'notes',
    ];

    protected $casts = ['picked_at' => 'datetime'];

    public function pickingOrder() { return $this->belongsTo(PickingOrder::class); }
    public function pallet()       { return $this->belongsTo(Pallet::class); }
    public function pickedByUser() { return $this->belongsTo(User::class, 'picked_by'); }
    public function containerItem() 
    { 
        return $this->belongsTo(ContainerItem::class); 
    }

    public function markPrepared(int $userId): void
    {
        $this->update([
            'status'    => 'preparado',
            'picked_by' => $userId,
            'picked_at' => now(),
        ]);
    }
}
