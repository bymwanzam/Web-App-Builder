<?php

namespace Tests\Feature\Install;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class NotInstalledMiddlewareTest extends InstallerTestCase
{
    use RefreshDatabase;

    #[Test]
    public function allows_access_to_installer_when_not_installed(): void
    {
        // Remove installation flag
        SystemSetting::where('key', 'installation_completed')->delete();

        $response = $this->get('/install');

        $response->assertStatus(200);
    }

    #[Test]
    public function redirects_to_login_when_already_installed(): void
    {
        // Mark as installed
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function allows_completed_page_access_after_installation(): void
    {
        // Mark as installed
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        // Completed page has special exception
        $response = $this->get('/install/completed');

        $response->assertStatus(200);
    }

    #[Test]
    public function blocks_requirements_page_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/requirements');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function blocks_permissions_page_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/permissions');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function blocks_database_page_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/database');

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function blocks_admin_page_when_installed(): void
    {
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/install/admin');

        $response->assertRedirect(route('login'));
    }
}
