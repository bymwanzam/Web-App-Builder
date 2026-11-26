<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppSplashscreenSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AppSplashscreenSettingsTest extends TestCase
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

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('splashscreen_background_type', $app->androidWebViewConfig->splashscreen_background_type)
            ->assertSet('splashscreen_background', $app->androidWebViewConfig->splashscreen_background)
            ->assertSet('splashscreen_duration', $app->androidWebViewConfig->splashscreen_duration)
            ->assertSet('splashscreen_animation', $app->androidWebViewConfig->splashscreen_animation)
            ->assertSet('splashscreen_logo_enabled', $app->androidWebViewConfig->splashscreen_logo_enabled)
            ->assertSet('splashscreen_text_theme', $app->androidWebViewConfig->splashscreen_text_theme);
    }

    public function test_background_type_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_background_type', '')
            ->call('save')
            ->assertHasErrors(['splashscreen_background_type' => 'required']);
    }

    public function test_background_type_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_background_type', 'invalid')
            ->call('save')
            ->assertHasErrors('splashscreen_background_type');
    }

    public function test_background_type_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['color', 'image'] as $type) {
            Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
                ->set('splashscreen_background_type', $type)
                ->assertHasNoErrors('splashscreen_background_type');
        }
    }

    public function test_background_color_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_background', null)
            ->call('save')
            ->assertHasNoErrors('splashscreen_background');
    }

    public function test_background_color_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_background', 'invalid-color')
            ->call('save')
            ->assertHasErrors('splashscreen_background');
    }

    public function test_background_image_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_image_file', null)
            ->call('save')
            ->assertHasNoErrors('splashscreen_image_file');
    }

    public function test_background_image_must_be_image_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('background.txt', 100);

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_image_file', $file)
            ->call('save')
            ->assertHasErrors(['splashscreen_image_file' => 'image']);
    }

    public function test_background_image_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('background.jpg')->size(3000); // 3MB

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_image_file', $file)
            ->call('save')
            ->assertHasErrors(['splashscreen_image_file' => 'max']);
    }

    public function test_duration_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_duration', '')
            ->call('save')
            ->assertHasErrors(['splashscreen_duration' => 'required']);
    }

    public function test_duration_must_be_integer()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_duration', 'not-a-number')
            ->call('save')
            ->assertHasErrors(['splashscreen_duration' => 'integer']);
    }

    public function test_duration_must_be_at_least_1()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_duration', 0)
            ->call('save')
            ->assertHasErrors(['splashscreen_duration' => 'min']);
    }

    public function test_duration_cannot_exceed_10()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_duration', 11)
            ->call('save')
            ->assertHasErrors(['splashscreen_duration' => 'max']);
    }

    public function test_animation_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_animation', '')
            ->call('save')
            ->assertHasErrors(['splashscreen_animation' => 'required']);
    }

    public function test_animation_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_animation', 'invalid')
            ->call('save')
            ->assertHasErrors('splashscreen_animation');
    }

    public function test_animation_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['fade', 'slide', 'zoom', 'none'] as $animation) {
            Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
                ->set('splashscreen_animation', $animation)
                ->assertHasNoErrors('splashscreen_animation');
        }
    }

    public function test_logo_enabled_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_enabled', '')
            ->call('save')
            ->assertHasErrors(['splashscreen_logo_enabled' => 'required']);
    }

    public function test_logo_enabled_must_be_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_enabled', 'not-a-boolean')
            ->call('save')
            ->assertHasErrors(['splashscreen_logo_enabled' => 'boolean']);
    }

    public function test_logo_file_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_file', null)
            ->call('save')
            ->assertHasNoErrors('splashscreen_logo_file');
    }

    public function test_logo_file_must_be_image_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('logo.txt', 100);

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_file', $file)
            ->call('save')
            ->assertHasErrors(['splashscreen_logo_file' => 'image']);
    }

    public function test_logo_file_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.jpg')->size(3000); // 3MB

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_file', $file)
            ->call('save')
            ->assertHasErrors(['splashscreen_logo_file' => 'max']);
    }

    public function test_title_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_title', null)
            ->call('save')
            ->assertHasNoErrors('splashscreen_title');
    }

    public function test_title_cannot_exceed_255_characters()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_title', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['splashscreen_title' => 'max']);
    }

    public function test_subtitle_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_subtitle', null)
            ->call('save')
            ->assertHasNoErrors('splashscreen_subtitle');
    }

    public function test_subtitle_cannot_exceed_255_characters()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_subtitle', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['splashscreen_subtitle' => 'max']);
    }

    public function test_text_theme_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_text_theme', '')
            ->call('save')
            ->assertHasErrors(['splashscreen_text_theme' => 'required']);
    }

    public function test_text_theme_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_text_theme', 'invalid')
            ->call('save')
            ->assertHasErrors('splashscreen_text_theme');
    }

    public function test_text_theme_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['light', 'dark'] as $theme) {
            Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
                ->set('splashscreen_text_theme', $theme)
                ->assertHasNoErrors('splashscreen_text_theme');
        }
    }

    public function test_splashscreen_settings_can_be_updated_successfully()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_background_type', 'color')
            ->set('splashscreen_background', '#000000')
            ->set('splashscreen_duration', 5)
            ->set('splashscreen_animation', 'zoom')
            ->set('splashscreen_logo_enabled', true)
            ->set('splashscreen_title', 'Welcome')
            ->set('splashscreen_subtitle', 'Loading...')
            ->set('splashscreen_text_theme', 'light')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $this->assertEquals('color', $app->androidWebViewConfig->splashscreen_background_type);
        $this->assertEquals('#000000', $app->androidWebViewConfig->splashscreen_background);
        $this->assertEquals(5, $app->androidWebViewConfig->splashscreen_duration);
        $this->assertEquals('zoom', $app->androidWebViewConfig->splashscreen_animation);
        $this->assertEquals(true, $app->androidWebViewConfig->splashscreen_logo_enabled);
        $this->assertEquals('Welcome', $app->androidWebViewConfig->splashscreen_title);
        $this->assertEquals('Loading...', $app->androidWebViewConfig->splashscreen_subtitle);
        $this->assertEquals('light', $app->androidWebViewConfig->splashscreen_text_theme);
    }

    public function test_background_image_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('background.png');

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_image_file', $file)
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertNotNull($app->androidWebViewConfig->splashscreen_image);
        Storage::disk('public')->assertExists($app->androidWebViewConfig->splashscreen_image);
    }

    public function test_logo_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.png');

        Livewire::test(AppSplashscreenSettings::class, ['app' => $app])
            ->set('splashscreen_logo_file', $file)
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $this->assertNotNull($app->androidWebViewConfig->splashscreen_logo);
        Storage::disk('public')->assertExists($app->androidWebViewConfig->splashscreen_logo);
    }
}
