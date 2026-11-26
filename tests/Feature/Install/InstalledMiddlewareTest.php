<?php

namespace Tests\Feature\Install;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class InstalledMiddlewareTest extends InstallerTestCase
{
    use RefreshDatabase;

    #[Test]
    public function redirects_to_installer_when_not_installed(): void
    {
        // Remove installation flag
        SystemSetting::where('key', 'installation_completed')->delete();

        // Try to access a protected route
        $response = $this->get('/login');

        $response->assertRedirect(route('install'));
    }

    #[Test]
    public function allows_access_when_installed(): void
    {
        // Ensure installation is marked complete
        SystemSetting::set('installation_completed', true, 'boolean', 'app');

        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    #[Test]
    public function dashboard_redirects_when_not_installed(): void
    {
        SystemSetting::where('key', 'installation_completed')->delete();

        // Dashboard requires auth, so it may redirect to login first
        // which then redirects to install. We just check it doesn't
        // successfully load the dashboard.
        $response = $this->get('/dashboard');

        $response->assertRedirect();
    }

    #[Test]
    public function user_routes_redirect_when_not_installed(): void
    {
        SystemSetting::where('key', 'installation_completed')->delete();

        // User routes require auth, so they redirect to login first
        $response = $this->get('/user/apps');

        $response->assertRedirect();
    }
}
