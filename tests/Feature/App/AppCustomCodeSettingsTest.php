<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\Plan;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppCustomCodeSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AppCustomCodeSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithApp()
    {
        // Create a plan that allows custom code
        $plan = Plan::factory()->create([
            'can_use_custom_code' => true,
            'status' => 'active',
        ]);

        $user = User::factory()->create(['plan_id' => $plan->id]);
        $app = App::factory()->forUser($user)->create();
        $this->actingAs($user);

        return [$user, $app];
    }

    public function test_component_renders_for_authenticated_user()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('custom_css', $app->androidWebViewConfig->custom_css)
            ->assertSet('custom_js', $app->androidWebViewConfig->custom_js);
    }

    public function test_custom_css_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_css', null)
            ->call('save')
            ->assertHasNoErrors('custom_css');
    }

    public function test_custom_css_cannot_exceed_max_length()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_css', str_repeat('a', 100001))
            ->call('save')
            ->assertHasErrors(['custom_css' => 'max']);
    }

    public function test_custom_css_accepts_valid_css()
    {
        [$user, $app] = $this->createUserWithApp();

        $css = 'body { background-color: #000; }';

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_css', $css)
            ->call('save')
            ->assertHasNoErrors('custom_css');

        $app->refresh();
        $this->assertEquals($css, $app->androidWebViewConfig->custom_css);
    }

    public function test_custom_js_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_js', null)
            ->call('save')
            ->assertHasNoErrors('custom_js');
    }

    public function test_custom_js_cannot_exceed_max_length()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_js', str_repeat('a', 100001))
            ->call('save')
            ->assertHasErrors(['custom_js' => 'max']);
    }

    public function test_custom_js_accepts_valid_javascript()
    {
        [$user, $app] = $this->createUserWithApp();

        $js = 'console.log("Hello World");';

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_js', $js)
            ->call('save')
            ->assertHasNoErrors('custom_js');

        $app->refresh();
        $this->assertEquals($js, $app->androidWebViewConfig->custom_js);
    }

    public function test_custom_code_settings_can_be_updated_successfully()
    {
        [$user, $app] = $this->createUserWithApp();

        $css = '.custom-class { color: red; }';
        $js = 'alert("Test");';

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_css', $css)
            ->set('custom_js', $js)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast')
            ->assertDispatched('settings-updated');

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertEquals($css, $config->custom_css);
        $this->assertEquals($js, $config->custom_js);
    }

    public function test_custom_code_can_be_cleared()
    {
        [$user, $app] = $this->createUserWithApp();

        $app->androidWebViewConfig->update([
            'custom_css' => 'body { color: blue; }',
            'custom_js' => 'console.log("test");',
        ]);

        Livewire::test(AppCustomCodeSettings::class, ['app' => $app])
            ->set('custom_css', null)
            ->set('custom_js', null)
            ->call('save')
            ->assertHasNoErrors();

        $app->refresh();
        $config = $app->androidWebViewConfig;
        $this->assertNull($config->custom_css);
        $this->assertNull($config->custom_js);
    }
}
