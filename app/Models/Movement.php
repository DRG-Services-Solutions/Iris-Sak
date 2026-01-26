<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    /** @use HasFactory<\Database\Factories\MovementFactory> */
    use HasFactory;
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'user_id',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

}
