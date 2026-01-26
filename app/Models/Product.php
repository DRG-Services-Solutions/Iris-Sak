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
        'is_individually_tracked',
    ];

    public function instances()
    {
        return $this->hasMany(ProductInstance::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }   

    public function getStockAttribute()
    {
        return $this->instances()->whereIn('status', ['En Stock',])->count();
    }

    /**
     * The "booted" method of the model.
     * Se ejecuta cuando el modelo es inicializado.
     */
   

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
