<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AndroidWordPressConfig extends Model
{
    use HasFactory;

    protected $table = 'android_wordpress_configs';

    protected $fillable = [
        'app_id',
        // WordPress Connection
        'site_url',
        'site_id',
        'api_namespace',
        // General
        'package_name',
        'screen_orientation',
        'app_language',
        // Design
        'theme_color',
        'text_color_light',
        'text_color_dark',
        'enable_dynamic_colors',
        'layout_template',
        'layout_style',
        'show_app_bar_title',
        'progress_indicator',
        'progress_indicator_color',
        // Navigation Tab Colors
        'nav_tab_icon_color_light',
        'nav_tab_icon_color_dark',
        'active_tab_color_light',
        'active_tab_color_dark',
        // Content (WordPress-Specific)
        'post_types',
        'categories_filter',
        'tags_filter',
        'posts_per_page',
        'show_author',
        'show_date',
        'show_excerpt',
        'show_featured_image',
        'show_categories',
        'show_reading_time',
        'excerpt_length',
        // Features (WordPress-Specific)
        'enable_comments',
        'enable_search',
        'enable_bookmarks',
        'enable_offline_reading',
        'enable_share',
        'enable_user_auth',
        'enable_push_notifications',
        // Navigation
        'navigation_items',
        'drawer_items',
        'app_bar_buttons',
        'home_screen',
        'pull_to_refresh',
        'swipe_navigation',
        'preserve_tab_state',
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
        // Advanced
        'custom_css',
        'build_config',
        'environment_variables',
        // Push Notifications (Firebase)
        'google_services_json',
        'firebase_service_account',
        // Signing
        'keystore_id',
    ];

    protected $casts = [
        // Design
        'enable_dynamic_colors' => 'boolean',
        'show_app_bar_title' => 'boolean',
        // Content
        'post_types' => 'array',
        'categories_filter' => 'array',
        'tags_filter' => 'array',
        'posts_per_page' => 'integer',
        'show_author' => 'boolean',
        'show_date' => 'boolean',
        'show_excerpt' => 'boolean',
        'show_featured_image' => 'boolean',
        'show_categories' => 'boolean',
        'show_reading_time' => 'boolean',
        'excerpt_length' => 'integer',
        // Features
        'enable_comments' => 'boolean',
        'enable_search' => 'boolean',
        'enable_bookmarks' => 'boolean',
        'enable_offline_reading' => 'boolean',
        'enable_share' => 'boolean',
        'enable_user_auth' => 'boolean',
        'enable_push_notifications' => 'boolean',
        // Navigation
        'navigation_items' => 'array',
        'drawer_items' => 'array',
        'app_bar_buttons' => 'array',
        'pull_to_refresh' => 'boolean',
        'swipe_navigation' => 'boolean',
        'preserve_tab_state' => 'boolean',
        // Drawer
        'drawer_logo_enabled' => 'boolean',
        'drawer_logo_size' => 'integer',
        // Splashscreen
        'splashscreen_duration' => 'integer',
        'splashscreen_logo_enabled' => 'boolean',
        'splashscreen_logo_size' => 'integer',
        // Advanced
        'build_config' => 'array',
        'environment_variables' => 'array',
        // Push Notifications (Firebase)
        'firebase_service_account' => 'encrypted',
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
