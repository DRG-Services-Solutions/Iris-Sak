<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ampliamos la tabla del detalle de la orden
        Schema::table('picking_order_items', function (Blueprint $table) {
            $table->enum('pick_type', ['full_pallet', 'partial'])->default('full_pallet')->after('pallet_id');
            $table->foreignId('container_item_id')->nullable()->constrained('container_items')->nullOnDelete()->after('pick_type');
            $table->integer('quantity')->nullable()->after('container_item_id'); // Cantidad de cajas a surtir
        });

        // 2. Vinculamos las cajas físicas a la orden para trazabilidad perfecta
        Schema::table('boxes', function (Blueprint $table) {
            $table->foreignId('picking_order_id')->nullable()->constrained('picking_orders')->nullOnDelete()->after('pallet_id');
        });
    }

    public function down(): void
    {
        Schema::table('picking_order_items', function (Blueprint $table) {
            $table->dropForeign(['container_item_id']);
            $table->dropColumn(['pick_type', 'container_item_id', 'quantity']);
        });

        Schema::table('boxes', function (Blueprint $table) {
            $table->dropForeign(['picking_order_id']);
            $table->dropColumn(['picking_order_id']);
        });
    }
};