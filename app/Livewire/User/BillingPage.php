<?php

namespace App\Livewire\User;

use App\Models\Transaction;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BillingPage extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = [];

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when status filter changes.
     */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Refresh component when needed.
     */
    #[On('transactionUpdated')]
    #[On('subscriptionUpdated')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->with(['subscription.plan'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('transaction_id', 'like', '%'.$this->search.'%')
                        ->orWhere('payment_method', 'like', '%'.$this->search.'%');
                });
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->latest('transaction_date')
            ->paginate(10);

        $currentSubscription = $user->activeSubscription()->with('plan')->first();

        // Get pending Bank Transfer subscription (if exists)
        $pendingSubscription = $user->subscriptions()
            ->where('status', 'pending')
            ->where('payment_method', 'Bank Transfer')
            ->with('plan')
            ->latest()
            ->first();

        return view('livewire.user.billing-page', [
            'transactions' => $transactions,
            'currentSubscription' => $currentSubscription,
            'pendingSubscription' => $pendingSubscription,
        ]);
    }
}
