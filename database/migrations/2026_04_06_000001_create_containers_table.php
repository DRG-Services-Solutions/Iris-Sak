<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('container_number')->unique();
            $table->string('container_seal_number')->nullable();
            $table->string('packing_list_number')->nullable();
            $table->string('supplier')->nullable();
            $table->string('buyer')->nullable();
            $table->string('origin_country')->nullable();
            $table->string('transport_mode')->nullable();
            $table->string('port_loading')->nullable();
            $table->string('port_discharge')->nullable();
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->integer('declared_qty')->default(0);
            $table->integer('received_qty')->default(0);
            $table->integer('total_cartons')->default(0);
            $table->decimal('total_cbm', 10, 3)->default(0);
            $table->decimal('total_net_weight_kg', 10, 2)->default(0);
            $table->decimal('total_gross_weight_kg', 10, 2)->default(0);
            $table->enum('customs_status', ['pendiente', 'en_revision', 'liberado', 'retenido'])->default('pendiente');
            $table->enum('status', ['abierto', 'en_proceso', 'cerrado'])->default('abierto');
            $table->string('packing_list_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
