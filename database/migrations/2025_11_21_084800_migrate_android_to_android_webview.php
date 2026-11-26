<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Step 1: Get the old Android plugin ID
            $oldPlugin = DB::table('plugins')
                ->where('slug', 'android')
                ->first();

            // Skip migration if old plugin doesn't exist (already migrated or fresh install)
            if (! $oldPlugin) {
                // Check if AndroidWebView already exists (migration already ran)
                $newExists = DB::table('plugins')->where('slug', 'android-webview')->exists();
                if ($newExists) {
                    // Already migrated, skip
                    return;
                }

                // Fresh installation with no old plugin - this is fine, skip
                return;
            }

            // Step 2: Insert new AndroidWebView plugin record
            $newPluginId = DB::table('plugins')->insertGetId([
                'name' => 'Android WebView',
                'slug' => 'android-webview',
                'type' => 'platform',
                'class' => 'App\\Plugins\\Platforms\\AndroidWebView\\AndroidWebViewPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'config' => json_encode([]),
                'migrations' => json_encode([
                    'database/migrations/001_create_keystores_table.php',
                ]),
                'metadata' => json_encode([
                    'name' => 'Android WebView',
                    'slug' => 'android-webview',
                    'description' => 'Build Android apps that load website content in a native WebView with full customization',
                    'version' => '1.0.0',
                    'author' => 'Titan Systems',
                    'author_url' => 'https://codecanyon.net/user/titansystems',
                    'type' => 'platform',
                    'icon' => 'resources/images/icon.svg',
                    'main_class' => 'AndroidWebViewPlatform.php',
                    'namespace' => 'App\\Plugins\\Platforms\\AndroidWebView',
                    'requirements' => [
                        'php' => '>=8.2',
                        'laravel' => '>=12.0',
                    ],
                    'migrations' => [
                        'database/migrations/001_create_keystores_table.php',
                    ],
                    'livewire_components' => [
                        'Livewire/AppGeneralSettings.php',
                        'Livewire/AppDesignSettings.php',
                        'Livewire/AppIconSettings.php',
                        'Livewire/AppSplashscreenSettings.php',
                        'Livewire/AppNavigationSettings.php',
                        'Livewire/AppDrawerSettings.php',
                        'Livewire/AppWebViewSettings.php',
                        'Livewire/AppCustomCodeSettings.php',
                        'Livewire/AppPermissionsSettings.php',
                        'Livewire/AppSigningKeysSettings.php',
                        'Livewire/AppNotificationsSettings.php',
                        'Livewire/DeleteIconModal.php',
                        'Livewire/DeleteKeystoreModal.php',
                        'Livewire/EditKeystoreModal.php',
                        'Livewire/SigningKeyUploadModal.php',
                        'Livewire/DeleteSplashscreenImageModal.php',
                        'Livewire/DeleteSplashscreenLogoModal.php',
                        'Livewire/DeleteDrawerBackgroundModal.php',
                        'Livewire/DeleteDrawerLogoModal.php',
                        'Livewire/DeleteDrawerLogoDarkModal.php',
                        'Livewire/DeleteDrawerLogoLightModal.php',
                    ],
                    'settings' => [
                        'content_type' => 'webview',
                        'default_build_format' => 'apk',
                        'min_sdk_version' => 24,
                        'target_sdk_version' => 34,
                        'build_tools_version' => '34.0.0',
                        'supports_permissions' => true,
                        'supports_custom_code' => true,
                        'package_prefix' => 'com',
                    ],
                ]),
                'installed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Step 3: Update all apps to point to new plugin
            $affectedApps = DB::table('apps')
                ->where('platform_plugin_id', $oldPlugin->id)
                ->update([
                    'platform_plugin_id' => $newPluginId,
                    'updated_at' => now(),
                ]);

            // Step 4: Delete old Android plugin record
            // (Safe now because no apps reference it due to step 3)
            DB::table('plugins')
                ->where('id', $oldPlugin->id)
                ->delete();

            // Log migration details for rollback reference
            DB::table('migrations')->insert([
                'migration' => '2025_11_21_084800_migrate_android_to_android_webview',
                'batch' => DB::table('migrations')->max('batch') + 1,
            ]);

            // Store old plugin ID in a temporary table for rollback
            // Note: This will be used by down() method
            DB::statement('
                CREATE TEMPORARY TABLE IF NOT EXISTS android_migration_backup (
                    old_plugin_id INTEGER,
                    new_plugin_id INTEGER,
                    affected_apps_count INTEGER
                )
            ');
            DB::table('android_migration_backup')->insert([
                'old_plugin_id' => $oldPlugin->id,
                'new_plugin_id' => $newPluginId,
                'affected_apps_count' => $affectedApps,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            // Step 1: Get the AndroidWebView plugin
            $androidWebViewPlugin = DB::table('plugins')
                ->where('slug', 'android-webview')
                ->first();

            if (! $androidWebViewPlugin) {
                throw new \Exception('AndroidWebView plugin not found. Cannot rollback migration.');
            }

            // Step 2: Re-insert old Android plugin record
            $oldPluginId = DB::table('plugins')->insertGetId([
                'name' => 'Android Platform',
                'slug' => 'android',
                'type' => 'platform',
                'class' => 'App\\Plugins\\Platforms\\Android\\AndroidPlatform',
                'version' => '1.0.0',
                'status' => 'active',
                'config' => json_encode([]),
                'migrations' => json_encode([
                    'database/migrations/001_create_keystores_table.php',
                ]),
                'metadata' => json_encode([
                    'name' => 'Android Platform',
                    'slug' => 'android',
                    'description' => 'Build Android apps (APK & AAB)',
                    'version' => '1.0.0',
                    'author' => 'Titan Systems',
                    'author_url' => 'https://codecanyon.net/user/titansystems',
                    'type' => 'platform',
                    'icon' => 'resources/icon.jpg',
                    'main_class' => 'AndroidPlatform.php',
                    'namespace' => 'App\\Plugins\\Platforms\\Android',
                    'requirements' => [
                        'php' => '>=8.2',
                        'laravel' => '>=12.0',
                    ],
                    'migrations' => [
                        'database/migrations/001_create_keystores_table.php',
                    ],
                    'settings' => [
                        'default_build_format' => 'apk',
                        'min_sdk_version' => 24,
                        'target_sdk_version' => 34,
                        'build_tools_version' => '34.0.0',
                        'supports_permissions' => true,
                        'package_prefix' => 'com',
                    ],
                ]),
                'installed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Step 3: Revert all apps back to old Android plugin
            DB::table('apps')
                ->where('platform_plugin_id', $androidWebViewPlugin->id)
                ->update([
                    'platform_plugin_id' => $oldPluginId,
                    'updated_at' => now(),
                ]);

            // Step 4: Delete AndroidWebView plugin record
            DB::table('plugins')
                ->where('id', $androidWebViewPlugin->id)
                ->delete();
        });
    }
};
