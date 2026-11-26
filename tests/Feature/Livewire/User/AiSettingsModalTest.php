<?php

namespace Tests\Feature\Livewire\User;

use App\Livewire\User\AiSettingsModal;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserAiSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiSettingsModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::factory()->create([
            'can_use_ai_chat' => true,
            'monthly_ai_tokens' => 10000,
            'allow_user_ai_api_key' => true,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
    }

    #[Test]
    public function it_renders_component()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->assertSuccessful();
    }

    #[Test]
    public function it_opens_modal_on_event()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->assertSet('showModal', false)
            ->dispatch('open-ai-settings-modal')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function it_closes_modal()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->dispatch('open-ai-settings-modal')
            ->assertSet('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_loads_user_settings()
    {
        UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'preferred_model' => 'gpt-4.1',
            'openai_api_key' => 'sk-test-key',
        ]);

        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->assertSet('preferred_provider', 'openai')
            ->assertSet('preferred_model', 'gpt-4.1')
            ->assertSet('openai_api_key', '••••••••••••••••');
    }

    #[Test]
    public function it_defaults_to_system_provider()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->assertSet('preferred_provider', 'system');
    }

    #[Test]
    public function it_shows_can_use_own_key_based_on_plan()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->assertSet('canUseOwnKey', true);
    }

    #[Test]
    public function it_shows_cannot_use_own_key_for_restricted_plan()
    {
        $restrictedPlan = Plan::factory()->create(['allow_user_ai_api_key' => false]);
        $restrictedUser = User::factory()->create(['plan_id' => $restrictedPlan->id]);

        Livewire::actingAs($restrictedUser)
            ->test(AiSettingsModal::class)
            ->assertSet('canUseOwnKey', false);
    }

    #[Test]
    public function it_loads_available_providers()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class);

        $providers = $component->get('availableProviders');

        $this->assertArrayHasKey('openai', $providers);
        $this->assertArrayHasKey('anthropic', $providers);
    }

    #[Test]
    public function it_updates_models_when_provider_changes()
    {
        $component = Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'openai');

        $models = $component->get('availableModels');

        $this->assertArrayHasKey('gpt-4.1', $models);
        $this->assertArrayHasKey('gpt-4o', $models);
    }

    #[Test]
    public function it_clears_models_when_system_provider_selected()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'openai')
            ->set('preferred_provider', 'system')
            ->assertSet('availableModels', []);
    }

    #[Test]
    public function it_resets_model_when_provider_changes()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'openai')
            ->set('preferred_model', 'gpt-4.1')
            ->set('preferred_provider', 'anthropic')
            ->assertSet('preferred_model', null);
    }

    #[Test]
    public function it_saves_settings_and_closes_modal()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->dispatch('open-ai-settings-modal')
            ->set('preferred_provider', 'openai')
            ->set('openai_api_key', 'sk-new-key')
            ->call('save')
            ->assertDispatched('show-toast')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('user_ai_settings', [
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
        ]);

        $settings = UserAiSettings::where('user_id', $this->user->id)->first();
        $this->assertEquals('sk-new-key', $settings->openai_api_key);
    }

    #[Test]
    public function it_does_not_update_masked_keys()
    {
        UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-original-key',
        ]);

        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->call('save');

        $settings = UserAiSettings::where('user_id', $this->user->id)->first();
        $this->assertEquals('sk-original-key', $settings->openai_api_key);
    }

    #[Test]
    public function it_prevents_saving_when_plan_disallows()
    {
        $restrictedPlan = Plan::factory()->create(['allow_user_ai_api_key' => false]);
        $restrictedUser = User::factory()->create(['plan_id' => $restrictedPlan->id]);

        Livewire::actingAs($restrictedUser)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'openai')
            ->set('openai_api_key', 'sk-test-key')
            ->call('save')
            ->assertDispatched('show-toast', type: 'error');

        $this->assertDatabaseMissing('user_ai_settings', [
            'user_id' => $restrictedUser->id,
        ]);
    }

    #[Test]
    public function it_clears_openai_key()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('openai_api_key', 'sk-test-key')
            ->call('clearOpenAiKey')
            ->assertSet('openai_api_key', null);
    }

    #[Test]
    public function it_clears_anthropic_key()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('anthropic_api_key', 'sk-ant-test-key')
            ->call('clearAnthropicKey')
            ->assertSet('anthropic_api_key', null);
    }

    #[Test]
    public function it_masks_keys_after_save()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'openai')
            ->set('openai_api_key', 'sk-new-key')
            ->call('save')
            ->assertSet('openai_api_key', '••••••••••••••••');
    }

    #[Test]
    public function it_saves_anthropic_key_when_anthropic_selected()
    {
        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->set('preferred_provider', 'anthropic')
            ->set('anthropic_api_key', 'sk-ant-new-key')
            ->call('save');

        $settings = UserAiSettings::where('user_id', $this->user->id)->first();
        $this->assertEquals('sk-ant-new-key', $settings->anthropic_api_key);
    }

    #[Test]
    public function it_requires_new_key_for_testing_openai()
    {
        UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'openai',
            'openai_api_key' => 'sk-existing-key',
        ]);

        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->call('testOpenAiKey')
            ->assertDispatched('show-toast', type: 'error', message: 'Please enter a new API key to test');
    }

    #[Test]
    public function it_requires_new_key_for_testing_anthropic()
    {
        UserAiSettings::create([
            'user_id' => $this->user->id,
            'preferred_provider' => 'anthropic',
            'anthropic_api_key' => 'sk-ant-existing-key',
        ]);

        Livewire::actingAs($this->user)
            ->test(AiSettingsModal::class)
            ->call('testAnthropicKey')
            ->assertDispatched('show-toast', type: 'error', message: 'Please enter a new API key to test');
    }
}
