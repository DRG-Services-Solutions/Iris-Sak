<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'container_item_id',
        'box_code',
        'quantity',
        'status',
        'pallet_id',
        'created_by',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    // --- Relaciones ---

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function containerItem()
    {
        return $this->belongsTo(ContainerItem::class);
    }

    public function pallet()
    {
        return $this->belongsTo(Pallet::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- Helpers ---

    /**
     * Genera un código de caja único: CAJ-{últimos 6 del contenedor}-{secuencia 4 dígitos}
     */
    public static function generateBoxCode(Container $container, int $sequence): string
    {
        $suffix = strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $container->container_number), -6));
        return sprintf('CAJ-%s-%04d', $suffix, $sequence);
    }

    public function isAssignedToPallet(): bool
    {
        return $this->pallet_id !== null;
    }

    public function close(): void
    {
        $this->update([
            'status'    => 'cerrada',
            'closed_at' => now(),
        ]);
    }

    public function assignToPallet(Pallet $pallet): void
    {
        $this->update([
            'pallet_id' => $pallet->id,
            'status'    => 'en_tarima',
        ]);
    }

    public function removeFromPallet(): void
    {
        $this->update([
            'pallet_id' => null,
            'status'    => 'cerrada',
        ]);
    }

    // --- Scopes ---

    public function scopeAvailableForPallet($query)
    {
        return $query->whereIn('status', ['cerrada'])->whereNull('pallet_id');
    }

    public function scopeByContainer($query, int $containerId)
    {
        return $query->where('container_id', $containerId);
    }
}
