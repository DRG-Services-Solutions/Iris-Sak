<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('maquila_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pallet_id')->constrained('pallets')->cascadeOnDelete();
            $table->unsignedTinyInteger('from_station')->nullable();
            $table->unsignedTinyInteger('to_station')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('maquila_logs'); }
};
