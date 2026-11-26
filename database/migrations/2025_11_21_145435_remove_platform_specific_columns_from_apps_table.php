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
        $columns = [
            // General
            'url', 'app_id', 'screen_orientation', 'app_language', 'admin_email', 'user_agent', 'platform_config',
            // Design
            'theme_color', 'enable_dynamic_colors', 'title_color', 'layout_template', 'progress_indicator',
            'progress_indicator_color', 'show_app_bar_title', 'nav_tab_icon_color', 'active_tab_color', 'build_config',
            // Drawer
            'drawer_mode', 'drawer_background_color', 'drawer_background_image', 'drawer_logo_enabled', 'drawer_logo',
            'drawer_logo_light', 'drawer_logo_dark', 'drawer_logo_size', 'drawer_title', 'drawer_subtitle', 'drawer_text_theme',
            // Icon
            'app_icon', 'uploaded_icon_zip', 'icon_extracted_path',
            // Splashscreen
            'splashscreen_background_type', 'splashscreen_image', 'splashscreen_background', 'splashscreen_duration',
            'splashscreen_animation', 'splashscreen_logo_enabled', 'splashscreen_logo', 'splashscreen_logo_size',
            'splashscreen_title', 'splashscreen_subtitle', 'splashscreen_text_theme',
            // Navigation
            'navigation_items', 'drawer_items', 'app_bar_buttons', 'home_url', 'pull_to_refresh', 'swipe_navigation', 'preserve_tab_state',
            // Permissions
            'permission_location', 'permission_camera', 'permission_storage', 'permission_record_audio',
            'permission_read_contacts', 'permission_vibrate',
            // Custom Code
            'custom_css', 'custom_js',
            // Environment
            'environment_variables',
        ];

        // Only drop columns that exist
        Schema::table('apps', function (Blueprint $table) use ($columns) {
            $existingColumns = Schema::getColumnListing('apps');
            $columnsToDrop = array_intersect($columns, $existingColumns);

            if (! empty($columnsToDrop)) {
                // Drop any unique indexes before dropping columns
                if (in_array('app_id', $columnsToDrop)) {
                    try {
                        $table->dropUnique('apps_app_id_unique');
                    } catch (\Exception $e) {
                        // Index might not exist, continue
                    }
                }

                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse migration: recreate columns (but data is lost)
        throw new \Exception('Cannot reverse this migration. Data has been migrated to android_webview_configs.');
    }
};
