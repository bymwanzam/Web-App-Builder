<?php

namespace Database\Seeders;

use App\Models\Plugin;
use Illuminate\Database\Seeder;

class PlatformPluginsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Core platform plugins (always installed)
        $plugins = [
            [
                'name' => 'Android WebView',
                'slug' => 'android-webview',
                'type' => 'platform',
                'class' => 'App\Plugins\Platforms\AndroidWebView\AndroidWebViewPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'config' => null,
                'migrations' => [
                    '2025_11_21_145300_create_android_webview_configs_table.php',
                    '2025_11_24_000000_update_text_color_fields_in_android_webview_configs.php',
                    '2025_11_24_163205_split_tab_colors_light_dark_in_android_webview_configs.php',
                    '2025_12_24_000004_create_app_pages_table.php',
                ],
                'metadata' => json_decode(file_get_contents(app_path('Plugins/Platforms/AndroidWebView/plugin.json')), true),
                'installed_at' => now(),
            ],
        ];

        // Development platform plugins (local environment only)
        if (app()->environment('local')) {
            $plugins[] = [
                'name' => 'Android WordPress',
                'slug' => 'android-wordpress',
                'type' => 'platform',
                'class' => 'App\Plugins\Platforms\AndroidWordPress\AndroidWordPressPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'config' => null,
                'migrations' => [
                    '2025_12_26_000000_create_android_wordpress_configs_table.php',
                    '2025_12_26_000001_create_app_pages_table_if_not_exists.php',
                ],
                'metadata' => json_decode(file_get_contents(app_path('Plugins/Platforms/AndroidWordPress/plugin.json')), true),
                'installed_at' => now(),
            ];
        }

        foreach ($plugins as $pluginData) {
            $plugin = Plugin::updateOrCreate(
                ['slug' => $pluginData['slug']],
                $pluginData
            );

            // Always run platform-specific migrations to ensure tables exist
            $this->runPlatformMigrations($plugin);

            $this->command->info("Installed platform: {$pluginData['name']}");
        }
    }

    /**
     * Run platform-specific migrations.
     */
    protected function runPlatformMigrations(Plugin $plugin): void
    {
        if (empty($plugin->migrations)) {
            return;
        }

        // Convert slug to PascalCase directory name (android-webview -> AndroidWebView)
        $platformDir = str_replace(['-', '_'], '', ucwords($plugin->slug, '-_'));
        $platformPath = app_path("Plugins/Platforms/{$platformDir}/database/migrations");

        if (! is_dir($platformPath)) {
            return;
        }

        foreach ($plugin->migrations as $migrationFile) {
            $migrationPath = "{$platformPath}/{$migrationFile}";

            if (file_exists($migrationPath)) {
                try {
                    // Include the migration file and get the returned class instance
                    // This works with both anonymous classes and named classes
                    $migration = require $migrationPath;

                    // If it's an object (anonymous class), run it directly
                    if (is_object($migration)) {
                        $migration->up();
                        $this->command->info("  → Ran migration: {$migrationFile}");
                    }
                } catch (\Exception $e) {
                    // Check if it's a "table already exists" error - that's OK
                    if (str_contains($e->getMessage(), 'already exists')) {
                        $this->command->info("  → Migration already applied: {$migrationFile}");
                    } else {
                        $this->command->warn("  → Failed to run migration {$migrationFile}: {$e->getMessage()}");
                    }
                }
            }
        }
    }
}
