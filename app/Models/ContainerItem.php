<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'item_number',
        'product_code',
        'barcode',
        'product_description',
        'product_description_cn',
        'declared_qty',
        'received_qty',
        'cbm',
        'net_weight_kg',
        'gross_weight_kg',
        'package_type',
        'carton_numbers',
        'carton_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'cbm' => 'decimal:3',
        'net_weight_kg' => 'decimal:2',
        'gross_weight_kg' => 'decimal:2',
    ];

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function inspectionLabels()
    {
        return $this->hasMany(InspectionLabel::class);
    }

    public function getDifferenceAttribute(): int
    {
        return $this->declared_qty - $this->received_qty;
    }

    /**
     * Devuelve array de números de caja.
     */
    public function getCartonNumbersArrayAttribute(): array
    {
        if (empty($this->carton_numbers)) return [];
        return array_map('trim', explode(',', $this->carton_numbers));
    }

    public function evaluateStatus(): void
    {
        if ($this->received_qty === 0) {
            $this->update(['status' => 'pendiente']);
        } elseif ($this->received_qty >= $this->declared_qty) {
            $this->update(['status' => 'conforme']);
        } else {
            $this->update(['status' => 'con_diferencia']);
        }
    }
}
