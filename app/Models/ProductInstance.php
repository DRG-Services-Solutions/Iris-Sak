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
        'product_id',       
        'work_order_id', 
        'status',           
        'current_station',  
        'notes',            
        'user_id',          
        
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
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtiene el usuario asociado a esta instancia (si existe).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); 
    }
    /**
     * Obtiene la orden de trabajo a la que pertenece esta instancia.
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }   
}
