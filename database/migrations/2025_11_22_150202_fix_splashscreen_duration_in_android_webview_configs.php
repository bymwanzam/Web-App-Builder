<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix splashscreen_duration values that were incorrectly set to 2000 (milliseconds)
     * instead of 3 (seconds). This was a regression bug introduced in the Android WebView
     * platform migration where the default was changed from 3 to 2000.
     */
    public function up(): void
    {
        // Skip if table doesn't exist (fresh migration scenario)
        if (! Schema::hasTable('android_webview_configs')) {
            return;
        }

        // Fix any existing apps with duration of 2000 to the correct default of 3
        DB::table('android_webview_configs')
            ->where('splashscreen_duration', 2000)
            ->update(['splashscreen_duration' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert the change (not recommended as 2000 was incorrect)
        // DB::table('android_webview_configs')
        //     ->where('splashscreen_duration', 3)
        //     ->update(['splashscreen_duration' => 2000]);
    }
};
