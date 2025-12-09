<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();

            // Usuario que realizó el conteo
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Folio único del conteo (ej: "INV-00001")
            $table->string('folio')->unique();

            // Tipo de inventario: 'general', 'ciclo', 'estacion'
            $table->string('type')->default('general');

            // Ubicación/estación si aplica
            $table->string('station')->nullable();

            // Estado del conteo: 'en_proceso', 'completado', 'cancelado'
            $table->string('status')->default('en_proceso')->index();

            // Cantidad esperada (si se conoce)
            $table->integer('expected_count')->nullable();

            // Cantidad encontrada mediante RFID
            $table->integer('found_count')->default(0);

            // Cantidad de discrepancias
            $table->integer('discrepancy_count')->default(0);

            // EPCs detectados (JSON array)
            $table->json('detected_epcs')->nullable();

            // Notas o comentarios
            $table->text('notes')->nullable();

            // Timestamp de inicio y finalización
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_counts');
    }
};
