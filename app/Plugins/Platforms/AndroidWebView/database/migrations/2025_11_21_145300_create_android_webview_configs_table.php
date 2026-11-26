<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('android_webview_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();

            // === General Settings ===
            $table->string('url'); // WebView-specific: website URL to load
            $table->string('package_name')->unique();
            $table->enum('screen_orientation', ['portrait', 'landscape', 'system'])->default('system');
            $table->char('app_language', 2)->default('en');
            $table->string('admin_email')->nullable();

            // === WebView-Specific Settings ===
            $table->string('user_agent')->nullable();

            // === Design Settings ===
            $table->string('theme_color')->default('#007AFF');
            $table->boolean('enable_dynamic_colors')->default(false);
            $table->enum('title_color', ['automatic', 'light', 'dark'])->default('automatic');
            $table->enum('layout_template', ['app_bar', 'app_bar_tabs', 'app_bar_drawer', 'blank'])
                ->default('app_bar_tabs');
            $table->enum('progress_indicator', ['circular', 'linear', 'none'])->default('circular');
            $table->string('progress_indicator_color')->nullable();
            $table->boolean('show_app_bar_title')->default(true);
            $table->string('nav_tab_icon_color')->nullable();
            $table->string('active_tab_color')->nullable();

            // === Drawer Settings ===
            $table->enum('drawer_mode', ['default', 'color', 'image'])->default('default');
            $table->string('drawer_background_color')->nullable();
            $table->string('drawer_background_image')->nullable();
            $table->boolean('drawer_logo_enabled')->default(false);
            $table->string('drawer_logo')->nullable();
            $table->string('drawer_logo_light')->nullable();
            $table->string('drawer_logo_dark')->nullable();
            $table->integer('drawer_logo_size')->default(48);
            $table->string('drawer_title')->nullable();
            $table->string('drawer_subtitle')->nullable();
            $table->enum('drawer_text_theme', ['light', 'dark'])->default('dark');
            // Drawer item icon colors (inactive state)
            $table->string('drawer_icon_color_light')->nullable();
            $table->string('drawer_icon_color_dark')->nullable();
            // Drawer item active/selected colors
            $table->string('drawer_active_color_light')->nullable();
            $table->string('drawer_active_color_dark')->nullable();

            // === Icon Settings ===
            $table->string('app_icon')->nullable();
            $table->string('uploaded_icon_zip')->nullable();
            $table->string('icon_extracted_path')->nullable();

            // === Splashscreen Settings ===
            $table->enum('splashscreen_background_type', ['color', 'image'])->default('color');
            $table->string('splashscreen_image')->nullable();
            $table->string('splashscreen_background')->default('#007AFF');
            $table->integer('splashscreen_duration')->default(3);
            $table->enum('splashscreen_animation', ['fade', 'zoom', 'none'])->default('fade');
            $table->boolean('splashscreen_logo_enabled')->default(false);
            $table->string('splashscreen_logo')->nullable();
            $table->integer('splashscreen_logo_size')->default(120);
            $table->string('splashscreen_title')->nullable();
            $table->string('splashscreen_subtitle')->nullable();
            $table->enum('splashscreen_text_theme', ['light', 'dark'])->default('light');

            // === Navigation Settings (Complex - Keep as JSON) ===
            $table->json('navigation_items')->nullable();
            $table->json('drawer_items')->nullable();
            $table->json('app_bar_buttons')->nullable();
            $table->string('home_url')->nullable();
            $table->boolean('pull_to_refresh')->default(true);
            $table->boolean('swipe_navigation')->default(true);
            $table->boolean('preserve_tab_state')->default(false);

            // === Permissions (Android-Specific) ===
            $table->boolean('permission_location')->default(false);
            $table->boolean('permission_camera')->default(false);
            $table->boolean('permission_storage')->default(false);
            $table->boolean('permission_record_audio')->default(false);
            $table->boolean('permission_read_contacts')->default(false);
            $table->boolean('permission_vibrate')->default(false);

            // === Custom Code ===
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();

            // === Build Configuration ===
            $table->json('build_config')->nullable();
            $table->json('environment_variables')->nullable();

            // === Signing ===
            $table->foreignId('keystore_id')->nullable()->constrained('keystores')->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->unique('app_id'); // One config per app
            $table->index('package_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('android_webview_configs');
    }
};
