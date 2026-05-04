<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'client_name', 'destination', 'status',
        'priority', 'created_by', 'assigned_to', 'notes',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function items()      { return $this->hasMany(PickingOrderItem::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function assignee()   { return $this->belongsTo(User::class, 'assigned_to'); }
    public function dispatch()   { return $this->hasOne(Dispatch::class); }

    public function getTotalPalletsAttribute(): int { return $this->items()->count(); }
    public function getTotalPiecesAttribute(): int
    {
        return $this->items()->with('pallet.boxes')->get()
            ->sum(fn($item) => $item->pallet->boxes->sum('quantity'));
    }

    public static function generateOrderNumber(): string
    {
        $today = now()->format('Ymd');
        $seq = self::whereDate('created_at', today())->count() + 1;
        return sprintf('PIC-%s-%03d', $today, $seq);
    }

    public function start(): void
    {
        $this->update(['status' => 'en_proceso', 'started_at' => now()]);
    }

    public function complete(): void
    {
        $this->update(['status' => 'completado', 'completed_at' => now()]);
    }

    public function scopePending($query)    { return $query->where('status', 'pendiente'); }
    public function scopeInProgress($query) { return $query->where('status', 'en_proceso'); }
}
