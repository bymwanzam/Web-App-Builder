<?php

namespace App\Livewire\User;

use App\Models\AiConversation;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteConversationModal extends Component
{
    public $showModal = false;

    public $conversationId = null;

    public $conversationTitle = '';

    public bool $isActiveConversation = false;

    #[On('delete-conversation')]
    public function confirmDelete($id, $isActive = false)
    {
        $conversation = AiConversation::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($conversation) {
            $this->conversationId = $conversation->id;
            $this->conversationTitle = $conversation->title ?? 'Untitled conversation';
            $this->isActiveConversation = $isActive;
            $this->showModal = true;
        }
    }

    public function delete()
    {
        $deletedId = $this->conversationId;
        $wasActive = $this->isActiveConversation;

        $conversation = AiConversation::where('id', $deletedId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $conversation) {
            $this->showModal = false;

            return;
        }

        $conversation->delete();

        $this->showModal = false;
        $this->isActiveConversation = false;

        // If we deleted the active conversation, use Livewire.navigate() to remount fresh
        // This avoids hydration errors (trying to fetch the deleted model)
        // Use session flash for toast to avoid event dispatch racing with navigation
        if ($wasActive) {
            session()->flash('toast', [
                'type' => 'success',
                'message' => 'Conversation deleted successfully',
            ]);
            $this->js("Livewire.navigate('".route('user.ai')."')");

            return;
        }

        // For non-active conversations, show toast and notify components
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Conversation deleted successfully'
        );
        $this->dispatch('conversationDeleted', deletedId: $deletedId, wasActive: false);
    }

    public function render()
    {
        return view('livewire.user.delete-conversation-modal');
    }
}
