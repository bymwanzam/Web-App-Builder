<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppDesignSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppDesignSettingsTest extends TestCase
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

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('theme_color', $app->androidWebViewConfig->theme_color)
            ->assertSet('layout_template', $app->androidWebViewConfig->layout_template)
            ->assertSet('progress_indicator', $app->androidWebViewConfig->progress_indicator);
    }

    public function test_theme_color_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('theme_color', '')
            ->call('save')
            ->assertHasErrors(['theme_color' => 'required']);
    }

    public function test_theme_color_must_be_valid_hex()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('theme_color', 'not-hex-color')
            ->call('save')
            ->assertHasErrors('theme_color');
    }

    public function test_theme_color_accepts_valid_hex_colors()
    {
        [$user, $app] = $this->createUserWithApp();

        $validColors = ['#FF5733', '#00FF00', '#0000FF', '#FFFFFF', '#000000'];

        foreach ($validColors as $color) {
            Livewire::test(AppDesignSettings::class, ['app' => $app])
                ->set('theme_color', $color)
                ->assertHasNoErrors('theme_color');
        }
    }

    public function test_layout_template_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('layout_template', '')
            ->call('save')
            ->assertHasErrors(['layout_template' => 'required']);
    }

    public function test_layout_template_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('layout_template', 'invalid')
            ->call('save')
            ->assertHasErrors('layout_template');
    }

    public function test_layout_template_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['app_bar_drawer', 'app_bar_tabs', 'app_bar', 'blank'] as $template) {
            Livewire::test(AppDesignSettings::class, ['app' => $app])
                ->set('layout_template', $template)
                ->assertHasNoErrors('layout_template');
        }
    }

    public function test_progress_indicator_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('progress_indicator', '')
            ->call('save')
            ->assertHasErrors(['progress_indicator' => 'required']);
    }

    public function test_progress_indicator_must_be_valid_enum()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('progress_indicator', 'invalid')
            ->call('save')
            ->assertHasErrors('progress_indicator');
    }

    public function test_progress_indicator_accepts_valid_values()
    {
        [$user, $app] = $this->createUserWithApp();

        foreach (['disable', 'linear', 'circular'] as $indicator) {
            Livewire::test(AppDesignSettings::class, ['app' => $app])
                ->set('progress_indicator', $indicator)
                ->assertHasNoErrors('progress_indicator');
        }
    }

    public function test_progress_indicator_color_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('progress_indicator_color', null)
            ->call('save')
            ->assertHasNoErrors('progress_indicator_color');
    }

    public function test_progress_indicator_color_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('progress_indicator_color', 'invalid-color')
            ->call('save')
            ->assertHasErrors('progress_indicator_color');
    }

    public function test_show_app_bar_title_is_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('show_app_bar_title', true)
            ->assertHasNoErrors('show_app_bar_title')
            ->set('show_app_bar_title', false)
            ->assertHasNoErrors('show_app_bar_title');
    }

    public function test_nav_tab_icon_color_light_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('nav_tab_icon_color_light', null)
            ->call('save')
            ->assertHasNoErrors('nav_tab_icon_color_light');
    }

    public function test_nav_tab_icon_color_light_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('nav_tab_icon_color_light', 'invalid')
            ->call('save')
            ->assertHasErrors('nav_tab_icon_color_light');
    }

    public function test_nav_tab_icon_color_dark_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('nav_tab_icon_color_dark', null)
            ->call('save')
            ->assertHasNoErrors('nav_tab_icon_color_dark');
    }

    public function test_nav_tab_icon_color_dark_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('nav_tab_icon_color_dark', 'invalid')
            ->call('save')
            ->assertHasErrors('nav_tab_icon_color_dark');
    }

    public function test_active_tab_color_light_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('active_tab_color_light', null)
            ->call('save')
            ->assertHasNoErrors('active_tab_color_light');
    }

    public function test_active_tab_color_light_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('active_tab_color_light', 'invalid')
            ->call('save')
            ->assertHasErrors('active_tab_color_light');
    }

    public function test_active_tab_color_dark_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('active_tab_color_dark', null)
            ->call('save')
            ->assertHasNoErrors('active_tab_color_dark');
    }

    public function test_active_tab_color_dark_must_be_valid_hex_when_provided()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('active_tab_color_dark', 'invalid')
            ->call('save')
            ->assertHasErrors('active_tab_color_dark');
    }

    public function test_design_settings_can_be_updated_successfully()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->set('theme_color', '#FF5733')
            ->set('layout_template', 'app_bar_tabs')
            ->set('progress_indicator', 'circular')
            ->set('progress_indicator_color', '#00FF00')
            ->set('show_app_bar_title', false)
            ->set('nav_tab_icon_color_light', '#6b7280')
            ->set('nav_tab_icon_color_dark', '#9CA3AF')
            ->set('active_tab_color_light', '#3b82f6')
            ->set('active_tab_color_dark', '#60A5FA')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertEquals('#FF5733', $config->theme_color);
        $this->assertEquals('app_bar_tabs', $config->layout_template);
        $this->assertEquals('circular', $config->progress_indicator);
        $this->assertEquals('#00FF00', $config->progress_indicator_color);
        $this->assertEquals(false, $config->show_app_bar_title);
        $this->assertEquals('#6b7280', $config->nav_tab_icon_color_light);
        $this->assertEquals('#9CA3AF', $config->nav_tab_icon_color_dark);
        $this->assertEquals('#3b82f6', $config->active_tab_color_light);
        $this->assertEquals('#60A5FA', $config->active_tab_color_dark);
    }

    public function test_clear_color_method_works_for_progress_indicator_color()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update(['progress_indicator_color' => '#FF5733']);

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->call('clearColor', 'progress_indicator_color')
            ->assertSet('progress_indicator_color', null);
    }

    public function test_clear_color_method_works_for_nav_tab_icon_color_light()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update(['nav_tab_icon_color_light' => '#FF5733']);

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->call('clearColor', 'nav_tab_icon_color_light')
            ->assertSet('nav_tab_icon_color_light', null);
    }

    public function test_clear_color_method_works_for_nav_tab_icon_color_dark()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update(['nav_tab_icon_color_dark' => '#FF5733']);

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->call('clearColor', 'nav_tab_icon_color_dark')
            ->assertSet('nav_tab_icon_color_dark', null);
    }

    public function test_clear_color_method_works_for_active_tab_color_light()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update(['active_tab_color_light' => '#FF5733']);

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->call('clearColor', 'active_tab_color_light')
            ->assertSet('active_tab_color_light', null);
    }

    public function test_clear_color_method_works_for_active_tab_color_dark()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update(['active_tab_color_dark' => '#FF5733']);

        Livewire::test(AppDesignSettings::class, ['app' => $app])
            ->call('clearColor', 'active_tab_color_dark')
            ->assertSet('active_tab_color_dark', null);
    }
}
