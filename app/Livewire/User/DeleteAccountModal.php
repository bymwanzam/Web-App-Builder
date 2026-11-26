<?php

namespace App\Livewire\User;

use App\Models\AccountDeletionRequest;
use App\Notifications\AccountDeletionCancelledNotification;
use App\Notifications\AccountDeletionScheduledNotification;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteAccountModal extends Component
{
    public bool $showModal = false;

    public string $password = '';

    #[On('openDeleteAccountModal')]
    public function openModal(): void
    {
        $this->showModal = true;
        $this->password = '';
        $this->resetValidation();
    }

    public function requestDeletion(): void
    {
        $user = auth()->user();

        // Check for existing pending request
        $existingRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingRequest) {
            $this->dispatch('toast', type: 'error', message: 'You already have a pending account deletion request.');

            return;
        }

        // Validate password
        $this->validate([
            'password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (! Hash::check($value, $user->password)) {
                    $fail('The password is incorrect.');
                }
            }],
        ]);

        // Create deletion request
        $request = AccountDeletionRequest::createForUser($user);

        // Send notification with cancel link
        $user->notify(new AccountDeletionScheduledNotification($request));

        $this->showModal = false;
        $this->password = '';

        $this->dispatch('toast', type: 'success', message: 'Account deletion scheduled. Check your email for details.');
        $this->dispatch('closeDeleteAccountModal');
    }

    public function cancelDeletion(): void
    {
        $user = auth()->user();

        $request = AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if (! $request) {
            $this->dispatch('toast', type: 'error', message: 'No pending deletion request found.');

            return;
        }

        if (! $request->canBeCancelled()) {
            $this->dispatch('toast', type: 'error', message: 'This deletion request can no longer be cancelled.');

            return;
        }

        $request->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Send cancellation confirmation
        $user->notify(new AccountDeletionCancelledNotification);

        $this->dispatch('toast', type: 'success', message: 'Account deletion cancelled.');
    }

    public function render()
    {
        $user = auth()->user();

        $pendingRequest = AccountDeletionRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        return view('livewire.user.delete-account-modal', [
            'pendingDeletion' => $pendingRequest !== null,
            'deletionRequest' => $pendingRequest,
        ]);
    }
}
