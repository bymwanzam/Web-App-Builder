<?php

namespace App\Plugins\PaymentGateways;

use App\Contracts\PaymentGatewayPlugin;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AdminPaymentNotification;
use App\Notifications\PaymentCompletedNotification;
use App\Notifications\SubscriptionActivatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class RazorpayPlugin implements PaymentGatewayPlugin
{
    private array $config;

    private Api $api;

    private const ZERO_DECIMAL_CURRENCIES = ['BIF', 'CLP', 'DJF', 'GNF', 'ISK', 'JPY', 'KMF', 'KRW', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

    private const THREE_DECIMAL_CURRENCIES = ['BHD', 'IQD', 'JOD', 'KWD', 'OMR', 'TND'];

    private const PERIOD_MAP = [
        'day' => 'daily',
        'week' => 'weekly',
        'month' => 'monthly',
        'year' => 'yearly',
    ];

    private const PERIOD_TO_DAYS = [
        'day' => 1,
        'week' => 7,
        'month' => 30,
        'year' => 365,
    ];

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];

        if (! empty($this->config['key_id']) && ! empty($this->config['key_secret'])) {
            $this->api = new Api($this->config['key_id'], $this->config['key_secret']);
        }
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'Razorpay';
    }

    public function getDescription(): string
    {
        return 'Accept payments in India and globally with Razorpay - Support for UPI, Cards, NetBanking, Wallets';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/razorpay/icon.jpg';
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
        return ! empty($this->config['key_id']) && ! empty($this->config['key_secret']);
    }

    public function validateConfig(array $config): void
    {
        if (empty($config['key_id'])) {
            throw new \Exception('Key ID is required');
        }

        if (empty($config['key_secret'])) {
            throw new \Exception('Key Secret is required');
        }

        if (empty($config['webhook_secret'])) {
            throw new \Exception('Webhook Secret is required for security');
        }

        // Test API connection
        try {
            $api = new Api($config['key_id'], $config['key_secret']);
            // Simple API call to verify credentials
            $api->payment->all(['count' => 1]);
        } catch (\Exception $e) {
            throw new \Exception('Invalid API credentials: '.$e->getMessage());
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        try {
            // Calculate amount based on currency decimals
            $amount = $this->convertAmount($plan->price, $plan->currency);

            // Generate cache ID for metadata storage
            $cacheId = Str::uuid()->toString();

            Cache::put($cacheId, [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ], 86400); // 24 hours

            // Create Razorpay plan
            $intervalUnit = $plan->interval_unit ?? 'month';
            $intervalCount = $plan->interval_count ?? 1;

            $razorpayPlan = $this->api->plan->create([
                'period' => self::PERIOD_MAP[$intervalUnit] ?? 'monthly',
                'interval' => $intervalCount,
                'item' => [
                    'name' => $plan->name,
                    'description' => 'Subscription to '.$plan->name,
                    'amount' => $amount,
                    'currency' => $plan->currency,
                ],
            ]);

            // Calculate total_count for 5 years
            $daysPerCycle = $intervalCount * (self::PERIOD_TO_DAYS[$intervalUnit] ?? 30);
            $totalCount = (int) ceil((5 * 365) / $daysPerCycle);

            // Create subscription
            $subscription = $this->api->subscription->create([
                'plan_id' => $razorpayPlan->id,
                'total_count' => $totalCount,
                'notes' => [
                    'cache_id' => $cacheId,
                ],
            ]);

            Log::info('Razorpay subscription created', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

            // Redirect to Razorpay short URL
            return $subscription->short_url;
        } catch (\Exception $e) {
            Log::error('Razorpay payment initialization failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw new \Exception('Payment initialization failed: '.$e->getMessage());
        }
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug('Razorpay Webhook Received', ['payload' => $request->all()]);

        // Verify webhook signature
        if (! empty($this->config['webhook_secret'])) {
            if (! $this->verifyWebhookSignature($request)) {
                Log::error('Razorpay Webhook Signature Verification Failed');

                return response('Invalid signature', 401);
            }
        }

        try {
            $payload = $request->json()->all();
            $event = $payload['event'] ?? '';

            if (empty($event)) {
                return response('No event type', 400);
            }

            // Handle subscription events
            if (str_starts_with($event, 'subscription.')) {
                $this->handleSubscriptionEvent($event, $payload);
            }

            // Handle refund events
            if ($event === 'refund.processed') {
                $this->handleRefundProcessed($payload);
            }

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('Razorpay webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Razorpay from retrying
            return response('Webhook received but processing failed', 200);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        // Redirect back to billing page
        // Webhook will handle the actual subscription activation
        return redirect()->route('user.billing');
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        try {
            $this->api->subscription->fetch($subscription->subscription_id)->cancel([
                'cancel_at_cycle_end' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay subscription cancellation failed', [
                'subscription_id' => $subscription->subscription_id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Cancellation failed: '.$e->getMessage());
        }
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        try {
            $razorpaySubscription = $this->api->subscription->fetch($subscriptionId);

            return [
                'status' => $razorpaySubscription->status,
                'current_start' => $razorpaySubscription->current_start,
                'current_end' => $razorpaySubscription->current_end,
                'ended_at' => $razorpaySubscription->ended_at ?? null,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve subscription: '.$e->getMessage());
        }
    }

    // Private Helper Methods
    private function convertAmount(float $amount, string $currency): int
    {
        $currency = strtoupper($currency);

        if (in_array($currency, self::THREE_DECIMAL_CURRENCIES)) {
            return (int) round($amount * 1000, 0);
        } elseif (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (int) round($amount, 0);
        } else {
            return (int) round($amount * 100, 0);
        }
    }

    private function reverseAmount(int $amount, string $currency): float
    {
        $currency = strtoupper($currency);

        if (in_array($currency, self::THREE_DECIMAL_CURRENCIES)) {
            return (float) $amount / 1000;
        } elseif (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (float) $amount;
        } else {
            return (float) $amount / 100;
        }
    }

    private function handleSubscriptionEvent(string $event, array $payload): void
    {
        $subscriptionData = $payload['payload']['subscription']['entity'] ?? [];
        $subscriptionId = $subscriptionData['id'] ?? null;

        $subscription = Subscription::where('subscription_id', $subscriptionId)->first();

        if (! $subscription && in_array($event, ['subscription.authenticated', 'subscription.activated'])) {
            // Create new subscription from cached metadata
            $notes = $subscriptionData['notes'] ?? [];
            $cacheId = $notes['cache_id'] ?? null;

            if (! $cacheId) {
                Log::error('Razorpay subscription cache ID not found');

                return;
            }

            $metadata = Cache::get($cacheId);
            if (! $metadata) {
                Log::error("Razorpay subscription metadata not found for cache_id: {$cacheId}");

                return;
            }

            $plan = Plan::find($metadata['plan_id']);
            if (! $plan) {
                return;
            }

            $subscription = Subscription::create([
                'user_id' => $metadata['user_id'],
                'plan_id' => $metadata['plan_id'],
                'payment_method' => 'Razorpay',
                'subscription_id' => $subscriptionId,
                'status' => 'active',
                'amount' => $plan->price,
                'renewal_at' => isset($subscriptionData['current_end'])
                    ? \Carbon\Carbon::createFromTimestamp($subscriptionData['current_end'])
                    : now()->addMonth(),
            ]);

            // Update user plan and refill credits
            $user = $subscription->user;
            if ($user) {
                // Cancel all other subscriptions
                Subscription::where('user_id', $user->id)
                    ->where('id', '!=', $subscription->id)
                    ->whereIn('status', ['active', 'pending'])
                    ->update([
                        'status' => 'cancelled',
                        'ends_at' => now(),
                    ]);

                $user->update([
                    'plan_id' => $subscription->plan_id,
                    'build_credits' => $plan->monthly_build_credits,
                ]);

                // Send user notification
                try {
                    $user->notify(new SubscriptionActivatedNotification($subscription));
                } catch (\Exception $e) {
                    Log::error('Failed to send SubscriptionActivatedNotification', [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send admin notification
                AdminPaymentNotification::sendIfEnabled(
                    'subscription_activated',
                    $user,
                    $subscription
                );
            }

            // Create transaction for first payment
            if ($event === 'subscription.activated') {
                Transaction::create([
                    'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                    'user_id' => $metadata['user_id'],
                    'subscription_id' => $subscription->id,
                    'external_transaction_id' => $subscriptionId,
                    'amount' => $plan->price,
                    'currency' => $plan->currency,
                    'status' => 'completed',
                    'type' => 'subscription',
                    'payment_method' => 'Razorpay',
                    'transaction_date' => now(),
                ]);
            }
        } elseif ($subscription) {
            // Update existing subscription
            $status = match ($subscriptionData['status'] ?? '') {
                'active', 'resumed' => 'active',
                'pending', 'halted', 'paused' => 'cancelled', // Suspended
                'expired', 'completed' => 'expired',
                'cancelled' => 'cancelled',
                default => 'pending'
            };

            $subscription->update(['status' => $status]);

            // Refill credits on charged event
            if ($event === 'subscription.charged') {
                $plan = $subscription->plan;
                $user = $subscription->user;
                if ($plan && $user) {
                    $user->update(['build_credits' => $plan->monthly_build_credits]);
                }

                // Create transaction
                $paymentId = $payload['payload']['payment']['entity']['id'] ?? null;
                if ($paymentId && ! Transaction::where('external_transaction_id', $paymentId)->exists()) {
                    $paymentData = $payload['payload']['payment']['entity'] ?? [];
                    $currency = $paymentData['currency'] ?? 'INR';
                    $amount = $paymentData['amount'] ?? 0;

                    $transaction = Transaction::create([
                        'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                        'user_id' => $subscription->user_id,
                        'subscription_id' => $subscription->id,
                        'external_transaction_id' => $paymentId,
                        'amount' => $this->reverseAmount($amount, $currency),
                        'currency' => strtoupper($currency),
                        'status' => 'completed',
                        'type' => 'subscription',
                        'payment_method' => 'Razorpay',
                        'transaction_date' => now(),
                    ]);

                    // Send user notification
                    $user = $subscription->user;
                    if ($user) {
                        try {
                            $user->notify(new PaymentCompletedNotification($transaction));
                        } catch (\Exception $e) {
                            Log::error('Failed to send PaymentCompletedNotification', [
                                'user_id' => $user->id,
                                'transaction_id' => $transaction->id,
                                'error' => $e->getMessage(),
                            ]);
                        }

                        // Send admin notification
                        AdminPaymentNotification::sendIfEnabled(
                            'payment_completed',
                            $user,
                            $subscription,
                            $transaction
                        );
                    }
                }
            }
        }
    }

    private function handleRefundProcessed(array $payload): void
    {
        $refundData = $payload['payload']['refund']['entity'] ?? [];
        $paymentId = $refundData['payment_id'] ?? null;

        if (! $paymentId) {
            return;
        }

        // Find transaction by payment ID
        $transaction = Transaction::where('external_transaction_id', $paymentId)->first();

        if ($transaction) {
            $refundedAmount = $this->reverseAmount(
                $refundData['amount'] ?? 0,
                $refundData['currency'] ?? $transaction->currency
            );
            $totalAmount = $transaction->amount;

            $status = $refundedAmount >= $totalAmount ? 'refunded' : 'partially_refunded';

            $transaction->update(['status' => $status]);
        }
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        try {
            $webhookSignature = $request->header('X-Razorpay-Signature');
            $webhookBody = $request->getContent();

            $this->api->utility->verifyWebhookSignature(
                $webhookBody,
                $webhookSignature,
                $this->config['webhook_secret']
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
