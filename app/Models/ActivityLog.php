<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * Laravel infiere 'activity_logs', pero podemos ser explícitos.
     * @var string
     */
    // protected $table = 'activity_logs'; // Opcional

    /**
     * Los atributos que no se protegen contra asignación masiva.
     * Alternativamente, podrías usar $fillable y listar los campos.
     * Usar $guarded vacío es común para modelos de log donde controlas la creación.
     * ¡Precaución! Asegúrate de controlar los datos que pasas a ::create() o ::update().
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Muy útil para la columna 'details' de tipo JSON.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'details' => 'array', // Convierte automáticamente JSON a/desde array PHP
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el usuario que realizó la acción (si aplica).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene la instancia de producto afectada (si aplica).
     */
    public function productInstance(): BelongsTo
    {
        // Laravel buscará la columna 'product_instance_id' por defecto
        return $this->belongsTo(ProductInstance::class);
    }

    /**
     * Obtiene la orden de trabajo asociada (si aplica).
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    protected function readableAction(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->action) { // Usa match (PHP 8+) o un switch
                'INSTANCE_CREATED_VIA_SCAN' => 'Item Escaneado y Registrado',
                'ORDER_STEP_FINALIZED' => 'Escaneo Finalizado (Movido a Est. 02)', // Texto más claro
                'ORDER_RELEASED' => 'Orden Liberada / Enviada', // Ejemplo si tuvieras esta acción
                // --- Añade aquí más casos para otras acciones que registres ---
                // 'STATUS_UPDATED' => 'Estado de Item Actualizado',
                // 'STATION_CHANGED' => 'Item Movido de Estación',
                default => $this->action // Si no hay traducción, muestra el código original
            },
        );
    }


    // --- FIN RELACIONES ---

    // No necesitamos timestamps aquí si usamos los de Eloquent (created_at/updated_at)
    // public $timestamps = false; // Descomenta si NO usaste $table->timestamps() en la migración

}
