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
        Schema::table('tools', function (Blueprint $table) {
            // Adding the 'epc' column to the 'tools' table
            // The column is a string with a maximum length of 24 characters
            $table->string('epc', 24)->unique()->nullable()->after('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Dropping the 'epc' column from the 'tools' table
            // This is the reverse of the 'up' method
            // It ensures that if we roll back the migration, the column is removed
            $table->dropColumn('epc');

        });
    }
};
