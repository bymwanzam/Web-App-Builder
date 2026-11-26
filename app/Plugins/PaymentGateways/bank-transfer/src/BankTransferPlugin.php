<?php

namespace App\Plugins\PaymentGateways;

use App\Contracts\PaymentGatewayPlugin;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class BankTransferPlugin implements PaymentGatewayPlugin
{
    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'Bank Transfer';
    }

    public function getDescription(): string
    {
        return 'Accept manual bank transfer payments with admin approval workflow';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/bank-transfer/icon.jpg';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getAuthor(): string
    {
        return 'Titan Systems';
    }

    public function getAuthorUrl(): string
    {
        return 'https://codecanyon.net/user/titansystems';
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['instructions']);
    }

    public function validateConfig(array $config): void
    {
        if (empty($config['instructions'])) {
            throw new \Exception('Bank transfer instructions are required');
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        // Check for existing pending subscription
        $existingPending = Subscription::where('user_id', $user->id)
            ->where('payment_method', 'Bank Transfer')
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            throw new \Exception('You already have a pending bank transfer. Please complete or cancel it first.');
        }

        // Calculate amount (simple case - no taxes or coupons in MVP)
        $amount = round($plan->price, 2);

        // Calculate renewal date from plan intervals
        $renewalAt = $this->calculateRenewalDate($plan);

        // Create subscription with pending status
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_method' => 'Bank Transfer',
            'subscription_id' => 'BT-'.strtoupper(Str::random(10)),
            'status' => 'pending',
            'amount' => $amount,
            'renewal_at' => $renewalAt,
        ]);

        // Create pending transaction record with bank transfer instructions in metadata
        $transaction = Transaction::create([
            'transaction_id' => 'TXN-'.strtoupper(Str::random(10)),
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'external_transaction_id' => null,
            'amount' => $amount,
            'currency' => $plan->currency,
            'status' => 'pending',
            'type' => 'subscription',
            'payment_method' => 'Bank Transfer',
            'transaction_date' => now(),
            'metadata' => [
                'bank_transfer_instructions' => $this->config['instructions'] ?? null,
            ],
        ]);

        // Notify admin of new pending bank transfer (don't let email failure break payment)
        try {
            $adminEmail = SystemSetting::get('admin_notification_email');
            $enabledEvents = SystemSetting::get('admin_notification_events', []);

            if ($adminEmail && in_array('bank_transfer_pending', $enabledEvents)) {
                Notification::route('mail', $adminEmail)
                    ->notify(new \App\Notifications\BankTransferPendingNotification($transaction));
            } else {
                \Log::warning('Bank Transfer notification not sent: admin email not configured or event disabled', [
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'amount' => $amount,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send bank transfer admin notification', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Return URL string for Livewire to handle redirect
        // Note: Success toast is dispatched by the calling Livewire component
        return route('user.billing');
    }

    public function handleWebhook(Request $request): Response
    {
        // No webhook for manual bank transfers
        return response('Bank Transfer does not use webhooks', 404);
    }

    public function callback(Request $request): RedirectResponse
    {
        // No callback for manual bank transfers
        return redirect()->route('user.billing');
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        // For bank transfer, just update the local subscription
        // No remote cancellation needed
        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
        ]);
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        // Bank transfer has no remote status - return local status
        $subscription = Subscription::where('subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            throw new \Exception("Subscription not found: {$subscriptionId}");
        }

        return [
            'status' => $subscription->status,
            'renewal_at' => $subscription->renewal_at,
            'amount' => $subscription->amount,
        ];
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
