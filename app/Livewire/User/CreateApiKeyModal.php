<?php

namespace App\Livewire\User;

use Livewire\Attributes\On;
use Livewire\Component;

class CreateApiKeyModal extends Component
{
    public $showModal = false;

    public $name = '';

    public $scopes = [];

    public $newToken = null;

    public $availableScopes = [
        'apps:read' => 'Read apps',
        'apps:write' => 'Create and update apps',
        'builds:read' => 'Read build status',
        'builds:trigger' => 'Trigger new builds',
        'builds:download' => 'Download build artifacts',
        'notifications:read' => 'Read notifications',
        'notifications:send' => 'Send push notifications',
        'account:read' => 'Read account information',
    ];

    #[On('open-create-api-key-modal')]
    public function open()
    {
        $this->reset(['name', 'scopes', 'newToken']);
        $this->showModal = true;
    }

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'array',
        ]);

        $user = auth()->user();

        // Verify user can use API
        if (! $user->canUseApi()) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Your plan does not include API access.'
            );
            $this->showModal = false;

            return;
        }

        // Create token with selected scopes (empty array means all abilities)
        $abilities = empty($this->scopes) ? ['*'] : $this->scopes;
        $token = $user->createToken($this->name, $abilities);

        $this->newToken = $token->plainTextToken;
        $this->dispatch('apiKeyCreated');
    }

    public function closeModal()
    {
        // Only hide the modal - don't reset state yet to avoid flash during animation
        // State is reset in open() when modal is reopened
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.user.create-api-key-modal');
    }
}
