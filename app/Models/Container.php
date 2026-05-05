<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_number',
        'container_seal_number',
        'packing_list_number',
        'supplier',
        'buyer',
        'origin_country',
        'transport_mode',
        'port_loading',
        'port_discharge',
        'etd',
        'eta',
        'declared_qty',
        'received_qty',
        'total_cartons',
        'total_cbm',
        'total_net_weight_kg',
        'total_gross_weight_kg',
        'customs_status',
        'status',
        'packing_list_path',
        'notes',
        'received_by',
        'received_at',
        'branch_id',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'etd' => 'date',
        'eta' => 'date',
        'total_cbm' => 'decimal:3',
        'total_net_weight_kg' => 'decimal:2',
        'total_gross_weight_kg' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ContainerItem::class);
    }

    public function inspectionLabels()
    {
        return $this->hasMany(InspectionLabel::class);
    }

    public function boxes()
    {
        return $this->hasMany(Box::class);
    }

    public function pallets()
    {
        return $this->hasMany(Pallet::class);
    }

    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getDifferenceAttribute(): int
    {
        return $this->declared_qty - $this->received_qty;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'abierto');
    }

    public function scopePendingCustoms($query)
    {
        return $query->whereIn('customs_status', ['pendiente', 'en_revision']);
    }

    public function recalculateFromItems(): void
    {
        $this->update([
            'declared_qty'         => $this->items()->sum('declared_qty'),
            'received_qty'         => $this->items()->sum('received_qty'),
            'total_cbm'            => $this->items()->sum('cbm'),
            'total_net_weight_kg'  => $this->items()->sum('net_weight_kg'),
            'total_gross_weight_kg'=> $this->items()->sum('gross_weight_kg'),
            'total_cartons'        => $this->items()->sum('carton_count'),
        ]);
    }

    public function canClose(): bool
    {
        return $this->items()->where('status', 'pendiente')->doesntExist();
    }
}
