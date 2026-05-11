<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->foreignId('container_item_id')->constrained('container_items')->cascadeOnDelete();
            $table->string('box_code')->unique();
            $table->integer('quantity')->default(0);
            $table->integer('expected_qty')->default(0);
            $table->enum('status', ['abierta', 'cerrada', 'en_tarima', 'despachada'])->default('abierta');
            $table->foreignId('pallet_id')->nullable()->constrained('pallets')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->enum('source', ['contenedor', 'reempaque'])->default('reempaque');
            $table->timestamp('assigned_to_pallet_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
