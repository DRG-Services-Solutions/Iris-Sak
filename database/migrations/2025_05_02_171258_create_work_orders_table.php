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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique(); // Folio único (ej. 00001) - Veremos cómo generarlo
            $table->foreignId('user_id')->constrained('users'); // Usuario que la creó/inició
            $table->string('process'); // Proceso actual (ej. 'Seleccion/Picking')
            $table->string('station'); // Estación actual (ej. '01')
            $table->string('status')->default('Pendiente Escaneo'); // Estado general (ej. 'Pendiente Escaneo', 'Procesando', 'Completada')
            $table->timestamp('started_at')->nullable(); // Fecha/Hora inicio real del proceso
            $table->timestamp('completed_at')->nullable(); // Fecha/Hora finalización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
