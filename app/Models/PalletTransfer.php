<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalletTransfer extends Model
{
    protected $fillable = [
        'pallet_id', 'from_location_id', 'to_location_id',
        'transferred_by', 'notes',
    ];

    public function pallet()       { return $this->belongsTo(Pallet::class); }
    public function fromLocation() { return $this->belongsTo(Location::class, 'from_location_id'); }
    public function toLocation()   { return $this->belongsTo(Location::class, 'to_location_id'); }
    public function transferredBy(){ return $this->belongsTo(User::class, 'transferred_by'); }
}
