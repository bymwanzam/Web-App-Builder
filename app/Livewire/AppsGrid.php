<?php

namespace App\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AppsGrid extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 12;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[On('appUpdated')]
    #[On('appDeleted')]
    public function refresh()
    {
        // Refresh component
    }

    public function render()
    {
        $apps = App::query()
            ->where('user_id', auth()->id())
            ->with([
                'builds' => function ($query) {
                    $query->latest()->limit(1);
                },
                'platformPlugin',
            ])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.apps-grid', [
            'apps' => $apps,
        ]);
    }
}
