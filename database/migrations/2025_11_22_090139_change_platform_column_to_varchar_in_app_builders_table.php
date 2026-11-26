<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change platform column from ENUM to VARCHAR to support dynamic platform plugins.
     * This allows builders to support any platform identifier without database migrations.
     */
    public function up(): void
    {
        // Convert to VARCHAR(50) to support any platform plugin identifier
        Schema::table('app_builders', function (Blueprint $table) {
            $table->string('platform', 50)->default('android')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to string (no-op, already string)
        Schema::table('app_builders', function (Blueprint $table) {
            $table->string('platform', 50)->default('android')->change();
        });
    }
};
