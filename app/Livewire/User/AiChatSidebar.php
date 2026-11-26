<?php

namespace App\Livewire\User;

use App\Models\AiConversation;
use Livewire\Attributes\On;
use Livewire\Component;

class AiChatSidebar extends Component
{
    public array $conversations = [];

    public ?int $activeConversationId = null;

    public int $conversationCount = 0;

    public function mount(?int $activeConversationId = null)
    {
        $this->activeConversationId = $activeConversationId;
        $this->loadConversations();
    }

    #[On('conversationStarted')]
    public function onConversationStarted(int $conversationId)
    {
        $this->activeConversationId = $conversationId;
        $this->loadConversations();

        // Update URL without full navigation (conversation already loaded in main component)
        $this->dispatch('updateUrl', url: route('user.ai.conversation', $conversationId));
    }

    #[On('conversationUpdated')]
    public function loadConversations()
    {
        $this->conversations = AiConversation::where('user_id', auth()->id())
            ->main() // Filter out widget conversations
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($conv) => [
                'id' => $conv->id,
                'title' => $conv->title ?? 'New conversation',
                'appName' => $conv->app?->name,
                'updatedAt' => $conv->updated_at->diffForHumans(),
            ])
            ->toArray();

        // Update conversation count and dispatch event for layout
        $this->conversationCount = count($this->conversations);
        $this->dispatch('sidebarStateChanged', hasConversations: $this->conversationCount > 0);
        // Also dispatch to browser/Alpine for sidebar visibility
        $hasConversations = $this->conversationCount > 0 ? 'true' : 'false';
        $this->js("window.dispatchEvent(new CustomEvent('sidebar-state-changed', { detail: { hasConversations: {$hasConversations} } }))");
    }

    public function selectConversation(int $conversationId)
    {
        $this->redirect(route('user.ai.conversation', $conversationId), navigate: true);
    }

    #[On('conversationDeleted')]
    public function onConversationDeleted(int $deletedId, bool $wasActive = false)
    {
        // Clear active state if this was the active conversation
        if ($wasActive || $this->activeConversationId === $deletedId) {
            $this->activeConversationId = null;
        }

        $this->loadConversations();
    }

    public function newConversation()
    {
        $this->activeConversationId = null;
        $this->dispatch('startNewConversation');
    }

    public function render()
    {
        return view('livewire.user.ai-chat-sidebar');
    }
}
