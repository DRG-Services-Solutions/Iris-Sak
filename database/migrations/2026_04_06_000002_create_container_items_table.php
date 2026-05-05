<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('container_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->integer('item_number')->default(0);
            $table->string('product_code')->nullable();       // PRODUCT CODE (SKU interno Miniso)
            $table->string('barcode')->nullable();             // BAR CODE (EAN/UPC)
            $table->string('product_description');             // PRODUCT DESCRIPTION (EN)
            $table->string('product_description_cn')->nullable(); // PRODUCT DESCRIPTION (CN)
            $table->integer('declared_qty')->default(0);       // QUANTITY (PCS)
            $table->integer('received_qty')->default(0);
            $table->decimal('cbm', 10, 3)->default(0);        // TOTAL MEASUREMENT (CBM)
            $table->decimal('net_weight_kg', 10, 2)->default(0);  // TOTAL/N.W KG
            $table->decimal('gross_weight_kg', 10, 2)->default(0); // TOTAL/G.W KG
            $table->string('package_type')->nullable();        // KIND OF PACKAGE (BAG, CARTON)
            $table->text('carton_numbers')->nullable();        // CARTON NO. (lista separada por comas)
            $table->integer('carton_count')->default(0);       // Cuántas cajas trae este item
            $table->integer('received_cartons')->default(0);   // El estatus se determina automáticamente en base a las cantidades, pero lo guardamos para facilitar consultas y reportes
            $table->enum('status', ['pendiente', 'conforme', 'faltante', 'sobrante', 'no_recibido'])->default('pendiente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('container_items');
    }
};
