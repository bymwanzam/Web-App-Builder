<?php

namespace Tests;

use App\Models\SystemSetting;
use App\Services\PluginManager;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed platform plugins to ensure they are in the database
        // This also runs plugin migrations via runPlatformMigrations()
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PlatformPluginsSeeder']);

        // Re-boot PluginManager to register view namespaces after seeding
        $pluginManager = $this->app->make(PluginManager::class);
        $pluginManager->boot();

        // Setup system settings for auth tests
        $this->setupSystemSettings();
    }

    /**
     * Setup system settings required for auth tests.
     */
    protected function setupSystemSettings(): void
    {
        // Only setup if system_settings table exists
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        try {
            // Mark installation as completed for tests
            SystemSetting::updateOrCreate(
                ['key' => 'installation_completed'],
                [
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'app',
                ]
            );

            // Enable registration by default in tests
            SystemSetting::updateOrCreate(
                ['key' => 'allow_registration'],
                [
                    'value' => '1',
                    'type' => 'boolean',
                    'description' => 'Allow user registration',
                ]
            );

            // Disable email verification in tests for simplicity
            SystemSetting::updateOrCreate(
                ['key' => 'enable_email_verification'],
                [
                    'value' => '0',
                    'type' => 'boolean',
                    'description' => 'Require email verification for new users',
                ]
            );
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist or other DB issues
            // This allows tests to run even if migrations haven't been run yet
        }
    }
}
