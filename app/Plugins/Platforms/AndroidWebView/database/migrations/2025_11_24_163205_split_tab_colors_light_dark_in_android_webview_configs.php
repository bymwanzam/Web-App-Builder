<?php

use App\Models\AndroidWebViewConfig;
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
        Schema::table('android_webview_configs', function (Blueprint $table) {
            // Add new fields with light/dark variants
            $table->string('nav_tab_icon_color_light', 7)->nullable()->after('active_tab_color');
            $table->string('nav_tab_icon_color_dark', 7)->nullable()->after('nav_tab_icon_color_light');
            $table->string('active_tab_color_light', 7)->nullable()->after('nav_tab_icon_color_dark');
            $table->string('active_tab_color_dark', 7)->nullable()->after('active_tab_color_light');
        });

        // Migrate existing data: copy single values to both light/dark
        $configs = AndroidWebViewConfig::all();
        foreach ($configs as $config) {
            $config->update([
                'nav_tab_icon_color_light' => $config->nav_tab_icon_color ?? '#6B7280',
                'nav_tab_icon_color_dark' => $config->nav_tab_icon_color ?? '#6B7280',
                'active_tab_color_light' => $config->active_tab_color ?? '#3B82F6',
                'active_tab_color_dark' => $config->active_tab_color ?? '#3B82F6',
            ]);
        }

        // Drop old single-value fields
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->dropColumn(['nav_tab_icon_color', 'active_tab_color']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('android_webview_configs', function (Blueprint $table) {
            // Restore old fields
            $table->string('nav_tab_icon_color')->nullable()->after('show_app_bar_title');
            $table->string('active_tab_color')->nullable()->after('nav_tab_icon_color');
        });

        // Migrate back: use light mode values
        $configs = AndroidWebViewConfig::all();
        foreach ($configs as $config) {
            $config->update([
                'nav_tab_icon_color' => $config->nav_tab_icon_color_light ?? '#6B7280',
                'active_tab_color' => $config->active_tab_color_light ?? '#3B82F6',
            ]);
        }

        // Drop new fields
        Schema::table('android_webview_configs', function (Blueprint $table) {
            $table->dropColumn([
                'nav_tab_icon_color_light',
                'nav_tab_icon_color_dark',
                'active_tab_color_light',
                'active_tab_color_dark',
            ]);
        });
    }
};
