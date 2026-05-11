<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id', 'container_item_id', 'box_code', 'source',
        'expected_qty', 'quantity', 'status', 'pallet_id',
        'picking_order_id', 
        'created_by', 'notes', 'closed_at', 'assigned_to_pallet_at', 'dispatched_at'
    ];

    protected $casts = ['closed_at' => 'datetime', 'assigned_to_pallet_at' => 'datetime', 'dispatched_at' => 'datetime',];

    // --- Relaciones ---

    public function container()     { return $this->belongsTo(Container::class); }
    public function containerItem() { return $this->belongsTo(ContainerItem::class); }
    public function pallet()        { return $this->belongsTo(Pallet::class); }
    public function creator()       { return $this->belongsTo(User::class, 'created_by'); }
    public function pickingOrder() 
    { 
        return $this->belongsTo(PickingOrder::class); 
    }

    // --- Accesores ---

    public function getMissingAttribute(): int
    {
        return $this->expected_qty - $this->quantity;
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->quantity >= $this->expected_qty;
    }

    public function getIsOriginalAttribute(): bool
    {
        return $this->source === 'contenedor';
    }

    // --- Generadores de código ---

    /**
     * Código para cajas de reempaque: CAJ-XXXXXX-0001
     */
    public static function generateBoxCode(Container $container, int $sequence): string
    {
        $suffix = strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $container->container_number), -6));
        return sprintf('CAJ-%s-%04d', $suffix, $sequence);
    }

    /**
     * Código para cajas originales del contenedor: REC-XXXXXX-0001
     */
    public static function generateOriginalBoxCode(Container $container, int $sequence): string
    {
        $suffix = strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $container->container_number), -6));
        return sprintf('REC-%s-%04d', $suffix, $sequence);
    }

    // --- Estado ---

    public function isAssignedToPallet(): bool { return $this->pallet_id !== null; }

    public function close(): void
    {
        $this->update(['status' => 'cerrada', 'closed_at' => now()]);
    }

    public function assignToPallet(Pallet $pallet): void
    {
        $this->update([
            'pallet_id'              => $pallet->id,
            'status'                 => 'en_tarima',
            'assigned_to_pallet_at'  => now(), // ← NUEVO: registra cuándo se asignó
        ]);
    }

    public function removeFromPallet(): void
    {
        $this->update([
            'pallet_id'              => null,
            'status'                 => 'cerrada',
            'assigned_to_pallet_at'  => null, // ← limpiamos el timestamp
        ]);
    }

    // --- Scopes ---

    public function scopeAvailableForPallet($query)
    {
        return $query->where('status', 'cerrada')->whereNull('pallet_id');
    }

    public function scopeOriginal($query)
    {
        return $query->where('source', 'contenedor');
    }

    public function scopeRepacked($query)
    {
        return $query->where('source', 'reempaque');
    }
}
