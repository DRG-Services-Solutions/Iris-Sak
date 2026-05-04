<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_number', 'picking_order_id', 'transport_type',
        'driver_name', 'plates', 'destination', 'status',
        'dispatched_by', 'notes', 'loaded_at', 'dispatched_at',
    ];

    protected $casts = [
        'loaded_at'     => 'datetime',
        'dispatched_at' => 'datetime',
    ];

    public function pickingOrder() { return $this->belongsTo(PickingOrder::class); }
    public function dispatchedBy() { return $this->belongsTo(User::class, 'dispatched_by'); }

    public static function generateDispatchNumber(): string
    {
        $today = now()->format('Ymd');
        $seq = self::whereDate('created_at', today())->count() + 1;
        return sprintf('DES-%s-%03d', $today, $seq);
    }

    public function markLoaded(): void
    {
        $this->update(['status' => 'cargado', 'loaded_at' => now()]);
    }

    public function markDispatched(): void
    {
        $this->update(['status' => 'despachado', 'dispatched_at' => now()]);
    }
}
