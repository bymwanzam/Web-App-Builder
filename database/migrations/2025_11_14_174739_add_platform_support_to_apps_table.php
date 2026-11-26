<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds platform plugin support to apps table and migrates existing Android apps.
     */
    public function up(): void
    {
        // 1. Add platform plugin relationship and config fields
        Schema::table('apps', function (Blueprint $table) {
            $table->foreignId('platform_plugin_id')
                ->nullable()
                ->after('user_id')
                ->constrained('plugins')
                ->onDelete('restrict'); // Prevent deleting platform if apps exist

            $table->json('platform_config')->nullable()->after('layout_template');
        });

        // 2. Find or create Android platform plugin
        $androidPlugin = DB::table('plugins')
            ->where('type', 'platform')
            ->where('slug', 'android')
            ->first();

        // If Android plugin exists, migrate all existing apps to use it
        if ($androidPlugin) {
            $this->migrateExistingApps($androidPlugin->id);
        }

        // 3. Drop Android-specific columns (data already migrated to platform_config)
        Schema::table('apps', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('apps', 'android_keystore_id')) {
                $table->dropForeign(['android_keystore_id']);
            }

            // Drop Android-specific columns
            $table->dropColumn([
                'android_version_name',
                'android_package_name',
                'android_keystore_id',
            ]);
        });
    }

    /**
     * Migrate existing apps to platform plugin system.
     */
    protected function migrateExistingApps(int $androidPluginId): void
    {
        $apps = DB::table('apps')->get();

        foreach ($apps as $app) {
            // Build platform_config from Android-specific fields
            $platformConfig = [];

            // Version name
            if ($app->android_version_name) {
                $platformConfig['version_name'] = $app->android_version_name;
            }

            // Package name
            if ($app->android_package_name) {
                $platformConfig['package_name'] = $app->android_package_name;
            } else {
                // Generate package name if not set
                $slug = strtolower($app->name);
                $slug = preg_replace('/[^a-z0-9]/', '', $slug);
                $platformConfig['package_name'] = "com.{$slug}.app";
            }

            // Keystore
            if ($app->android_keystore_id) {
                $platformConfig['keystore_id'] = $app->android_keystore_id;
            }

            // Permissions (check if they exist in the apps table)
            $permissions = [];
            if (Schema::hasColumn('apps', 'permission_location') && $app->permission_location) {
                $permissions['location'] = true;
            }
            if (Schema::hasColumn('apps', 'permission_camera') && $app->permission_camera) {
                $permissions['camera'] = true;
            }
            if (Schema::hasColumn('apps', 'permission_storage') && $app->permission_storage) {
                $permissions['storage'] = true;
            }
            if (Schema::hasColumn('apps', 'permission_record_audio') && $app->permission_record_audio) {
                $permissions['record_audio'] = true;
            }
            if (Schema::hasColumn('apps', 'permission_read_contacts') && $app->permission_read_contacts) {
                $permissions['read_contacts'] = true;
            }
            if (Schema::hasColumn('apps', 'permission_vibrate') && $app->permission_vibrate) {
                $permissions['vibrate'] = true;
            }

            if (! empty($permissions)) {
                $platformConfig['permissions'] = $permissions;
            }

            // Platform-specific build settings
            $platformConfig['min_sdk'] = 24;
            $platformConfig['target_sdk'] = 34;
            $platformConfig['build_tools_version'] = '34.0.0';

            // Update app with platform plugin and config
            DB::table('apps')
                ->where('id', $app->id)
                ->update([
                    'platform_plugin_id' => $androidPluginId,
                    'platform_config' => json_encode($platformConfig),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     * Restores Android-specific columns from platform_config.
     */
    public function down(): void
    {
        // 1. Re-add Android-specific columns
        Schema::table('apps', function (Blueprint $table) {
            $table->string('android_version_name', 20)
                ->nullable()
                ->default('1.0.0')
                ->after('version_name');

            $table->string('android_package_name')
                ->nullable()
                ->after('app_id');

            $table->foreignId('android_keystore_id')
                ->nullable()
                ->after('environment_variables')
                ->constrained('keystores')
                ->onDelete('set null');
        });

        // 2. Restore data from platform_config
        $apps = DB::table('apps')->whereNotNull('platform_config')->get();

        foreach ($apps as $app) {
            $platformConfig = json_decode($app->platform_config, true);

            if (! $platformConfig) {
                continue;
            }

            $updates = [];

            if (isset($platformConfig['version_name'])) {
                $updates['android_version_name'] = $platformConfig['version_name'];
            }

            if (isset($platformConfig['package_name'])) {
                $updates['android_package_name'] = $platformConfig['package_name'];
            }

            if (isset($platformConfig['keystore_id'])) {
                $updates['android_keystore_id'] = $platformConfig['keystore_id'];
            }

            if (! empty($updates)) {
                DB::table('apps')
                    ->where('id', $app->id)
                    ->update($updates);
            }
        }

        // 3. Drop platform plugin fields
        Schema::table('apps', function (Blueprint $table) {
            $table->dropForeign(['platform_plugin_id']);
            $table->dropColumn(['platform_plugin_id', 'platform_config']);
        });
    }
};
