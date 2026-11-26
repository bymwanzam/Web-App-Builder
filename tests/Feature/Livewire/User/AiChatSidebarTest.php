<?php

namespace Tests\Feature\Livewire\User;

use App\Livewire\User\AiChatSidebar;
use App\Models\AiConversation;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiChatSidebarTest extends TestCase
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
            ->test(AiChatSidebar::class)
            ->assertSuccessful();
    }

    #[Test]
    public function it_loads_user_conversations()
    {
        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'First Conversation',
            'messages' => [],
        ]);

        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Second Conversation',
            'messages' => [],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class);

        $conversations = $component->get('conversations');

        $this->assertCount(2, $conversations);
    }

    #[Test]
    public function it_does_not_load_other_users_conversations()
    {
        $otherUser = User::factory()->create(['plan_id' => $this->plan->id]);

        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'My Conversation',
            'messages' => [],
        ]);

        AiConversation::create([
            'user_id' => $otherUser->id,
            'title' => 'Other User Conversation',
            'messages' => [],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class);

        $conversations = $component->get('conversations');

        $this->assertCount(1, $conversations);
        $this->assertEquals('My Conversation', $conversations[0]['title']);
    }

    #[Test]
    public function it_sets_active_conversation_id_on_mount()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class, ['activeConversationId' => $conversation->id])
            ->assertSet('activeConversationId', $conversation->id);
    }

    #[Test]
    public function it_selects_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Test Conversation',
            'messages' => [],
        ]);

        // selectConversation redirects to the conversation page
        Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class)
            ->call('selectConversation', $conversation->id)
            ->assertRedirect(route('user.ai.conversation', $conversation->id));
    }

    #[Test]
    public function it_starts_new_conversation()
    {
        Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class, ['activeConversationId' => 123])
            ->call('newConversation')
            ->assertSet('activeConversationId', null)
            ->assertDispatched('startNewConversation');
    }

    #[Test]
    public function it_updates_on_conversation_started_event()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'New Conversation',
            'messages' => [],
        ]);

        Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class)
            ->dispatch('conversationStarted', conversationId: $conversation->id)
            ->assertSet('activeConversationId', $conversation->id);
    }

    #[Test]
    public function it_reloads_on_conversation_updated_event()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
            'messages' => [],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class);

        // Update conversation title
        $conversation->update(['title' => 'Updated Title']);

        $component->dispatch('conversationUpdated');

        $conversations = $component->get('conversations');
        $this->assertEquals('Updated Title', $conversations[0]['title']);
    }

    #[Test]
    public function it_handles_deleted_active_conversation()
    {
        $conversation = AiConversation::create([
            'user_id' => $this->user->id,
            'title' => 'Will Be Deleted',
            'messages' => [],
        ]);

        $conversationId = $conversation->id;

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class, ['activeConversationId' => $conversationId]);

        // Delete the conversation
        $conversation->delete();

        // Sidebar clears active state when receiving conversationDeleted event
        // (navigation is now handled by AiChat component)
        $component->dispatch('conversationDeleted', deletedId: $conversationId)
            ->assertSet('activeConversationId', null);
    }

    #[Test]
    public function it_uses_default_title_for_untitled_conversations()
    {
        AiConversation::create([
            'user_id' => $this->user->id,
            'title' => null,
            'messages' => [],
        ]);

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class);

        $conversations = $component->get('conversations');

        $this->assertEquals('New conversation', $conversations[0]['title']);
    }

    #[Test]
    public function it_limits_conversations_to_20()
    {
        for ($i = 0; $i < 25; $i++) {
            AiConversation::create([
                'user_id' => $this->user->id,
                'title' => "Conversation {$i}",
                'messages' => [],
            ]);
        }

        $component = Livewire::actingAs($this->user)
            ->test(AiChatSidebar::class);

        $conversations = $component->get('conversations');

        $this->assertCount(20, $conversations);
    }
}
