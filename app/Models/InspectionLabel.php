<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InspectionLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'container_item_id',
        'label_code',
        'piece_number',
        'inspection_status',
        'inspected_by',
        'inspected_at',
        'notes',
        'printed',
    ];

    protected $casts = [
        'inspected_at' => 'datetime',
        'printed' => 'boolean',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function containerItem()
    {
        return $this->belongsTo(ContainerItem::class);
    }

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    /**
     * Genera un código de etiqueta único basado en el contenedor.
     */
    public static function generateLabelCode(Container $container, int $pieceNumber): string
    {
        $prefix = strtoupper(Str::substr($container->container_number, -6));
        return sprintf('SAK-%s-%04d', $prefix, $pieceNumber);
    }

    public function markAsInspected(string $status, ?int $userId = null, ?string $notes = null): void
    {
        $this->update([
            'inspection_status' => $status,
            'inspected_by' => $userId,
            'inspected_at' => now(),
            'notes' => $notes,
        ]);
    }
}
