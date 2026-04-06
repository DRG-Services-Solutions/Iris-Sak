<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_labels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('container_id')->constrained('containers')->cascadeOnDelete();
            $table->foreignId('container_item_id')->nullable()->constrained('container_items')->nullOnDelete();
            $table->string('label_code')->unique();
            $table->integer('piece_number')->default(1);
            $table->enum('inspection_status', ['conforme', 'pendiente', 'con_diferencia'])->default('pendiente');
            $table->foreignId('inspected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('printed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_labels');
    }
};
