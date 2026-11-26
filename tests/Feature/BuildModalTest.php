<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\BuildModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuildModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected App $testApp;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed builders (required for tests to pass)
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\AppBuilderSeeder']);

        // Seed plans (required for user to have access to builders)
        $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\PlanSeeder']);

        // Create a user with build credits and a plan
        $plan = \App\Models\Plan::where('name', 'Free')->first();
        $this->user = User::factory()->create([
            'build_credits' => 10,
            'plan_id' => $plan->id,
        ]);

        // Create an app for testing (factory automatically creates androidWebViewConfig)
        $this->testApp = App::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    #[Test]
    public function it_opens_modal_when_event_dispatched()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->assertSet('showModal', false)
            ->dispatch('openBuildModal')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function it_closes_modal_when_close_method_called()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_sets_platform_to_android_webview_when_modal_opens()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->dispatch('openBuildModal')
            ->assertSet('platform', 'android-webview');
    }

    #[Test]
    public function it_updates_build_type_property_when_build_type_selected()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->assertSet('buildType', 'debug')
            ->set('buildType', 'release')
            ->assertSet('buildType', 'release');
    }

    #[Test]
    public function it_returns_default_credit_cost_when_no_builder_selected()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(BuildModal::class, ['app' => $this->testApp]);

        // Default cost is 1 credit when no builder is selected
        $this->assertEquals(1, $component->instance()->getBuildCreditCost());
    }

    #[Test]
    public function it_shows_signing_warning_for_android_release_build_without_keystore()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'release')
            ->set('selectedKeystoreId', null)
            ->assertSet('showSigningWarning', true)
            ->assertSee('keystore');
    }

    #[Test]
    public function it_hides_signing_warning_for_debug_builds()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'debug')
            ->assertSet('showSigningWarning', false);
    }

    #[Test]
    public function it_hides_signing_warning_when_android_has_keystore()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'release')
            ->set('selectedKeystoreId', 1)
            ->assertSet('showSigningWarning', false);
    }

    #[Test]
    public function it_disables_submission_for_android_release_build_without_keystore()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'release')
            ->assertSet('canSubmit', false);
    }

    #[Test]
    public function it_allows_submission_for_debug_builds()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'debug')
            ->assertSet('canSubmit', true);
    }

    #[Test]
    public function it_allows_submission_for_release_builds_with_signing_keys()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildType', 'release')
            ->set('selectedKeystoreId', 1)
            ->assertSet('canSubmit', true);
    }

    #[Test]
    public function it_resets_form_when_modal_opens()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('buildType', 'release')
            ->set('buildFormat', 'aab')
            ->dispatch('openBuildModal')
            ->assertSet('platform', 'android-webview')
            ->assertSet('buildType', 'debug')
            ->assertSet('buildFormat', 'apk');
    }

    #[Test]
    public function it_defaults_build_format_to_apk_for_android()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->assertSet('buildFormat', 'apk');
    }

    #[Test]
    public function it_allows_changing_build_format_to_aab_for_android()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('platform', 'android-webview')
            ->set('buildFormat', 'aab')
            ->assertSet('buildFormat', 'aab');
    }

    #[Test]
    public function it_shows_format_selection_for_android_webview()
    {
        $this->actingAs($this->user);

        // AndroidWebView platform should show format selection when modal is open
        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->dispatch('openBuildModal')
            ->assertSee('Output Format')
            ->assertSee('APK')
            ->assertSee('AAB');
    }

    #[Test]
    public function it_resets_build_format_when_modal_opens()
    {
        $this->actingAs($this->user);

        Livewire::test(BuildModal::class, ['app' => $this->testApp])
            ->set('buildFormat', 'aab')
            ->dispatch('openBuildModal')
            ->assertSet('buildFormat', 'apk');
    }
}
