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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('current_station')->nullable(); //Estacion de trabajo del proceso
            $table->foreignId('branch_id')->constrained()->onDelete('cascade'); //Llave foranea a sucursales   
            $table->string('status')->default('available');
            $table->string('barcode')->unique();
            $table->string('epc', 24)->unique()->nullable(); //Numero autogenerado del tag de radiofrecuencia a codificar en cada producto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
