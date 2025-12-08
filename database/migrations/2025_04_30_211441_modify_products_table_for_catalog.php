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
        Schema::table('products', function (Blueprint $table) {
            
            // 1. Eliminar la columna EPC (ya no pertenece al catálogo)
            $table->dropColumn('epc');

            // 2. Asegurar que 'barcode' sea único (si no lo era o se quitó)
            // Si ya tenías 'unique' en la creación original y no lo quitaste, esta línea dará error o no hará nada.
            // Si lo quitaste o no estabas seguro, puedes añadirla. Comenta/descomenta según tu caso.
            // $table->unique('barcode');

            // 3. (Opcional pero recomendado) Añadir campo para saber si se rastrea individualmente
            $table->boolean('is_individually_tracked')->default(true)->after('barcode');

            // 4. (Opcional) Eliminar otras columnas si no pertenecen al catálogo (ej. status, current_station si las hubieras añadido)
            $table->dropColumn(['status', 'current_station']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Revertir los cambios en orden inverso
            // $table->string('status')->nullable(); // Re-añadir si se borró
            // $table->string('current_station')->nullable(); // Re-añadir si se borró
            $table->dropColumn('is_individually_tracked'); // Eliminar si se añadió
            // $table->dropUnique(['barcode']); // Quitar unique si se añadió en up()
            $table->string('epc', 24)->unique()->nullable()->after('barcode'); // Re-añadir EPC


        });
    }
};
