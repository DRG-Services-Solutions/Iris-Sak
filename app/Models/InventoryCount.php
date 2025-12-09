<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folio',
        'type',
        'station',
        'status',
        'expected_count',
        'found_count',
        'discrepancy_count',
        'detected_epcs',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'detected_epcs' => 'array',
        'expected_count' => 'integer',
        'found_count' => 'integer',
        'discrepancy_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Obtiene el usuario que realizó el conteo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Genera el siguiente folio disponible
     */
    public static function generateNextFolio(): string
    {
        $lastCount = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastCount ? ((int) substr($lastCount->folio, 4)) + 1 : 1;
        return 'INV-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Obtiene el estado en formato legible
     */
    public function getReadableStatusAttribute(): string
    {
        return match($this->status) {
            'en_proceso' => 'En Proceso',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
            default => $this->status,
        };
    }

    /**
     * Obtiene el tipo en formato legible
     */
    public function getReadableTypeAttribute(): string
    {
        return match($this->type) {
            'general' => 'Inventario General',
            'ciclo' => 'Conteo Cíclico',
            'estacion' => 'Por Estación',
            default => $this->type,
        };
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por tipo
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
