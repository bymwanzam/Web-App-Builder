<?php

namespace App\Livewire\Admin;

use App\Models\AppBuilder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BuildersTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = [];

    public $queueCounts = [];

    public function mount()
    {
        $this->fetchQueueCounts();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[On('builderUpdated')]
    #[On('builderDeleted')]
    public function refresh()
    {
        $this->resetPage();
        $this->fetchQueueCounts();
    }

    /**
     * Fetch real-time queue counts from all active builders.
     * Called on mount and via wire:poll for auto-refresh.
     */
    public function fetchQueueCounts()
    {
        $builders = AppBuilder::active()->get();

        foreach ($builders as $builder) {
            $this->queueCounts[$builder->id] = $builder->getQueueCount();
        }
    }

    public function render()
    {
        $builders = AppBuilder::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('url', 'like', '%'.$this->search.'%');
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.builders-table', [
            'builders' => $builders,
        ]);
    }
}
