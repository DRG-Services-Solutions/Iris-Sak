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
        Schema::table('product_instances', function (Blueprint $table) {
            $table->foreignId('work_order_id')
                  ->nullable() // ¿Puede una instancia existir sin orden? Si no, quita nullable()
                  ->after('product_id') // O donde prefieras
                  ->constrained('work_orders') // Vincula a la tabla work_orders
                  ->nullOnDelete(); // Si se borra la orden, pone este campo a NULL (o usa cascade/restrict)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_instances', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']); // Borra la restricción de clave foránea
            $table->dropColumn('work_order_id'); // Borra la columna

        });
    }
};
