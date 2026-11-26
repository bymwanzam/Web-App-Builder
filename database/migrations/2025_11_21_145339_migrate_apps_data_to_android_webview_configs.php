<?php

use App\Models\AndroidWebViewConfig;
use App\Models\App;
use App\Models\Plugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get Android WebView plugin ID
        $androidPlugin = Plugin::where('slug', 'android-webview')->first();

        if (! $androidPlugin) {
            // If plugin doesn't exist yet, try 'android'
            $androidPlugin = Plugin::where('slug', 'android')->first();
        }

        if (! $androidPlugin) {
            // Plugin not installed yet (fresh migration) - skip migration
            // This is expected behavior on fresh installs with no existing apps
            return;
        }

        // Migrate all existing apps
        App::where('platform_plugin_id', $androidPlugin->id)->chunk(100, function ($apps) {
            foreach ($apps as $app) {
                $this->migrateAppConfig($app);
            }
        });
    }

    /**
     * Migrate a single app's configuration.
     */
    protected function migrateAppConfig(App $app): void
    {
        // Helper to ensure JSON fields are arrays
        $ensureArray = function ($value) {
            if (is_array($value)) {
                return $value;
            }
            if (is_string($value)) {
                $decoded = json_decode($value, true);

                return $decoded ?? [];
            }

            return [];
        };

        // Extract platform_config JSON if it exists
        $platformConfig = is_string($app->platform_config)
            ? json_decode($app->platform_config, true)
            : (is_array($app->platform_config) ? $app->platform_config : []);

        // Extract package_name from multiple sources
        $packageName = $platformConfig['package_name'] ?? null
                    ?? $app->app_id
                    ?? 'com.example.'.Str::slug($app->name);

        // Extract keystore_id from platform_config
        $keystoreId = $platformConfig['keystore_id'] ?? null;

        // Create config record
        AndroidWebViewConfig::create([
            'app_id' => $app->id,

            // General
            'url' => $app->url ?? '',
            'package_name' => $packageName,
            // Map 'auto' to 'system' for screen_orientation (new schema uses 'system')
            'screen_orientation' => in_array($app->screen_orientation, ['portrait', 'landscape', 'system'])
                ? $app->screen_orientation
                : ($app->screen_orientation === 'auto' ? 'system' : 'system'),
            'app_language' => $app->app_language ?? 'en',
            'admin_email' => $app->admin_email,
            'user_agent' => $app->user_agent,

            // Design
            'theme_color' => $app->theme_color ?? '#007AFF',
            'enable_dynamic_colors' => $app->enable_dynamic_colors ?? false,
            // Keep 'automatic' as valid value for title_color
            'title_color' => in_array($app->title_color, ['automatic', 'light', 'dark'])
                ? $app->title_color
                : 'automatic',
            'layout_template' => $app->layout_template ?? 'app_bar_tabs',
            // Map 'disable' to 'none' and 'linear'/'circular' are valid
            'progress_indicator' => in_array($app->progress_indicator, ['circular', 'linear', 'none'])
                ? $app->progress_indicator
                : ($app->progress_indicator === 'disable' ? 'none' : 'circular'),
            'progress_indicator_color' => $app->progress_indicator_color,
            'show_app_bar_title' => $app->show_app_bar_title ?? true,
            'nav_tab_icon_color' => $app->nav_tab_icon_color,
            'active_tab_color' => $app->active_tab_color,

            // Drawer (keep valid values, convert legacy to 'default')
            'drawer_mode' => in_array($app->drawer_mode, ['default', 'color', 'image'])
                ? $app->drawer_mode
                : 'default', // Convert any legacy 'overlay'/'permanent' values to 'default'
            'drawer_background_color' => $app->drawer_background_color,
            'drawer_background_image' => $app->drawer_background_image,
            'drawer_logo_enabled' => $app->drawer_logo_enabled ?? false,
            'drawer_logo' => $app->drawer_logo,
            'drawer_logo_light' => $app->drawer_logo_light,
            'drawer_logo_dark' => $app->drawer_logo_dark,
            'drawer_logo_size' => $app->drawer_logo_size ?? 48,
            'drawer_title' => $app->drawer_title,
            'drawer_subtitle' => $app->drawer_subtitle,
            'drawer_text_theme' => $app->drawer_text_theme ?? 'dark',

            // Icon
            'app_icon' => $app->app_icon,
            'uploaded_icon_zip' => $app->uploaded_icon_zip,
            'icon_extracted_path' => $app->icon_extracted_path,

            // Splashscreen
            'splashscreen_background_type' => $app->splashscreen_background_type ?? 'color',
            'splashscreen_image' => $app->splashscreen_image,
            'splashscreen_background' => $app->splashscreen_background ?? '#007AFF',
            'splashscreen_duration' => $app->splashscreen_duration ?? 2000,
            // Map invalid animation values to 'fade'
            'splashscreen_animation' => in_array($app->splashscreen_animation, ['fade', 'zoom', 'none'])
                ? $app->splashscreen_animation
                : 'fade',
            'splashscreen_logo_enabled' => $app->splashscreen_logo_enabled ?? true,
            'splashscreen_logo' => $app->splashscreen_logo,
            'splashscreen_logo_size' => $app->splashscreen_logo_size ?? 120,
            'splashscreen_title' => $app->splashscreen_title,
            'splashscreen_subtitle' => $app->splashscreen_subtitle,
            'splashscreen_text_theme' => $app->splashscreen_text_theme ?? 'light',

            // Navigation
            'navigation_items' => $ensureArray($app->navigation_items),
            'drawer_items' => $ensureArray($app->drawer_items),
            'app_bar_buttons' => $ensureArray($app->app_bar_buttons),
            'home_url' => $app->home_url,
            'pull_to_refresh' => $app->pull_to_refresh ?? true,
            'swipe_navigation' => $app->swipe_navigation ?? true,
            'preserve_tab_state' => $app->preserve_tab_state ?? false,

            // Permissions (prioritize columns over platform_config)
            'permission_location' => $app->permission_location ?? ($platformConfig['permissions']['location'] ?? false),
            'permission_camera' => $app->permission_camera ?? ($platformConfig['permissions']['camera'] ?? false),
            'permission_storage' => $app->permission_storage ?? ($platformConfig['permissions']['storage'] ?? false),
            'permission_record_audio' => $app->permission_record_audio ?? ($platformConfig['permissions']['record_audio'] ?? false),
            'permission_read_contacts' => $app->permission_read_contacts ?? ($platformConfig['permissions']['read_contacts'] ?? false),
            'permission_vibrate' => $app->permission_vibrate ?? ($platformConfig['permissions']['vibrate'] ?? false),

            // Custom Code
            'custom_css' => $app->custom_css,
            'custom_js' => $app->custom_js,

            // Build
            'build_config' => $ensureArray($app->build_config),
            'environment_variables' => $ensureArray($app->environment_variables),

            // Signing
            'keystore_id' => $keystoreId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migration is irreversible - data loss would occur
        throw new \Exception('Cannot reverse this migration without data loss. Restore from backup instead.');
    }
};
