<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ProductInstance;


class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'user_id',
        'process',
        'station',
        'status',
        'is_audited',
        'started_at',
        'completed_at',
        'is_audited',
    ];

    /**
     * Define los casts para atributos específicos.
     * Es buena práctica castear fechas a objetos Carbon.
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_audited' => 'boolean',
    ];

    /**
     * Obtiene el usuario que inició/posee la orden.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

        /**
     * Obtiene todas las instancias de producto asociadas a esta orden de trabajo.
     */
    public function productInstances(): HasMany
    {
        return $this->hasMany(ProductInstance::class);
    }

}
