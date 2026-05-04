<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'zone', 'aisle', 'level',
        'position', 'type', 'active', 'capacity', 'pallet_id'
    ];

    protected $casts = ['active' => 'boolean'];
    

    public function pallets() { return $this->hasMany(Pallet::class); }

    public function getFullCodeAttribute(): string
    {
        return collect([$this->zone, $this->aisle, $this->level, $this->position])
            ->filter()->implode('-');
    }

    public function getOccupancyAttribute(): int
    {
        return $this->pallets()->count();
    }

    public function hasPallets(): bool
    {
        return $this->pallets()->exists();
    }

    public function isEmpty(): bool
    {
        return !$this->hasPallets();
    }   

    




    public function scopeActive($query) { return $query->where('active', true); }
}
