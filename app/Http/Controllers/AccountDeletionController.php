<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Notifications\AccountDeletionCancelledNotification;

class AccountDeletionController extends Controller
{
    /**
     * Cancel account deletion via email link.
     */
    public function cancel(string $token)
    {
        $request = AccountDeletionRequest::where('cancellation_token', $token)->first();

        if (! $request) {
            return view('account.deletion-cancelled', [
                'success' => false,
                'message' => 'Invalid cancellation link.',
            ]);
        }

        if ($request->status !== 'pending') {
            $statusMessage = match ($request->status) {
                'cancelled' => 'This deletion request was already cancelled.',
                'completed' => 'This account has already been deleted.',
                default => 'This deletion request is no longer valid.',
            };

            return view('account.deletion-cancelled', [
                'success' => false,
                'message' => $statusMessage,
            ]);
        }

        if (! $request->canBeCancelled()) {
            return view('account.deletion-cancelled', [
                'success' => false,
                'message' => 'This deletion request can no longer be cancelled.',
            ]);
        }

        $request->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Send confirmation notification
        if ($request->user) {
            $request->user->notify(new AccountDeletionCancelledNotification);
        }

        return view('account.deletion-cancelled', [
            'success' => true,
            'message' => 'Your account deletion has been cancelled. Your account will remain active.',
        ]);
    }
}
