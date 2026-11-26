<?php

namespace App\Livewire\User;

use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteApiKeyModal extends Component
{
    public $showModal = false;

    public $tokenId = null;

    public $tokenName = '';

    #[On('delete-api-key')]
    public function confirmDelete($id)
    {
        $token = PersonalAccessToken::where('id', $id)
            ->where('tokenable_id', auth()->id())
            ->where('tokenable_type', \App\Models\User::class)
            ->first();

        if ($token) {
            $this->tokenId = $token->id;
            $this->tokenName = $token->name;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['tokenId', 'tokenName']);
    }

    public function delete()
    {
        $token = PersonalAccessToken::where('id', $this->tokenId)
            ->where('tokenable_id', auth()->id())
            ->where('tokenable_type', \App\Models\User::class)
            ->first();

        if ($token) {
            $token->delete();
            $this->dispatch('show-toast',
                type: 'success',
                message: 'API key revoked successfully.'
            );
        }

        $this->closeModal();
        $this->dispatch('apiKeyDeleted');
    }

    public function render()
    {
        return view('livewire.user.delete-api-key-modal');
    }
}
