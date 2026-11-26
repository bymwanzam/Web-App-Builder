<?php

namespace Tests\Feature;

use App\Livewire\User\CreateApiKeyModal;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateApiKeyModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $planWithApi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->planWithApi = Plan::factory()->create([
            'name' => 'Pro',
            'can_use_api' => true,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->planWithApi->id,
        ]);
    }

    #[Test]
    public function it_renders_for_authenticated_user()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_opens_modal_on_event()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->assertSet('showModal', false)
            ->dispatch('open-create-api-key-modal')
            ->assertSet('showModal', true);
    }

    #[Test]
    public function it_closes_modal_and_resets_form()
    {
        // closeModal only hides the modal (to prevent flash during animation)
        // Form is reset when modal is reopened via open()
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Test Token')
            ->set('scopes', ['apps:read'])
            ->call('closeModal')
            ->assertSet('showModal', false)
            // Reopen modal - should reset form
            ->dispatch('open-create-api-key-modal')
            ->assertSet('showModal', true)
            ->assertSet('name', '')
            ->assertSet('scopes', [])
            ->assertSet('newToken', null);
    }

    #[Test]
    public function it_validates_name_is_required()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', '')
            ->call('create')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function it_validates_name_max_length()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', str_repeat('a', 256))
            ->call('create')
            ->assertHasErrors(['name' => 'max']);
    }

    #[Test]
    public function it_creates_api_key_with_all_permissions()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Full Access Token')
            ->set('scopes', []) // Empty means all permissions
            ->call('create')
            ->assertSet('newToken', fn ($value) => ! empty($value))
            ->assertDispatched('apiKeyCreated');

        // Verify token was created with wildcard ability
        $token = $this->user->tokens()->first();
        $this->assertEquals('Full Access Token', $token->name);
        $this->assertEquals(['*'], $token->abilities);
    }

    #[Test]
    public function it_creates_api_key_with_specific_scopes()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Limited Token')
            ->set('scopes', ['apps:read', 'builds:read'])
            ->call('create')
            ->assertSet('newToken', fn ($value) => ! empty($value))
            ->assertDispatched('apiKeyCreated');

        // Verify token was created with specific abilities
        $token = $this->user->tokens()->first();
        $this->assertEquals('Limited Token', $token->name);
        $this->assertEquals(['apps:read', 'builds:read'], $token->abilities);
    }

    #[Test]
    public function it_shows_token_after_creation()
    {
        $component = Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Test Token')
            ->call('create');

        // Token should be set and visible
        $component->assertSet('newToken', fn ($value) => str_contains($value, '|'));
        $component->assertSee('API Key Created');
        $component->assertSee('Copy your API key now');
    }

    #[Test]
    public function it_prevents_closing_modal_when_token_shown()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Test Token')
            ->call('create')
            ->assertSet('newToken', fn ($value) => ! empty($value))
            ->assertSet('showModal', true);
    }

    #[Test]
    public function it_shows_error_when_plan_does_not_allow_api()
    {
        $planWithoutApi = Plan::factory()->create([
            'can_use_api' => false,
        ]);

        $userWithoutApi = User::factory()->create([
            'plan_id' => $planWithoutApi->id,
        ]);

        Livewire::actingAs($userWithoutApi)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Test Token')
            ->call('create')
            ->assertDispatched('show-toast')
            ->assertSet('showModal', false);

        // Verify no token was created
        $this->assertEquals(0, $userWithoutApi->tokens()->count());
    }

    #[Test]
    public function it_displays_available_scopes()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->assertSee('apps:read')
            ->assertSee('apps:write')
            ->assertSee('builds:read')
            ->assertSee('builds:trigger')
            ->assertSee('builds:download')
            ->assertSee('notifications:read')
            ->assertSee('notifications:send')
            ->assertSee('account:read');
    }

    #[Test]
    public function it_resets_form_when_modal_opens()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->set('name', 'Old Name')
            ->set('scopes', ['apps:read'])
            ->set('newToken', 'old-token')
            ->dispatch('open-create-api-key-modal')
            ->assertSet('name', '')
            ->assertSet('scopes', [])
            ->assertSet('newToken', null);
    }

    #[Test]
    public function it_can_select_multiple_scopes()
    {
        Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Multi-scope Token')
            ->set('scopes', ['apps:read', 'apps:write', 'builds:read', 'builds:trigger'])
            ->call('create')
            ->assertDispatched('apiKeyCreated');

        $token = $this->user->tokens()->first();
        $this->assertCount(4, $token->abilities);
    }

    #[Test]
    public function it_creates_unique_tokens()
    {
        // Create first token
        $component1 = Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Token 1')
            ->call('create');

        // Get the displayed token from the component
        $newToken1 = $component1->get('newToken');

        // Create second token
        $component2 = Livewire::actingAs($this->user)
            ->test(CreateApiKeyModal::class)
            ->dispatch('open-create-api-key-modal')
            ->set('name', 'Token 2')
            ->call('create');

        $newToken2 = $component2->get('newToken');

        // Both tokens should exist and be different
        $this->assertNotNull($newToken1);
        $this->assertNotNull($newToken2);
        $this->assertNotEquals($newToken1, $newToken2);
        $this->assertEquals(2, $this->user->tokens()->count());
    }
}
