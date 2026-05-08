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

        if (!$location->hasAvailableSpace()) {
            throw new \Exception("La localidad está llena.");
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
    public function moveToStation($station, $userId = null, $notes = null): void
    {
        // 1. Actualizamos la tarima con la nueva estación
        $this->update([
            'maquila_station' => $station,
            'maquila_started_at' => $this->maquila_started_at ?? now(), 
        ]);

        // 2. Creamos el registro en el historial (MaquilaLog)
        // Esto es lo que aprovecha los argumentos extra que enviamos desde el controlador
        if (class_exists(\App\Models\MaquilaLog::class)) {
            \App\Models\MaquilaLog::create([
                'pallet_id'  => $this->id,
                'station'    => $station,
                'changed_by' => $userId,
                'notes'      => $notes,
            ]);
        }
    }

    /**
     * Marca la tarima como completada en su proceso de maquila.
     */
    public function completeMaquila($userId = null, $notes = null): void
    {
        // 1. Actualizamos la tarima marcando la hora de finalización
        $this->update([
            'maquila_completed_at' => now(),
            // 'maquila_station' => null, // (Opcional) Descomenta esto si quieres que al completar se libere el número de estación
        ]);

        // 2. Si manejas un historial (MaquilaLog), aquí es el lugar perfecto para guardarlo
        // Asegúrate de ajustar los nombres de las columnas a como los tengas en tu base de datos
        if (class_exists(\App\Models\MaquilaLog::class)) {
            \App\Models\MaquilaLog::create([
                'pallet_id'  => $this->id,
                'action'     => 'completado', // O el estatus que uses para identificar este evento
                'changed_by' => $userId,
                'notes'      => $notes,
            ]);
        }
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