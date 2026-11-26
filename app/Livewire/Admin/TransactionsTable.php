<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = [];

    public $confirmApproveId = null;

    public $confirmDeclineId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Show approve confirmation modal.
     */
    public function confirmApprove($id)
    {
        $this->confirmApproveId = $id;
    }

    /**
     * Show decline confirmation modal.
     */
    public function confirmDecline($id)
    {
        $this->confirmDeclineId = $id;
    }

    /**
     * Approve a pending bank transfer transaction.
     */
    public function approveTransaction($id)
    {
        $this->confirmApproveId = null;

        try {
            $transaction = Transaction::with(['user', 'subscription'])->findOrFail($id);

            // Verify transaction is pending and is bank transfer
            if ($transaction->status !== 'pending' || $transaction->payment_method !== 'Bank Transfer') {
                session()->flash('error', 'Only pending bank transfer transactions can be approved.');

                return;
            }

            // Update transaction status
            $transaction->update(['status' => 'completed']);

            // Activate subscription
            if ($transaction->subscription) {
                // Calculate renewal date from plan intervals
                $plan = $transaction->subscription->plan;
                $renewalAt = $this->calculateRenewalDate($plan);

                $transaction->subscription->update([
                    'status' => 'active',
                    'renewal_at' => $renewalAt,
                ]);

                // Cancel all other active/pending subscriptions
                Subscription::where('user_id', $transaction->user_id)
                    ->where('id', '!=', $transaction->subscription->id)
                    ->whereIn('status', ['active', 'pending'])
                    ->update([
                        'status' => 'cancelled',
                        'ends_at' => now(),
                    ]);

                // Update user's plan
                $transaction->user->update([
                    'plan_id' => $transaction->subscription->plan_id,
                ]);

                // Refill build credits
                $transaction->user->refillBuildCredits();
            }

            // Notify user (don't let email failure break the approval)
            try {
                $transaction->user->notify(new \App\Notifications\BankTransferApprovedNotification($transaction));
            } catch (\Exception $notificationException) {
                \Log::error('Failed to send BankTransferApprovedNotification', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Transaction approved successfully.'
            );
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to approve transaction: '.$e->getMessage()
            );
        }
    }

    /**
     * Decline a pending bank transfer transaction.
     */
    public function declineTransaction($id)
    {
        $this->confirmDeclineId = null;

        try {
            $transaction = Transaction::with(['user', 'subscription'])->findOrFail($id);

            // Verify transaction is pending and is bank transfer
            if ($transaction->status !== 'pending' || $transaction->payment_method !== 'Bank Transfer') {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Only pending bank transfer transactions can be declined.'
                );

                return;
            }

            // Update transaction status
            $transaction->update(['status' => 'failed']);

            // Cancel subscription
            if ($transaction->subscription) {
                $transaction->subscription->update([
                    'status' => 'cancelled',
                    'ends_at' => now(),
                ]);
            }

            // Notify user (don't let email failure break the decline)
            try {
                $transaction->user->notify(new \App\Notifications\BankTransferDeclinedNotification($transaction));
            } catch (\Exception $notificationException) {
                \Log::error('Failed to send BankTransferDeclinedNotification', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $transaction->user_id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Transaction declined.'
            );
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to decline transaction: '.$e->getMessage()
            );
        }
    }

    public function render()
    {
        $transactions = Transaction::with(['user', 'subscription'])
            ->when($this->search, function ($query) {
                $query->where('transaction_id', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('email', 'like', '%'.$this->search.'%')
                            ->orWhere('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);

        return view('livewire.admin.transactions-table', [
            'transactions' => $transactions,
        ]);
    }

    private function calculateRenewalDate(Plan $plan): \Carbon\Carbon
    {
        $intervalUnit = $plan->interval_unit ?? 'month';
        $intervalCount = $plan->interval_count ?? 1;

        return match ($intervalUnit) {
            'day' => now()->addDays($intervalCount),
            'week' => now()->addWeeks($intervalCount),
            'month' => now()->addMonths($intervalCount),
            'year' => now()->addYears($intervalCount),
            default => now()->addMonth(),
        };
    }
}
