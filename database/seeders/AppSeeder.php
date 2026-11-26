<?php

namespace Database\Seeders;

use App\Models\AndroidWebViewConfig;
use App\Models\App;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed test apps in local environment
        if (! app()->environment('local')) {
            return;
        }

        // Get AndroidWebView platform plugin ID
        $androidWebViewPlugin = Plugin::where('slug', 'android-webview')->first();

        if (! $androidWebViewPlugin) {
            $this->command->warn('AndroidWebView plugin not found. Skipping app seeding.');

            return;
        }

        // Define test app types with varied configurations
        $appTypes = [
            [
                'name' => 'E-commerce Store',
                'url' => 'https://example-shop.com',
                'package_name' => 'com.appy.shop',
                'theme_color' => '#3B82F6', // Blue
            ],
            [
                'name' => 'News Portal',
                'url' => 'https://example-news.com',
                'package_name' => 'com.appy.news',
                'theme_color' => '#EF4444', // Red
            ],
            [
                'name' => 'Social Network',
                'url' => 'https://example-social.com',
                'package_name' => 'com.appy.social',
                'theme_color' => '#8B5CF6', // Purple
            ],
            [
                'name' => 'Food Delivery',
                'url' => 'https://example-food.com',
                'package_name' => 'com.appy.food',
                'theme_color' => '#F97316', // Orange
            ],
            [
                'name' => 'Fitness Tracker',
                'url' => 'https://example-fitness.com',
                'package_name' => 'com.appy.fitness',
                'theme_color' => '#10B981', // Green
            ],
            [
                'name' => 'Real Estate',
                'url' => 'https://example-realestate.com',
                'package_name' => 'com.appy.realestate',
                'theme_color' => '#14B8A6', // Teal
            ],
            [
                'name' => 'Learning Platform',
                'url' => 'https://example-learning.com',
                'package_name' => 'com.appy.learning',
                'theme_color' => '#6366F1', // Indigo
            ],
            [
                'name' => 'Music Streaming',
                'url' => 'https://example-music.com',
                'package_name' => 'com.appy.music',
                'theme_color' => '#EC4899', // Pink
            ],
            [
                'name' => 'Travel Booking',
                'url' => 'https://example-travel.com',
                'package_name' => 'com.appy.travel',
                'theme_color' => '#06B6D4', // Cyan
            ],
        ];

        // Create apps for both admin (ID=1) and regular user (ID=2)
        $userIds = [1, 2];
        $userPrefix = [1 => 'admin', 2 => 'user'];

        // Available layout templates
        $layoutTemplates = ['app_bar', 'app_bar_tabs', 'app_bar_drawer', 'blank'];

        foreach ($userIds as $userId) {
            foreach ($appTypes as $appType) {
                // Create App record
                $app = App::create([
                    'user_id' => $userId,
                    'platform_plugin_id' => $androidWebViewPlugin->id,
                    'name' => $appType['name'],
                    'version_name' => '1.0.0',
                    'version_code' => 1,
                ]);

                // Create unique package name for each user
                $packageName = str_replace('com.appy.', "com.appy.{$userPrefix[$userId]}.", $appType['package_name']);

                // Randomize layout template
                $layoutTemplate = $layoutTemplates[array_rand($layoutTemplates)];

                // Create AndroidWebViewConfig record
                AndroidWebViewConfig::create([
                    'app_id' => $app->id,
                    'url' => $appType['url'],
                    'package_name' => $packageName,
                    'theme_color' => $appType['theme_color'],
                    'layout_template' => $layoutTemplate,
                    'screen_orientation' => 'portrait',
                    'pull_to_refresh' => true,
                    'enable_dynamic_colors' => true,
                    'show_app_bar_title' => true,
                    'swipe_navigation' => false,
                    'preserve_tab_state' => true,
                    'drawer_mode' => 'default',
                    'splashscreen_duration' => 3,
                    'splashscreen_background_type' => 'color',
                    'splashscreen_background' => $appType['theme_color'],
                    'splashscreen_logo_enabled' => false,
                    'navigation_items' => [],
                    'drawer_items' => [],
                    'app_bar_buttons' => [],
                    'permission_location' => false,
                    'permission_camera' => false,
                    'permission_storage' => false,
                    'permission_record_audio' => false,
                    'permission_read_contacts' => false,
                    'permission_vibrate' => false,
                ]);

                $this->command->info("Created app: {$appType['name']} for user ID {$userId}");
            }
        }

        $this->command->info('Successfully seeded 18 test apps (9 per user)');
    }
}
