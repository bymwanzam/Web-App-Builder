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
        Schema::table('apps', function (Blueprint $table) {
            // General settings - make nullable initially for existing records
            $table->string('app_id')->nullable()->unique()->after('url');
            $table->enum('screen_orientation', ['system', 'portrait', 'landscape'])->default('system')->after('app_id');
            $table->string('app_language', 10)->default('EN')->after('screen_orientation');
            $table->string('admin_email')->nullable()->after('app_language');
            $table->text('user_agent')->nullable()->after('admin_email');

            // Design settings (status bar, additional colors)
            $table->string('status_bar_color')->nullable()->after('title_color');
            $table->string('primary_color')->nullable()->after('status_bar_color');
            $table->string('accent_color')->nullable()->after('primary_color');

            // Icon settings
            $table->string('app_icon')->nullable()->after('icon_type');
            $table->string('icon_background_gradient_start')->nullable()->after('app_icon');
            $table->string('icon_background_gradient_end')->nullable()->after('icon_background_gradient_start');

            // Splashscreen settings
            $table->string('splashscreen_image')->nullable()->after('layout_template');
            $table->string('splashscreen_background')->default('#FFFFFF')->after('splashscreen_image');
            $table->integer('splashscreen_duration')->default(3)->after('splashscreen_background');
            $table->enum('splashscreen_animation', ['fade', 'slide', 'zoom', 'none'])->default('fade')->after('splashscreen_duration');

            // Navigation settings
            $table->json('navigation_items')->nullable()->after('splashscreen_animation');
            $table->string('home_url')->nullable()->after('navigation_items');
            $table->boolean('pull_to_refresh')->default(true)->after('home_url');
            $table->boolean('swipe_navigation')->default(true)->after('pull_to_refresh');

            // Localization settings
            $table->json('supported_languages')->nullable()->after('app_language');
            $table->boolean('rtl_support')->default(false)->after('supported_languages');
            $table->string('date_format')->default('MM/DD/YYYY')->after('rtl_support');
            $table->enum('time_format', ['12h', '24h'])->default('12h')->after('date_format');
            $table->string('currency_format')->default('USD')->after('time_format');
            $table->json('translations')->nullable()->after('currency_format');
        });

        // Generate app_ids for existing records
        DB::table('apps')->whereNull('app_id')->get()->each(function ($app) {
            // Convert to lowercase and remove all non-alphanumeric characters (Android package name compliance)
            $slug = strtolower($app->name);
            $slug = preg_replace('/[^a-z0-9]/', '', $slug);
            $appId = 'com.'.$slug.'.app';

            // Ensure uniqueness by appending number if needed
            $counter = 1;
            while (DB::table('apps')->where('app_id', $appId)->exists()) {
                $appId = 'com.'.$slug.$counter.'.app';
                $counter++;
            }

            DB::table('apps')
                ->where('id', $app->id)
                ->update([
                    'app_id' => $appId,
                    'admin_email' => $app->admin_email ?? 'admin@example.com',
                ]);
        });

        // Now make app_id required
        Schema::table('apps', function (Blueprint $table) {
            $table->string('app_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn([
                'app_id',
                'screen_orientation',
                'app_language',
                'admin_email',
                'user_agent',
                'status_bar_color',
                'primary_color',
                'accent_color',
                'app_icon',
                'icon_background_gradient_start',
                'icon_background_gradient_end',
                'splashscreen_image',
                'splashscreen_background',
                'splashscreen_duration',
                'splashscreen_animation',
                'navigation_items',
                'home_url',
                'pull_to_refresh',
                'swipe_navigation',
                'supported_languages',
                'rtl_support',
                'date_format',
                'time_format',
                'currency_format',
                'translations',
            ]);
        });
    }
};
