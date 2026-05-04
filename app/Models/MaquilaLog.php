<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaquilaLog extends Model
{
    protected $fillable = ['pallet_id', 'from_station', 'to_station', 'changed_by', 'notes'];

    public function pallet()   { return $this->belongsTo(Pallet::class); }
    public function changedBy(){ return $this->belongsTo(User::class, 'changed_by'); }

    public function getFromLabelAttribute(): string {
        return $this->from_station ? 'Estación ' . $this->from_station : 'Sin iniciar';
    }
    public function getToLabelAttribute(): string {
        return $this->to_station ? 'Estación ' . $this->to_station : 'Completado';
    }
}
