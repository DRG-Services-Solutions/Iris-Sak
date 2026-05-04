<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContainerItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id', 'item_number', 'product_code', 'barcode',
        'product_description', 'product_description_cn',
        'declared_qty', 'received_qty',
        'cbm', 'net_weight_kg', 'gross_weight_kg',
        'package_type', 'carton_numbers', 'carton_count',
        'received_cartons', 'status', 'notes',
    ];

    protected $casts = [
        'cbm' => 'decimal:3',
        'net_weight_kg' => 'decimal:2',
        'gross_weight_kg' => 'decimal:2',
    ];

    // --- Relaciones ---

    public function container()       { return $this->belongsTo(Container::class); }
    public function inspectionLabels(){ return $this->hasMany(InspectionLabel::class); }
    public function boxes()           { return $this->hasMany(Box::class); }
    public function originalBoxes()   { return $this->hasMany(Box::class)->where('source', 'contenedor'); }
    public function repackedBoxes()   { return $this->hasMany(Box::class)->where('source', 'reempaque'); }

    // --- Accesores ---

    public function getPiecesPerCartonAttribute(): int
    {
        if ($this->carton_count <= 0) return $this->declared_qty;
        return (int) ceil($this->declared_qty / $this->carton_count);
    }

    public function getCartonDifferenceAttribute(): int
    {
        return $this->carton_count - $this->received_cartons;
    }

    public function getDifferenceAttribute(): int
    {
        return $this->declared_qty - $this->received_qty;
    }

    public function getCartonNumbersArrayAttribute(): array
    {
        if (empty($this->carton_numbers)) return [];
        return array_map('trim', explode(',', $this->carton_numbers));
    }

    // --- Recepción por cajas ---

    /**
     * Actualizar recepción por cajas.
     * Auto-genera registros Box con source='contenedor' para que
     * puedan asignarse a tarimas directamente.
     */
    public function updateReceivedCartons(int $cartons, ?int $userId = null): void
    {
        $this->update([
            'received_cartons' => $cartons,
            'received_qty'     => $cartons * $this->pieces_per_carton,
        ]);

        $this->syncOriginalBoxes($cartons, $userId);
        $this->evaluateStatus();
    }

    /**
     * Sincroniza los registros Box originales del contenedor.
     * Si se reciben más cajas → crea las que faltan.
     * Si se reciben menos → elimina las sobrantes (que no estén en tarima).
     */
    private function syncOriginalBoxes(int $targetCount, ?int $userId = null): void
    {
        $container = $this->container;
        $currentOriginals = $this->originalBoxes()->orderBy('box_code')->get();
        $currentCount = $currentOriginals->count();

        if ($targetCount > $currentCount) {
            // Crear las cajas que faltan
            $lastSeq = Box::where('container_id', $container->id)
                ->where('source', 'contenedor')->count();

            $piecesPerCarton = $this->pieces_per_carton;

            for ($i = 1; $i <= ($targetCount - $currentCount); $i++) {
                Box::create([
                    'container_id'      => $container->id,
                    'container_item_id' => $this->id,
                    'box_code'          => Box::generateOriginalBoxCode($container, $lastSeq + $i),
                    'source'            => 'contenedor',
                    'expected_qty'      => $piecesPerCarton,
                    'quantity'          => $piecesPerCarton,
                    'status'            => 'cerrada',
                    'created_by'        => $userId,
                    'closed_at'         => now(),
                ]);
            }
        } elseif ($targetCount < $currentCount) {
            // Eliminar las sobrantes (solo las que NO están en tarima)
            $toRemove = $currentCount - $targetCount;
            $removable = $currentOriginals->filter(fn($b) => !$b->isAssignedToPallet())
                ->sortByDesc('id')
                ->take($toRemove);

            Box::whereIn('id', $removable->pluck('id'))->delete();
        }
    }

    /**
     * Evalúa estatus según cajas recibidas vs declaradas.
     */
    public function evaluateStatus(): void
    {
        if ($this->received_cartons === 0) {
            $this->update(['status' => 'pendiente']);
        } elseif ($this->received_cartons === $this->carton_count) {
            $this->update(['status' => 'conforme']);
        } elseif ($this->received_cartons < $this->carton_count) {
            $this->update(['status' => 'faltante']);
        } else {
            $this->update(['status' => 'sobrante']);
        }
    }
}
