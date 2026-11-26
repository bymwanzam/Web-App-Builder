<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppGeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppGeneralSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithApp()
    {
        $user = User::factory()->create();
        $app = App::factory()->forUser($user)->create();
        $this->actingAs($user);

        return [$user, $app];
    }

    public function test_component_renders_for_authenticated_user()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('name', $app->name)
            ->assertSet('url', $app->androidWebViewConfig->url)
            ->assertSet('app_id', $app->androidWebViewConfig->package_name)
            ->assertSet('screen_orientation', $app->androidWebViewConfig->screen_orientation)
            ->assertSet('app_language', $app->androidWebViewConfig->app_language);
    }

    public function test_guest_cannot_access_app_settings()
    {
        $app = App::factory()->create();

        $this->get('/user/apps/'.$app->id)
            ->assertRedirect('/login');
    }

    public function test_name_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    public function test_name_cannot_exceed_255_characters()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('name', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['name' => 'max']);
    }

    public function test_url_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('url', '')
            ->call('save')
            ->assertHasErrors(['url' => 'required']);
    }

    public function test_url_must_be_valid_format()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('url', 'not-a-valid-url')
            ->call('save')
            ->assertHasErrors(['url' => 'url']);
    }

    public function test_app_id_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_id', '')
            ->call('save')
            ->assertHasErrors(['app_id' => 'required']);
    }

    public function test_app_id_must_be_in_reverse_domain_format()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_id', 'invalid-format')
            ->call('save')
            ->assertHasErrors('app_id');
    }

    public function test_app_id_must_be_unique()
    {
        [$user, $app] = $this->createUserWithApp();
        $otherApp = App::factory()->forUser($user)->create();
        $otherApp->androidWebViewConfig->update(['package_name' => 'com.existing.app']);

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_id', 'com.existing.app')
            ->call('save')
            ->assertHasErrors(['app_id' => 'unique']);
    }

    public function test_screen_orientation_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('screen_orientation', '')
            ->call('save')
            ->assertHasErrors(['screen_orientation' => 'required']);
    }

    public function test_screen_orientation_must_be_valid_value()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('screen_orientation', 'invalid')
            ->call('save')
            ->assertHasErrors('screen_orientation');
    }

    public function test_screen_orientation_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        // Test that valid orientations don't trigger validation errors
        foreach (['system', 'portrait', 'landscape'] as $orientation) {
            Livewire::test(AppGeneralSettings::class, ['app' => $app])
                ->set('screen_orientation', $orientation)
                ->assertHasNoErrors('screen_orientation');
        }
    }

    public function test_app_language_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_language', '')
            ->call('save')
            ->assertHasErrors(['app_language' => 'required']);
    }

    public function test_app_language_must_be_en()
    {
        [$user, $app] = $this->createUserWithApp();

        // Test invalid language codes
        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_language', 'ES')
            ->call('save')
            ->assertHasErrors('app_language');

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_language', 'FR')
            ->call('save')
            ->assertHasErrors('app_language');

        // Test valid language code
        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('app_language', 'en')
            ->call('save')
            ->assertHasNoErrors('app_language');
    }

    public function test_general_settings_can_be_updated_successfully()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('name', 'Updated App Name')
            ->set('url', 'https://updated-app.com')
            ->set('screen_orientation', 'landscape')
            ->set('app_language', 'en')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $this->assertEquals('Updated App Name', $app->name);
        $this->assertEquals('https://updated-app.com', $app->androidWebViewConfig->url);
        $this->assertEquals('landscape', $app->androidWebViewConfig->screen_orientation);
        $this->assertEquals('en', $app->androidWebViewConfig->app_language);
    }

    public function test_real_time_validation_works()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->set('url', 'not-a-valid-url')
            ->assertHasErrors('url');
    }

    public function test_copy_app_id_dispatches_clipboard_event()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppGeneralSettings::class, ['app' => $app])
            ->call('copyAppId')
            ->assertDispatched('copy-to-clipboard', text: $app->androidWebViewConfig->package_name);
    }
}
