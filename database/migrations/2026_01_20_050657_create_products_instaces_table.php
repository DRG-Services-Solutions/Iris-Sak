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
        Schema::create('product_instances', function (Blueprint $table) {
            $table->id();

            // Clave Foránea a la tabla 'products' (el catálogo)
            $table->foreignId('product_id')
            ->constrained('products') 
            ->onDelete('cascade'); 

            // El EPC único de esta instancia física
            $table->string('epc', 24)->unique();
        
            // Estado y Ubicación Actual (Indexados para búsquedas rápidas)
            $table->string('status')->default('En Stock')->index(); 
            $table->string('current_station')->default('Almacén')->index(); 
            
            // --- Columnas Opcionales / Adicionales ---
            $table->text('notes')->nullable(); 

            $table->foreignId('user_id')
                  ->nullable() 
                  ->constrained('users') 
                  ->nullOnDelete(); 

             $table->foreignId('work_order_id')
                  ->nullable() 
                  ->constrained('work_orders') 
                  ->nullOnDelete();

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
