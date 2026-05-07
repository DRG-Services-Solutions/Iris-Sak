<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'details' => 'array',
    ];

    // --- RELACIONES WMS ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Relación hacia el artículo específico del contenedor (para reportes por producto)
     */
    public function containerItem(): BelongsTo
    {
        return $this->belongsTo(ContainerItem::class);
    }

    // --- RELACIONES PREVIAS (Maquila / Producción) ---
    
    public function productInstance(): BelongsTo
    {
        return $this->belongsTo(ProductInstance::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    // --- DICCIONARIO DE EVENTOS ---

    // --- SCOPES PARA BÚSQUEDAS JSON (WMS) ---

    /**
     * Buscar logs por una llave específica dentro del JSON 'details'
     * Ejemplo: ActivityLog::whereDetail('container_id', 5)->get();
     */
    public function scopeWhereDetail($query, string $key, $value)
    {
        // Laravel traduce esto automáticamente a sintaxis JSON de MySQL
        return $query->where("details->{$key}", $value);
    }

    /**
     * Diccionario de acciones actualizado
     */
    protected function readableAction(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) { 
                'CONTAINER_CLOSED' => 'Contenedor Cerrado (Inicio Almacenaje)',
                'BOX_DISPATCHED'   => 'Caja Despachada (Fin Almacenaje)',
                'INSTANCE_CREATED_VIA_SCAN' => 'Item Escaneado y Registrado',
                'ORDER_STEP_FINALIZED' => 'Escaneo Finalizado (Movido a Est. 02)',
                default => $this->action 
            },
        );
    }

    
}