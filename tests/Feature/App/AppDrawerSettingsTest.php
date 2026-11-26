<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppDrawerSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AppDrawerSettingsTest extends TestCase
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

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('drawer_mode', $app->androidWebViewConfig->drawer_mode)
            ->assertSet('drawer_logo_enabled', $app->androidWebViewConfig->drawer_logo_enabled)
            ->assertSet('drawer_text_theme', $app->androidWebViewConfig->drawer_text_theme);
    }

    public function test_drawer_mode_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_mode', '')
            ->call('save')
            ->assertHasErrors(['drawer_mode' => 'required']);
    }

    public function test_drawer_mode_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_mode', 'invalid')
            ->call('save')
            ->assertHasErrors('drawer_mode');
    }

    public function test_drawer_mode_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['default', 'color', 'image'] as $mode) {
            Livewire::test(AppDrawerSettings::class, ['app' => $app])
                ->set('drawer_mode', $mode)
                ->assertHasNoErrors('drawer_mode');
        }
    }

    public function test_drawer_background_color_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_color', null)
            ->call('save')
            ->assertHasNoErrors('drawer_background_color');
    }

    public function test_drawer_background_color_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_color', 'invalid-color')
            ->call('save')
            ->assertHasErrors('drawer_background_color');
    }

    public function test_drawer_background_image_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_image_file', null)
            ->call('save')
            ->assertHasNoErrors('drawer_background_image_file');
    }

    public function test_drawer_background_image_must_be_image_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->create('background.txt', 100);

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_image_file', $file)
            ->call('save')
            ->assertHasErrors(['drawer_background_image_file' => 'image']);
    }

    public function test_drawer_background_image_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('background.jpg')->size(3000); // 3MB

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_image_file', $file)
            ->call('save')
            ->assertHasErrors(['drawer_background_image_file' => 'max']);
    }

    public function test_drawer_logo_enabled_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_logo_enabled', '')
            ->call('save')
            ->assertHasErrors(['drawer_logo_enabled' => 'required']);
    }

    public function test_drawer_logo_enabled_must_be_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_logo_enabled', 'not-a-boolean')
            ->call('save')
            ->assertHasErrors(['drawer_logo_enabled' => 'boolean']);
    }

    public function test_drawer_title_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_title', null)
            ->call('save')
            ->assertHasNoErrors('drawer_title');
    }

    public function test_drawer_title_cannot_exceed_255_characters()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_title', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['drawer_title' => 'max']);
    }

    public function test_drawer_subtitle_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_subtitle', null)
            ->call('save')
            ->assertHasNoErrors('drawer_subtitle');
    }

    public function test_drawer_subtitle_cannot_exceed_255_characters()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_subtitle', str_repeat('a', 256))
            ->call('save')
            ->assertHasErrors(['drawer_subtitle' => 'max']);
    }

    public function test_drawer_text_theme_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_text_theme', '')
            ->call('save')
            ->assertHasErrors(['drawer_text_theme' => 'required']);
    }

    public function test_drawer_text_theme_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_text_theme', 'invalid')
            ->call('save')
            ->assertHasErrors('drawer_text_theme');
    }

    public function test_drawer_text_theme_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['light', 'dark'] as $theme) {
            Livewire::test(AppDrawerSettings::class, ['app' => $app])
                ->set('drawer_text_theme', $theme)
                ->assertHasNoErrors('drawer_text_theme');
        }
    }

    public function test_drawer_settings_can_be_updated_successfully()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_mode', 'color')
            ->set('drawer_background_color', '#FF5733')
            ->set('drawer_logo_enabled', true)
            ->set('drawer_title', 'My App')
            ->set('drawer_subtitle', 'Welcome!')
            ->set('drawer_text_theme', 'light')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertEquals('color', $config->drawer_mode);
        $this->assertEquals('#FF5733', $config->drawer_background_color);
        $this->assertEquals(true, $config->drawer_logo_enabled);
        $this->assertEquals('My App', $config->drawer_title);
        $this->assertEquals('Welcome!', $config->drawer_subtitle);
        $this->assertEquals('light', $config->drawer_text_theme);
    }

    public function test_drawer_background_image_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('background.png');

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_background_image_file', $file)
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertNotNull($config->drawer_background_image);
        Storage::disk('public')->assertExists($config->drawer_background_image);
    }

    public function test_drawer_logo_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.png');

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_logo_light_file', $file)
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertNotNull($config->drawer_logo_light);
        Storage::disk('public')->assertExists($config->drawer_logo_light);
    }

    // =====================================================
    // Drawer Item Color Tests
    // =====================================================

    public function test_drawer_icon_color_light_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_light', null)
            ->call('save')
            ->assertHasNoErrors('drawer_icon_color_light');
    }

    public function test_drawer_icon_color_light_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_light', 'invalid-color')
            ->call('save')
            ->assertHasErrors('drawer_icon_color_light');
    }

    public function test_drawer_icon_color_light_accepts_valid_hex_color()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_light', '#6B7280')
            ->call('save')
            ->assertHasNoErrors('drawer_icon_color_light');
    }

    public function test_drawer_icon_color_dark_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_dark', null)
            ->call('save')
            ->assertHasNoErrors('drawer_icon_color_dark');
    }

    public function test_drawer_icon_color_dark_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_dark', 'not-hex')
            ->call('save')
            ->assertHasErrors('drawer_icon_color_dark');
    }

    public function test_drawer_active_color_light_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_active_color_light', null)
            ->call('save')
            ->assertHasNoErrors('drawer_active_color_light');
    }

    public function test_drawer_active_color_light_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_active_color_light', 'rgb(255,0,0)')
            ->call('save')
            ->assertHasErrors('drawer_active_color_light');
    }

    public function test_drawer_active_color_light_accepts_valid_hex_color()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_active_color_light', '#3B82F6')
            ->call('save')
            ->assertHasNoErrors('drawer_active_color_light');
    }

    public function test_drawer_active_color_dark_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_active_color_dark', null)
            ->call('save')
            ->assertHasNoErrors('drawer_active_color_dark');
    }

    public function test_drawer_active_color_dark_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_active_color_dark', '3B82F6') // missing #
            ->call('save')
            ->assertHasErrors('drawer_active_color_dark');
    }

    public function test_drawer_item_colors_are_persisted_after_save()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->set('drawer_icon_color_light', '#FF5733')
            ->set('drawer_icon_color_dark', '#AABBCC')
            ->set('drawer_active_color_light', '#00FF00')
            ->set('drawer_active_color_dark', '#0000FF')
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertEquals('#FF5733', $config->drawer_icon_color_light);
        $this->assertEquals('#AABBCC', $config->drawer_icon_color_dark);
        $this->assertEquals('#00FF00', $config->drawer_active_color_light);
        $this->assertEquals('#0000FF', $config->drawer_active_color_dark);
    }

    public function test_drawer_item_colors_have_default_values_on_mount()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDrawerSettings::class, ['app' => $app])
            ->assertSet('drawer_icon_color_light', '#6B7280')
            ->assertSet('drawer_icon_color_dark', '#9CA3AF');
    }
}
