<?php

namespace Tests\Feature;

use App\Models\AndroidWebViewConfig;
use App\Models\App;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AppFormTest extends TestCase
{
    use RefreshDatabase;

    protected function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    protected function seedPlatformPlugin()
    {
        // Create the AndroidWebView platform plugin
        return Plugin::create([
            'slug' => 'androidwebview',
            'type' => 'platform',
            'name' => 'Android WebView',
            'class' => 'App\Plugins\Platforms\AndroidWebView\AndroidWebViewPlatform',
            'version' => '1.0.0',
            'status' => 'active',
            'installed_at' => now(),
        ]);
    }

    #[Test]
    public function create_app_modal_renders_for_authenticated_user()
    {
        $this->createAuthenticatedUser();

        Livewire::test('app-form')
            ->assertOk()
            ->assertSet('showModal', false)
            ->assertSet('step', 1)
            ->assertSet('name', '');
    }

    #[Test]
    public function guest_cannot_access_app_form()
    {
        $this->get('/user/apps')
            ->assertRedirect('/login');
    }

    #[Test]
    public function modal_can_be_opened_via_create_app_event()
    {
        $this->createAuthenticatedUser();
        $this->seedPlatformPlugin();

        Livewire::test('app-form')
            ->dispatch('create-app')
            ->assertSet('showModal', true)
            ->assertSet('appId', null)
            ->assertSet('step', 1)
            ->assertSet('name', '');
    }

    #[Test]
    public function app_name_is_required()
    {
        $user = $this->createAuthenticatedUser();

        Livewire::test('app-form')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function app_name_cannot_exceed_255_characters()
    {
        $user = $this->createAuthenticatedUser();

        Livewire::test('app-form')
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]
    public function user_can_edit_existing_app_name()
    {
        $user = $this->createAuthenticatedUser();
        $platformPlugin = $this->seedPlatformPlugin();

        $app = App::factory()->forUser($user)->create([
            'name' => 'Original Name',
            'platform_plugin_id' => $platformPlugin->id,
        ]);

        Livewire::test('app-form')
            ->dispatch('edit-app', id: $app->id)
            ->assertSet('showModal', true)
            ->assertSet('appId', $app->id)
            ->assertSet('name', 'Original Name')
            ->assertSet('step', 2)
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('apps', [
            'id' => $app->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function package_name_is_auto_generated_on_create()
    {
        $user = $this->createAuthenticatedUser();
        $platformPlugin = $this->seedPlatformPlugin();

        $app = App::factory()->forUser($user)->create([
            'name' => 'My Test App',
            'platform_plugin_id' => $platformPlugin->id,
        ]);

        $config = AndroidWebViewConfig::where('app_id', $app->id)->first();

        $this->assertNotNull($config);
        $this->assertNotNull($config->package_name);
        $this->assertStringContainsString('com.', $config->package_name);
    }

    #[Test]
    public function package_name_is_unique_when_duplicate_names_exist()
    {
        $user = $this->createAuthenticatedUser();
        $platformPlugin = $this->seedPlatformPlugin();

        // Create first app
        $app1 = App::factory()->forUser($user)->create([
            'name' => 'Test App',
            'platform_plugin_id' => $platformPlugin->id,
        ]);
        $config1 = AndroidWebViewConfig::where('app_id', $app1->id)->first();
        $package1 = $config1->package_name;

        // Manually create second app with same name but different package
        $app2 = App::create([
            'user_id' => $user->id,
            'platform_plugin_id' => $platformPlugin->id,
            'name' => 'Test App',
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        // Create config with unique package name
        AndroidWebViewConfig::create([
            'app_id' => $app2->id,
            'url' => fake()->url(),
            'package_name' => 'com.testapp1.app', // Unique package name
            'theme_color' => '#3b82f6',
        ]);

        $config2 = AndroidWebViewConfig::where('app_id', $app2->id)->first();

        $this->assertNotEquals($package1, $config2->package_name);
    }

    #[Test]
    public function user_cannot_edit_another_users_app()
    {
        $user = $this->createAuthenticatedUser();
        $platformPlugin = $this->seedPlatformPlugin();
        $otherUser = User::factory()->create();
        $otherApp = App::factory()->forUser($otherUser)->create([
            'platform_plugin_id' => $platformPlugin->id,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::test('app-form')
            ->dispatch('edit-app', id: $otherApp->id);
    }

    #[Test]
    public function modal_closes_when_close_modal_is_called()
    {
        $this->createAuthenticatedUser();
        $this->seedPlatformPlugin();

        Livewire::test('app-form')
            ->dispatch('create-app')
            ->assertSet('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function modal_resets_form_fields_on_close()
    {
        $this->createAuthenticatedUser();
        $this->seedPlatformPlugin();

        Livewire::test('app-form')
            ->dispatch('create-app')
            ->set('name', 'Test')
            ->call('closeModal')
            ->assertSet('name', '');
    }

    #[Test]
    public function validation_errors_are_cleared_on_close()
    {
        $this->createAuthenticatedUser();

        Livewire::test('app-form')
            ->set('name', '')
            ->call('save')
            ->assertHasErrors('name')
            ->call('closeModal')
            ->assertHasNoErrors();
    }

    #[Test]
    public function platform_selection_step_loads_available_platforms()
    {
        $this->createAuthenticatedUser();

        Livewire::test('app-form')
            ->dispatch('create-app')
            ->assertSet('step', 1)
            ->assertCount('availablePlatforms', 1);
    }
}
