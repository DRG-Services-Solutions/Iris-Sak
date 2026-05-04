<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pallets', function (Blueprint $table) {
            $table->unsignedTinyInteger('maquila_station')->nullable()->after('status');
            $table->timestamp('maquila_started_at')->nullable()->after('maquila_station');
            $table->timestamp('maquila_completed_at')->nullable()->after('maquila_started_at');
        });
    }
    public function down(): void {
        Schema::table('pallets', function (Blueprint $table) {
            $table->dropColumn(['maquila_station', 'maquila_started_at', 'maquila_completed_at']);
        });
    }
};
