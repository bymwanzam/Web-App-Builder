<?php

namespace App\Livewire\Admin;

use App\Models\Subscription;
use Livewire\Attributes\On;
use Livewire\Component;

class CancelSubscriptionModal extends Component
{
    public $subscriptionId;

    public $userEmail;

    public $showModal = false;

    #[On('cancel-subscription')]
    public function confirmCancel($id, $user)
    {
        $this->subscriptionId = $id;
        $this->userEmail = $user;
        $this->showModal = true;
    }

    public function cancel()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Subscription management is disabled in demo mode.'
            );
            $this->closeModal();

            return;
        }

        if ($this->subscriptionId) {
            Subscription::findOrFail($this->subscriptionId)->delete();
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Subscription cancelled successfully!'
            );
            $this->closeModal();
            $this->dispatch('subscriptionUpdated');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->subscriptionId = null;
        $this->userEmail = null;
    }

    public function render()
    {
        return view('livewire.admin.cancel-subscription-modal');
    }
}
