<?php

namespace App\Livewire\User;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ApiKeysTable extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('apiKeyCreated')]
    #[On('apiKeyDeleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        // Check if user's plan allows API access
        $canUseApi = $user->canUseApi();

        $tokens = collect();
        if ($canUseApi) {
            $tokens = $user->tokens()
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        }

        return view('livewire.user.api-keys-table', [
            'tokens' => $tokens,
            'canUseApi' => $canUseApi,
        ]);
    }
}
