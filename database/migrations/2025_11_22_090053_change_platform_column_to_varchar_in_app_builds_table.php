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
     * This allows the system to accept any platform identifier (android-webview, ios-native, pwa, etc.)
     * without requiring database migrations for each new platform plugin.
     */
    public function up(): void
    {
        // Convert to VARCHAR(50) to support any platform plugin identifier
        Schema::table('app_builds', function (Blueprint $table) {
            $table->string('platform', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to string (no-op, already string)
        Schema::table('app_builds', function (Blueprint $table) {
            $table->string('platform', 50)->change();
        });
    }
};
