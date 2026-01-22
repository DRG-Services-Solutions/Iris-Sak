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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Quién realizó la acción (puede ser nulo si es una acción del sistema)
            $table->foreignId('user_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete(); // Si se borra el usuario, el log no se borra, solo queda sin user_id

            // Qué instancia específica se afectó (puede ser nulo si la acción es sobre una orden completa, etc.)
            $table->foreignId('product_instance_id')
            ->nullable()
            ->constrained('product_instances') // Asegúrate que tu tabla de instancias se llame así
            ->nullOnDelete(); // Si se borra la instancia, el log no se borra
      
            // A qué orden de trabajo estaba asociada la acción (puede ser nulo)
            $table->foreignId('work_order_id')
            ->nullable()
            ->constrained('work_orders')
            ->nullOnDelete(); // Si se borra la orden, el log no se borra
      
            // Qué acción se realizó (indexado para búsquedas rápidas)
            $table->string('action')->index();
            // Ejemplos: 'INSTANCE_CREATED', 'STATUS_UPDATED', 'STATION_CHANGED', 'ORDER_FINALIZED', 'ARRIVED_STATION_02'

            // Detalles adicionales en formato JSON (flexible)
            $table->json('details')->nullable();
            // Ejemplo: {"old_status": "Check-In", "new_status": "StandBy", "station": "02"}
            // Ejemplo: {"product_id": 5, "epc": "ABC..."} // Para INSTANCE_CREATED

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
