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
        // Update platform column to support 'android-webview'
        // Using Schema builder for database-agnostic approach
        Schema::table('app_builds', function (Blueprint $table) {
            $table->string('platform', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original
        Schema::table('app_builds', function (Blueprint $table) {
            $table->string('platform', 50)->change();
        });
    }
};
