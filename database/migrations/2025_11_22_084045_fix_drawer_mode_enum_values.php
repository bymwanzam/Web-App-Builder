<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if table doesn't exist (fresh migration scenario)
        if (! Schema::hasTable('android_webview_configs')) {
            return;
        }

        // Step 1: Convert any existing 'overlay' or 'permanent' values to 'default'
        DB::table('android_webview_configs')
            ->whereIn('drawer_mode', ['overlay', 'permanent'])
            ->update(['drawer_mode' => 'default']);

        // Step 2: Alter the enum column definition
        // Note: SQLite doesn't support ALTER COLUMN for enums, so we need to check the driver
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // For SQLite, we need to recreate the table (handled by fresh migration)
            // This migration is primarily for updating data, column change handled by fresh migration
            return;
        }

        // For MySQL/PostgreSQL, alter the column
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->enum('drawer_mode', ['default', 'color', 'image'])
                ->default('default')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip if table doesn't exist
        if (! Schema::hasTable('android_webview_configs')) {
            return;
        }

        // Convert 'color' and 'image' back to 'default' before changing enum
        DB::table('android_webview_configs')
            ->whereIn('drawer_mode', ['color', 'image'])
            ->update(['drawer_mode' => 'default']);

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        // Restore old enum values
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->enum('drawer_mode', ['overlay', 'permanent'])
                ->default('overlay')
                ->change();
        });

        // Convert 'default' back to 'overlay'
        DB::table('android_webview_configs')
            ->where('drawer_mode', 'default')
            ->update(['drawer_mode' => 'overlay']);
    }
};
