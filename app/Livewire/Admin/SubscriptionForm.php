<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class SubscriptionForm extends Component
{
    public $showModal = false;

    public $user_id;

    public $plan_id;

    public $status = 'active';

    public $payment_method = 'Manual';

    public $renewal_at;

    #[On('create-subscription')]
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Subscription management is disabled in demo mode.'
            );

            return;
        }

        $validated = $this->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'status' => 'required|in:active,pending,expired',
            'renewal_at' => 'required|date',
        ]);

        $plan = Plan::find($this->plan_id);
        $user = User::find($this->user_id);

        // Cancel all other active/pending subscriptions for this user
        Subscription::where('user_id', $this->user_id)
            ->whereIn('status', ['active', 'pending'])
            ->update([
                'status' => 'cancelled',
                'ends_at' => now(),
            ]);

        // Create new subscription
        $subscription = Subscription::create([
            'user_id' => $this->user_id,
            'plan_id' => $this->plan_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'amount' => $plan->price,
            'renewal_at' => Carbon::parse($this->renewal_at),
        ]);

        // If subscription is active, update user plan and refill build credits
        if ($this->status === 'active') {
            $user->update(['plan_id' => $this->plan_id]);
            $user->refillBuildCredits();
        }

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Subscription created successfully!'
        );
        $this->closeModal();
        $this->dispatch('subscriptionUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->user_id = '';
        $this->plan_id = '';
        $this->status = 'active';
        $this->payment_method = 'Manual';
        $this->renewal_at = Carbon::now()->addMonth()->format('Y-m-d\TH:i');
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::orderBy('email')->get();
        $plans = Plan::all();

        return view('livewire.admin.subscription-form', [
            'users' => $users,
            'plans' => $plans,
        ]);
    }
}
