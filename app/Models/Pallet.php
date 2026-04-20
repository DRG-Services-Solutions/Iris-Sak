<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'pallet_code',
        'status',
        'created_by',
        'notes',
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

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- Accesores calculados ---

    public function getTotalBoxesAttribute(): int
    {
        return $this->boxes()->count();
    }

    public function getTotalPiecesAttribute(): int
    {
        return $this->boxes()->sum('quantity');
    }

    /**
     * Resumen del contenido: agrupa por artículo y suma piezas.
     */
    public function getContentsSummaryAttribute(): \Illuminate\Support\Collection
    {
        return $this->boxes()
            ->with('containerItem')
            ->get()
            ->groupBy('container_item_id')
            ->map(function ($group) {
                $item = $group->first()->containerItem;
                return (object) [
                    'product_code'        => $item?->product_code,
                    'product_description'  => $item?->product_description ?? 'Sin descripción',
                    'boxes_count'         => $group->count(),
                    'total_pieces'        => $group->sum('quantity'),
                ];
            })->values();
    }

    // --- Helpers ---

    public static function generatePalletCode(Container $container, int $sequence): string
    {
        $suffix = strtoupper(Str::substr(preg_replace('/[^A-Za-z0-9]/', '', $container->container_number), -6));
        return sprintf('TAR-%s-%04d', $suffix, $sequence);
    }

    public function close(): void
    {
        $this->update([
            'status'    => 'cerrada',
            'closed_at' => now(),
        ]);
    }

    // --- Scopes ---

    public function scopeOpen($query)
    {
        return $query->where('status', 'abierta');
    }

    public function scopeByContainer($query, int $containerId)
    {
        return $query->where('container_id', $containerId);
    }
}
