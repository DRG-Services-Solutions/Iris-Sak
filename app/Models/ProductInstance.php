<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\WorkOrder;
use App\Models\User;


class ProductInstance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * Laravel infiere 'product_instances' por defecto, pero a veces
     * es bueno ser explícito si renombras cosas a menudo. Es opcional.
     *
     * @var string
     */
    // protected $table = 'product_instances'; // Opcional

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',       // Necesitamos poder asignar a qué producto pertenece
        'work_order_id', // <-- AÑADIR ESTA LÍNEA
        'status',           // El estado inicial o asignado
        'current_station',  // La estación inicial o asignada
        'notes',            // Notas opcionales
        'user_id',          // Usuario asociado opcional
        // 'epc' NO va aquí porque se autogenera y no queremos asignarlo masivamente
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = []; // Opcional

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = []; // Opcional, podríamos castear status/station a Enums más adelante

    /**
     * The "booted" method of the model.
     * Aquí movemos la lógica de generación del EPC.
     */
    protected static function booted(): void
    {
        static::creating(function (ProductInstance $instance) {
            // Generamos siempre un EPC único en mayúsculas al crear una nueva instancia
            $instance->epc = strtoupper(bin2hex(random_bytes(12)));
        });
    }

    // --- Definición de Relaciones Eloquent ---

    /**
     * Obtiene el tipo de producto (del catálogo) al que pertenece esta instancia.
     */
    public function product(): BelongsTo
    {
        // Una instancia pertenece a un producto del catálogo.
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtiene el usuario asociado a esta instancia (si existe).
     */
    public function user(): BelongsTo
    {
        // Una instancia puede pertenecer opcionalmente a un usuario.
        return $this->belongsTo(User::class); // Asegúrate de importar App\Models\User si no lo hace automáticamente tu IDE
    }
    /**
     * Obtiene la orden de trabajo a la que pertenece esta instancia.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
