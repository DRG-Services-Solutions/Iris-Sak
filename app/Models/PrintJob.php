<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\BelongsTo;

class PrintJob extends Model
{
    /** @use HasFactory<\Database\Factories\PrintJobFactory> */
    use HasFactory;
    protected $fillable = [
        'work_order_id',
        'printer_ip',
        'zpl_data',
        'status',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
