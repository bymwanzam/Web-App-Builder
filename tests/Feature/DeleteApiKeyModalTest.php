<?php

namespace Tests\Feature;

use App\Livewire\User\DeleteApiKeyModal;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteApiKeyModalTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = Plan::factory()->create([
            'name' => 'Pro',
            'can_use_api' => true,
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
    }

    #[Test]
    public function it_renders_for_authenticated_user()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_opens_modal_with_token_info_on_event()
    {
        $token = $this->user->createToken('Test Token', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->assertSet('showModal', false)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->assertSet('showModal', true)
            ->assertSet('tokenId', $tokenModel->id)
            ->assertSet('tokenName', 'Test Token');
    }

    #[Test]
    public function it_does_not_open_modal_for_nonexistent_token()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: 99999)
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_does_not_open_modal_for_other_users_token()
    {
        $otherUser = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
        $token = $otherUser->createToken('Other User Token', ['*']);
        $tokenModel = $otherUser->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_deletes_token_successfully()
    {
        $token = $this->user->createToken('Token to Delete', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->call('delete')
            ->assertSet('showModal', false)
            ->assertDispatched('show-toast')
            ->assertDispatched('apiKeyDeleted');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenModel->id,
        ]);
    }

    #[Test]
    public function it_does_not_delete_other_users_token()
    {
        $otherUser = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
        $token = $otherUser->createToken('Other User Token', ['*']);
        $tokenModel = $otherUser->tokens()->first();

        // Set the token ID manually (simulating a tampering attempt)
        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->set('tokenId', $tokenModel->id)
            ->call('delete');

        // Token should still exist
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenModel->id,
        ]);
    }

    #[Test]
    public function it_shows_token_name_in_confirmation_message()
    {
        $token = $this->user->createToken('My Important API Key', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->assertSee('My Important API Key');
    }

    #[Test]
    public function it_closes_modal_after_deletion()
    {
        $token = $this->user->createToken('Token', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->assertSet('showModal', true)
            ->call('delete')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function it_dispatches_api_key_deleted_event()
    {
        $token = $this->user->createToken('Token', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->call('delete')
            ->assertDispatched('apiKeyDeleted');
    }

    #[Test]
    public function it_shows_success_toast_after_deletion()
    {
        $token = $this->user->createToken('Token', ['*']);
        $tokenModel = $this->user->tokens()->first();

        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->dispatch('delete-api-key', id: $tokenModel->id)
            ->call('delete')
            ->assertDispatched('show-toast', type: 'success');
    }

    #[Test]
    public function it_can_delete_multiple_tokens_sequentially()
    {
        $token1 = $this->user->createToken('Token 1', ['*']);
        $token2 = $this->user->createToken('Token 2', ['*']);

        $tokenModel1 = $this->user->tokens()->where('name', 'Token 1')->first();
        $tokenModel2 = $this->user->tokens()->where('name', 'Token 2')->first();

        $component = Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class);

        // Delete first token
        $component->dispatch('delete-api-key', id: $tokenModel1->id)
            ->call('delete')
            ->assertDispatched('apiKeyDeleted');

        // Delete second token
        $component->dispatch('delete-api-key', id: $tokenModel2->id)
            ->call('delete')
            ->assertDispatched('apiKeyDeleted');

        $this->assertEquals(0, $this->user->tokens()->count());
    }

    #[Test]
    public function it_handles_deletion_of_already_deleted_token()
    {
        $token = $this->user->createToken('Token', ['*']);
        $tokenModel = $this->user->tokens()->first();
        $tokenId = $tokenModel->id;

        // Delete token directly
        $tokenModel->delete();

        // Try to delete via component
        Livewire::actingAs($this->user)
            ->test(DeleteApiKeyModal::class)
            ->set('tokenId', $tokenId)
            ->call('delete')
            ->assertSet('showModal', false);
    }
}
