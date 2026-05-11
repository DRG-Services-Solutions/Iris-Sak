<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->string('pallet_code')->unique();
            $table->enum('status', ['abierta', 'cerrada', 'despachado'])->default('abierta');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->unsignedTinyInteger('maquila_station')->nullable()->default(null);
            $table->timestamp('maquila_started_at')->nullable();
            $table->timestamp('maquila_completed_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};
