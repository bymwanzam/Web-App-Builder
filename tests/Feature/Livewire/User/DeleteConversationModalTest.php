<?php

namespace Tests\Feature\Livewire\User;

use App\Livewire\User\DeleteConversationModal;
use App\Models\AiConversation;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteConversationModalTest extends TestCase
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
        ]);

        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
        ]);
    }

    #[Test]
    public function it_renders_component()
    {
        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->assertSuccessful();
    }

    #[Test]
    public function it_shows_modal_on_delete_conversation_event()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->dispatch('delete-conversation', id: $conversation->id)
            ->assertSet('showModal', true)
            ->assertSet('conversationId', $conversation->id)
            ->assertSet('conversationTitle', 'Test Conversation');
    }

    #[Test]
    public function it_uses_default_title_for_untitled_conversations()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => null,
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->dispatch('delete-conversation', id: $conversation->id)
            ->assertSet('conversationTitle', 'Untitled conversation');
    }

    #[Test]
    public function it_does_not_show_modal_for_other_users_conversations()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);
        $conversation = AiConversation::create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Conversation',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->dispatch('delete-conversation', id: $conversation->id)
            ->assertSet('showModal', false)
            ->assertSet('conversationId', null);
    }

    #[Test]
    public function it_deletes_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->dispatch('delete-conversation', id: $conversation->id)
            ->call('delete')
            ->assertSet('showModal', false)
            ->assertDispatched('conversationDeleted')
            ->assertDispatched('show-toast');

        $this->assertDatabaseMissing('ai_conversations', ['id' => $conversation->id]);
    }

    #[Test]
    public function it_does_not_delete_other_users_conversations()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);
        $conversation = AiConversation::create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Conversation',
            'messages' => [],
        ]);

        // Manually set conversationId to bypass the event check
        Livewire::actingAs($this->user)
            ->test(DeleteConversationModal::class)
            ->set('conversationId', $conversation->id)
            ->call('delete');

        // Conversation should still exist
        $this->assertDatabaseHas('ai_conversations', ['id' => $conversation->id]);
    }
}
