<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('android_wordpress_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_id')->constrained('apps')->cascadeOnDelete();

            // === WordPress Connection ===
            $table->string('site_url'); // WordPress.com site URL
            $table->string('site_id')->nullable(); // WordPress.com site ID (for API)
            $table->string('api_namespace')->default('wp/v2'); // REST API namespace

            // === General Settings ===
            $table->string('package_name')->unique();
            $table->enum('screen_orientation', ['portrait', 'landscape', 'system'])->default('system');
            $table->char('app_language', 2)->default('en');

            // === Design Settings ===
            $table->string('theme_color')->default('#21759B');
            $table->string('text_color_light')->default('#1E1E1E');
            $table->string('text_color_dark')->default('#FFFFFF');
            $table->boolean('enable_dynamic_colors')->default(true);
            $table->enum('layout_template', ['list', 'app_bar', 'app_bar_tabs', 'app_bar_drawer'])
                ->default('app_bar_tabs');
            $table->enum('layout_style', ['list', 'grid', 'magazine'])->default('list');
            $table->boolean('show_app_bar_title')->default(true);

            // === Navigation Tab Colors ===
            $table->string('nav_tab_icon_color_light')->default('#666666');
            $table->string('nav_tab_icon_color_dark')->default('#AAAAAA');
            $table->string('active_tab_color_light')->default('#21759B');
            $table->string('active_tab_color_dark')->default('#5BB3D9');

            // === Content Settings (WordPress-Specific) ===
            $table->json('post_types')->nullable(); // ['post', 'page']
            $table->json('categories_filter')->nullable(); // Category IDs to filter
            $table->json('tags_filter')->nullable(); // Tag IDs to filter
            $table->integer('posts_per_page')->default(10);
            $table->boolean('show_author')->default(true);
            $table->boolean('show_date')->default(true);
            $table->boolean('show_excerpt')->default(true);
            $table->boolean('show_featured_image')->default(true);
            $table->boolean('show_categories')->default(true);
            $table->boolean('show_reading_time')->default(false);

            // === Features Settings (WordPress-Specific) ===
            $table->boolean('enable_comments')->default(true);
            $table->boolean('enable_search')->default(true);
            $table->boolean('enable_bookmarks')->default(true);
            $table->boolean('enable_offline_reading')->default(false);
            $table->boolean('enable_share')->default(true);
            $table->boolean('enable_user_auth')->default(false);
            $table->boolean('enable_push_notifications')->default(false);

            // === Navigation Settings (Complex - Keep as JSON) ===
            $table->json('navigation_items')->nullable(); // Bottom tabs (max 5)
            $table->json('drawer_items')->nullable(); // Drawer items (max 10)
            $table->json('app_bar_buttons')->nullable(); // App bar action buttons (max 3)
            $table->string('home_screen')->default('posts'); // posts, categories, custom
            $table->boolean('pull_to_refresh')->default(true);
            $table->boolean('swipe_navigation')->default(false);
            $table->boolean('preserve_tab_state')->default(true);

            // === Drawer Settings ===
            $table->enum('drawer_mode', ['default', 'color', 'image'])->default('default');
            $table->string('drawer_background_color')->nullable();
            $table->string('drawer_background_image')->nullable();
            $table->boolean('drawer_logo_enabled')->default(false);
            $table->string('drawer_logo')->nullable();
            $table->string('drawer_logo_light')->nullable();
            $table->string('drawer_logo_dark')->nullable();
            $table->integer('drawer_logo_size')->default(80);
            $table->string('drawer_title')->nullable();
            $table->string('drawer_subtitle')->nullable();
            $table->enum('drawer_text_theme', ['light', 'dark'])->default('light');
            $table->string('drawer_icon_color_light')->default('#666666');
            $table->string('drawer_icon_color_dark')->default('#AAAAAA');
            $table->string('drawer_active_color_light')->default('#21759B');
            $table->string('drawer_active_color_dark')->default('#5BB3D9');

            // === Icon Settings ===
            $table->string('app_icon')->nullable();
            $table->string('uploaded_icon_zip')->nullable();
            $table->string('icon_extracted_path')->nullable();

            // === Splashscreen Settings ===
            $table->enum('splashscreen_background_type', ['color', 'image'])->default('color');
            $table->string('splashscreen_image')->nullable();
            $table->string('splashscreen_background')->default('#21759B');
            $table->integer('splashscreen_duration')->default(3);
            $table->enum('splashscreen_animation', ['fade', 'zoom', 'none'])->default('fade');
            $table->boolean('splashscreen_logo_enabled')->default(false);
            $table->string('splashscreen_logo')->nullable();
            $table->integer('splashscreen_logo_size')->default(120);
            $table->string('splashscreen_title')->nullable();
            $table->string('splashscreen_subtitle')->nullable();
            $table->enum('splashscreen_text_theme', ['light', 'dark'])->default('light');

            // === Advanced Settings ===
            $table->json('build_config')->nullable();
            $table->json('environment_variables')->nullable();

            // === Push Notifications (Firebase) ===
            $table->string('google_services_json')->nullable(); // Path to google-services.json
            $table->text('firebase_service_account')->nullable(); // Encrypted service account JSON

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
        Schema::dropIfExists('android_wordpress_configs');
    }
};
