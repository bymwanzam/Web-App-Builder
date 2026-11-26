<?php

namespace Tests\Feature\Install;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class InstallControllerTest extends InstallerTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Remove installation_completed flag to allow installer access
        if (\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
            SystemSetting::where('key', 'installation_completed')->delete();
        }
    }

    #[Test]
    public function welcome_page_is_accessible_when_not_installed(): void
    {
        $response = $this->get('/install');

        $response->assertStatus(200);
        $response->assertViewIs('install.welcome');
    }

    #[Test]
    public function requirements_page_shows_dependencies(): void
    {
        $response = $this->get('/install/requirements');

        $response->assertStatus(200);
        $response->assertViewIs('install.requirements');
        $response->assertViewHas('dependencies');

        $dependencies = $response->viewData('dependencies');
        $this->assertIsArray($dependencies);
        $this->assertArrayHasKey('PHP 8.2+', $dependencies);
    }

    #[Test]
    public function permissions_page_shows_permissions(): void
    {
        $response = $this->get('/install/permissions');

        $response->assertStatus(200);
        $response->assertViewIs('install.permissions');
        $response->assertViewHas('permissions');

        $permissions = $response->viewData('permissions');
        $this->assertIsArray($permissions);
        $this->assertArrayHasKey('.env', $permissions);
        $this->assertArrayHasKey('storage', $permissions);
    }

    #[Test]
    public function database_page_shows_configuration_form(): void
    {
        $response = $this->get('/install/database');

        $response->assertStatus(200);
        $response->assertViewIs('install.database');
        $response->assertViewHas('config');
    }

    #[Test]
    public function admin_page_is_accessible(): void
    {
        $response = $this->get('/install/admin');

        $response->assertStatus(200);
        $response->assertViewIs('install.admin');
    }

    #[Test]
    public function completed_page_is_accessible(): void
    {
        $response = $this->get('/install/completed');

        $response->assertStatus(200);
        $response->assertViewIs('install.completed');
    }

    #[Test]
    public function welcome_redirects_to_login_when_installed(): void
    {
        // Mark as installed
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function requirements_redirects_to_login_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/requirements');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function database_redirects_to_login_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/database');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function admin_redirects_to_login_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/admin');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function completed_page_is_accessible_even_when_installed(): void
    {
        // Mark as installed
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/completed');

        // Completed page should be accessible even after installation
        $response->assertStatus(200);
    }

    #[Test]
    public function installer_service_is_injected_into_controller(): void
    {
        $response = $this->get('/install/requirements');

        $response->assertStatus(200);

        // If dependencies are present, the service was injected correctly
        $dependencies = $response->viewData('dependencies');
        $this->assertNotEmpty($dependencies);
    }

    #[Test]
    public function database_config_contains_expected_keys(): void
    {
        $response = $this->get('/install/database');

        $config = $response->viewData('config');

        $this->assertArrayHasKey('db_type', $config);
        $this->assertArrayHasKey('host', $config);
        $this->assertArrayHasKey('port', $config);
        $this->assertArrayHasKey('database', $config);
        $this->assertArrayHasKey('username', $config);
        $this->assertArrayHasKey('password', $config);
    }
}
