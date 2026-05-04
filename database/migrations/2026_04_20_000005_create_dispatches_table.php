<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number')->unique();
            $table->foreignId('picking_order_id')->constrained('picking_orders');
            $table->enum('transport_type', ['5ta_rueda', 'torton', 'camioneta', 'otro'])->default('camioneta');
            $table->string('driver_name')->nullable();
            $table->string('plates')->nullable();
            $table->string('destination');
            $table->enum('status', ['preparando', 'cargado', 'despachado', 'cancelado'])->default('preparando');
            $table->foreignId('dispatched_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
