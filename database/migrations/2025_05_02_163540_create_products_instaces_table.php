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
        Schema::create('products_instaces', function (Blueprint $table) {
            $table->id();

            // Clave Foránea a la tabla 'products' (el catálogo)
            $table->foreignId('product_id')
            ->constrained('products') // Asegura que el product_id exista en la tabla products
            ->onDelete('cascade'); // Opcional: Si se borra un producto del catálogo, se borran todas sus instancias. Alternativa: ->onDelete('restrict') para prevenirlo.

            // El EPC único de esta instancia física
            $table->string('epc', 24)->unique();
        
            // Estado y Ubicación Actual (Indexados para búsquedas rápidas)
            $table->string('status')->default('En Stock')->index(); // Ej: 'En Stock', 'Check-In', 'Estación A', 'En Mantenimiento', etc.
            $table->string('current_station')->default('Almacén')->index(); // Ej: 'Almacén', 'Estación A', 'Check-In', etc.
            
            // --- Columnas Opcionales / Adicionales ---
            $table->text('notes')->nullable(); // Notas específicas sobre esta instancia

            //Usuario asociado (¿último que la usó? ¿responsable?)
            $table->foreignId('user_id')
            ->nullable() // Permite que no siempre haya un usuario asociado
            ->constrained('users') // Asegura que el user_id exista en la tabla users
            ->nullOnDelete(); // Si se borra el usuario, este campo se pone a NULL

            // Fechas de creación y actualización
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_instaces');
    }
};
