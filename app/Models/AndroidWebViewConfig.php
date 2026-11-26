<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AndroidWebViewConfig extends Model
{
    use HasFactory;

    protected $table = 'android_webview_configs';

    protected $fillable = [
        'app_id',
        // General
        'url',
        'package_name',
        'screen_orientation',
        'app_language',
        'user_agent',
        // Design
        'theme_color',
        'text_color_light',
        'text_color_dark',
        'enable_dynamic_colors',
        'layout_template',
        'progress_indicator',
        'progress_indicator_color',
        'show_app_bar_title',
        'nav_tab_icon_color_light',
        'nav_tab_icon_color_dark',
        'active_tab_color_light',
        'active_tab_color_dark',
        // Drawer
        'drawer_mode',
        'drawer_background_color',
        'drawer_background_image',
        'drawer_logo_enabled',
        'drawer_logo',
        'drawer_logo_light',
        'drawer_logo_dark',
        'drawer_logo_size',
        'drawer_title',
        'drawer_subtitle',
        'drawer_text_theme',
        'drawer_icon_color_light',
        'drawer_icon_color_dark',
        'drawer_active_color_light',
        'drawer_active_color_dark',
        // Icon
        'app_icon',
        'uploaded_icon_zip',
        'icon_extracted_path',
        // Splashscreen
        'splashscreen_background_type',
        'splashscreen_image',
        'splashscreen_background',
        'splashscreen_duration',
        'splashscreen_animation',
        'splashscreen_logo_enabled',
        'splashscreen_logo',
        'splashscreen_logo_size',
        'splashscreen_title',
        'splashscreen_subtitle',
        'splashscreen_text_theme',
        // Navigation
        'navigation_items',
        'drawer_items',
        'app_bar_buttons',
        'home_url',
        'pull_to_refresh',
        'swipe_navigation',
        'preserve_tab_state',
        // Permissions
        'permission_location',
        'permission_camera',
        'permission_storage',
        'permission_record_audio',
        'permission_read_contacts',
        'permission_vibrate',
        // Custom Code
        'custom_css',
        'custom_js',
        // Build
        'build_config',
        'environment_variables',
        'keystore_id',
    ];

    protected $casts = [
        'enable_dynamic_colors' => 'boolean',
        'show_app_bar_title' => 'boolean',
        'drawer_logo_enabled' => 'boolean',
        'drawer_logo_size' => 'integer',
        'splashscreen_duration' => 'integer',
        'splashscreen_logo_enabled' => 'boolean',
        'splashscreen_logo_size' => 'integer',
        'pull_to_refresh' => 'boolean',
        'swipe_navigation' => 'boolean',
        'preserve_tab_state' => 'boolean',
        'permission_location' => 'boolean',
        'permission_camera' => 'boolean',
        'permission_storage' => 'boolean',
        'permission_record_audio' => 'boolean',
        'permission_read_contacts' => 'boolean',
        'permission_vibrate' => 'boolean',
        'navigation_items' => 'array',
        'drawer_items' => 'array',
        'app_bar_buttons' => 'array',
        'build_config' => 'array',
        'environment_variables' => 'array',
    ];

    /**
     * Get the app that owns this config.
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Get the keystore for signing.
     */
    public function keystore(): BelongsTo
    {
        return $this->belongsTo(Keystore::class);
    }
}
