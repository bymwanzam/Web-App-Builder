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
        // Skip if table doesn't exist (fresh migration scenario - columns are in create migration)
        if (! Schema::hasTable('android_webview_configs')) {
            return;
        }

        // Skip if columns already exist (plugin create migration includes them now)
        if (Schema::hasColumn('android_webview_configs', 'drawer_icon_color_light')) {
            return;
        }

        Schema::table('android_webview_configs', function (Blueprint $table) {
            // Drawer item icon colors (inactive state)
            $table->string('drawer_icon_color_light')->nullable()->after('drawer_text_theme');
            $table->string('drawer_icon_color_dark')->nullable()->after('drawer_icon_color_light');

            // Drawer item active/selected colors
            $table->string('drawer_active_color_light')->nullable()->after('drawer_icon_color_dark');
            $table->string('drawer_active_color_dark')->nullable()->after('drawer_active_color_light');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('android_webview_configs')) {
            return;
        }

        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->dropColumn([
                'drawer_icon_color_light',
                'drawer_icon_color_dark',
                'drawer_active_color_light',
                'drawer_active_color_dark',
            ]);
        });
    }
};
