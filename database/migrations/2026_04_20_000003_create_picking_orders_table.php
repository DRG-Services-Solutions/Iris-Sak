<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('picking_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('client_name')->nullable();
            $table->string('destination')->nullable();
            $table->enum('status', ['pendiente', 'en_proceso', 'completado', 'cancelado'])->default('pendiente');
            $table->enum('priority', ['normal', 'urgente'])->default('normal');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_orders');
    }
};
