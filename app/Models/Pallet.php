<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'location_id',
        'maquila_station',
        'maquila_started_at',
        'maquila_completed_at',
        
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'maquila_started_at' => 'datetime', // Añadido para que Laravel los trate como fechas
        'maquila_completed_at' => 'datetime', // Añadido para que Laravel los trate como fechas
    ];
    
    //agregado visibilidad para constantes de clase
    public const STATUS_CERRADA = 'cerrada';
    
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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


    //funciones de asignación de tarima a localidad y validación de estado
    public function canBeAssignedToLocation():bool
    {
        return $this->status === self::STATUS_CERRADA;
    }

    public function hasLocation(): bool
    {
        return $this->location_id !== null;
    }

    public function assignToLocation(Location $location)
    {
        if (!$this->canBeAssignedToLocation()) {
            throw new \Exception("Solo tarimas cerradas pueden ser asignadas a una localidad.");
        }
        return $this->update([
            'location_id' => $location->id
        ]);
        
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
        return sprintf('TAR-%s-%04d', $container->container_seal_number, $sequence);
    }

    public function close(): void
    {
        $this->update([
            'status'    => 'cerrada',
            'closed_at' => now(),
        ]);
    }

    // --- Helpers de Maquila ---

    /**
     * Mueve la tarima a una estación específica y registra el inicio si es la primera vez.
     */
    public function moveToStation(int $station): void
    {
        $data = [
            'maquila_station' => $station,
            // Si la tarima estaba completada y la regresaron a una estación, borramos la fecha de completado
            'maquila_completed_at' => null, 
        ];

        // Si es la primera vez que entra a la línea de maquila, registramos la hora de inicio
        if (is_null($this->maquila_started_at)) {
            $data['maquila_started_at'] = now();
        }

        $this->update($data);
    }

    /**
     * Marca la tarima como completada en su proceso de maquila.
     */
    public function completeMaquila(): void
    {
        $this->update([
            'maquila_completed_at' => now(),
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

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'cerrada');
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('location_id');
    }

    // NUEVO SCOPE: Para buscar tarimas en una estación específica
    public function scopeAtStation(Builder $query, int $station): Builder
    {
        return $query->where('maquila_station', $station)
                     ->whereNull('maquila_completed_at'); // Asegura que no cuente las ya terminadas
    }

    // NUEVO SCOPE: Para buscar tarimas que ya terminaron maquila
    public function scopeMaquilaCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('maquila_completed_at');
    }

    // --- Accesores ---
    public function getAvailablePalletAttribute(): bool
    {
        return $this->location_id === null;
    }
}