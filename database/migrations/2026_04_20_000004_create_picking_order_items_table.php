<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('picking_order_id')->constrained('picking_orders')->cascadeOnDelete();
            $table->foreignId('pallet_id')->constrained('pallets');
            $table->enum('status', ['pendiente', 'preparado', 'cargado'])->default('pendiente');
            $table->foreignId('picked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('picked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_order_items');
    }
};
