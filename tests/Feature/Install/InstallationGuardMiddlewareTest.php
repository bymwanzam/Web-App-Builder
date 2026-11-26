<?php

namespace Tests\Feature\Install;

use App\Models\SystemSetting;
use App\Services\InstallerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class InstallationGuardMiddlewareTest extends InstallerTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Remove installation flag to allow installer access
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            SystemSetting::where('key', 'installation_completed')->delete();
        }
    }

    #[Test]
    public function allows_requirements_page_when_dependencies_are_met(): void
    {
        $response = $this->get('/install/requirements');

        // Should be accessible since we're running on a valid PHP environment
        $response->assertStatus(200);
    }

    #[Test]
    public function allows_permissions_page_when_permissions_are_correct(): void
    {
        // In development environment, permissions are usually correct
        $response = $this->get('/install/permissions');

        $response->assertStatus(200);
    }

    #[Test]
    public function allows_database_page_when_database_is_connected(): void
    {
        // With RefreshDatabase, we already have a working database connection
        $response = $this->get('/install/database');

        $response->assertStatus(200);
    }

    #[Test]
    public function allows_admin_page_when_users_table_exists(): void
    {
        // RefreshDatabase ensures users table exists
        $response = $this->get('/install/admin');

        $response->assertStatus(200);
    }

    #[Test]
    public function guard_validates_step_sequence(): void
    {
        // All previous steps should pass for admin page to be accessible
        // The guard checks:
        // 1. Dependencies met (true - running on PHP 8.2+)
        // 2. Permissions correct (true - development environment)
        // 3. Database connected (true - RefreshDatabase)
        // 4. Users table exists (true - RefreshDatabase runs migrations)

        $response = $this->get('/install/admin');

        // If we get here without redirect, all guards passed
        $response->assertStatus(200);
    }

    #[Test]
    public function completed_page_bypasses_guard_checks(): void
    {
        // Completed page should be accessible regardless of guard status
        $response = $this->get('/install/completed');

        $response->assertStatus(200);
    }

    #[Test]
    public function installer_service_dependencies_are_checked(): void
    {
        $installer = new InstallerService;

        // All required dependencies should be met in test environment
        $requiredDependencies = [
            'PHP 8.2+',
            'BCMath',
            'Ctype',
            'cURL',
            'DOM',
            'Fileinfo',
            'Filter',
            'Hash',
            'JSON',
            'Mbstring',
            'OpenSSL',
            'PCRE',
            'PDO',
            'Session',
            'Tokenizer',
            'XML',
        ];

        foreach ($requiredDependencies as $dep) {
            $this->assertTrue(
                $installer->dependencies[$dep],
                "Required dependency '{$dep}' should be met"
            );
        }
    }
}
