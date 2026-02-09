<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'barcode',
        'tracking_type',
        'stock',
        'current_station',
        'branch_id',
        'status',
        'epc',
    ];

    public function instances()
    {
        return $this->hasMany(ProductInstance::class);
    }

    //Accesor para contar instancias en stock
    public function getStockCountAttribute()
    {
        return $this->instances()->count();
    }

  

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }   

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    //helpers para tipos de rastreos
    public function isRfidTracked()
    {
        return $this->tracking_type === 'rfid';
    }

    public function isBarcodeTracked()
    {
        return $this->tracking_type === 'barcode';
    }


    //Accesor para stock de ambos tipos
    public function getStockAttribute($value) 

    {
        if ($this->isRfidTracked()) {
            return $this->instances()
                ->whereIn('status', ['En Stock', 'available'])
                ->count();
        }
        
        return $value ?? 0;
    }

    public function updateStock($quantity, $operation = 'increment')
    {
        if ($this->isBarcodeTracked()) {
            if ($operation === 'increment') {
                $this->increment('stock', $quantity);
            } elseif ($operation === 'decrement') {
                $this->decrement('stock', $quantity);
            } else {
                $this->update(['stock' => $quantity]);
            }
        }
    }

    public function hasStock($quantity = 1)
    {
        return $this->stock >= $quantity;
    }
    
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['category'] ?? null, function ($query, $categoryId) {
            $query->where('category_id', $categoryId);
        })->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    




  
   

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = []; // Puedes usar esto si quieres ocultar campos en respuestas JSON

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = []; // Puedes usar esto para convertir tipos de datos (ej. boolean, date)

}
