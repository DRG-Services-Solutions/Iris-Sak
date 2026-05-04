<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('zone')->nullable()->after('name');
            $table->string('aisle')->nullable()->after('zone');
            $table->string('level')->nullable()->after('aisle');
            $table->string('position')->nullable()->after('level');
            $table->enum('type', ['rack', 'piso', 'andén', 'tránsito', 'otro'])->default('rack')->after('position');
            $table->boolean('active')->default(true)->after('type');
            $table->integer('capacity')->nullable()->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['zone', 'aisle', 'level', 'position', 'type', 'active', 'capacity']);
        });
    }
};
