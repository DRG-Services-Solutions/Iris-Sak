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
