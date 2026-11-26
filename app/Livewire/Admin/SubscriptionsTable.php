<?php

namespace App\Livewire\Admin;

use App\Models\Subscription;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionsTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = [];

    public $planFilter = [];

    #[On('subscriptionUpdated')]
    public function refreshSubscriptions()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPlanFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('email', 'like', '%'.$this->search.'%')
                        ->orWhere('name', 'like', '%'.$this->search.'%');
                })
                    ->orWhereHas('plan', function ($q) {
                        $q->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->when(count($this->planFilter) > 0, function ($query) {
                $query->whereHas('plan', function ($q) {
                    $q->whereIn('name', $this->planFilter);
                });
            })
            ->orderBy('id', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.subscriptions-table', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
