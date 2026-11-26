<?php

namespace App\Plugins\PaymentGateways;

use App\Contracts\PaymentGatewayPlugin;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AdminPaymentNotification;
use App\Notifications\PaymentCompletedNotification;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\SubscriptionCancelledNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripePlugin implements PaymentGatewayPlugin
{
    private array $config;

    private StripeClient $stripe;

    private const ZERO_DECIMAL_CURRENCIES = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

    private const SPECIAL_CURRENCIES = ['UGX', 'ISK'];

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];

        if (! empty($this->config['secret_key'])) {
            Stripe::setApiKey($this->config['secret_key']);
            $this->stripe = new StripeClient($this->config['secret_key']);
        }
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'Stripe';
    }

    public function getDescription(): string
    {
        return 'Accept payments globally with Stripe - The world\'s most popular payment platform';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/stripe/icon.jpg';
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
        return ! empty($this->config['secret_key']);
    }

    public function validateConfig(array $config): void
    {
        if (empty($config['secret_key'])) {
            throw new \Exception('Secret Key is required');
        }

        if (empty($config['webhook_secret'])) {
            throw new \Exception('Webhook Secret is required for security');
        }

        // Test API connection
        try {
            $client = new StripeClient($config['secret_key']);
            $client->balance->retrieve();
        } catch (ApiErrorException $e) {
            throw new \Exception('Invalid API credentials: '.$e->getMessage());
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        try {
            // Convert amount based on currency decimals
            $amount = $this->convertAmount($plan->price, $plan->currency);

            // Get or create Stripe customer
            $customerId = $this->getOrCreateCustomer($user);

            // Create Checkout Session
            $session = $this->stripe->checkout->sessions->create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($plan->currency),
                        'product_data' => [
                            'name' => $plan->name,
                            'description' => 'Subscription to '.$plan->name,
                        ],
                        'unit_amount' => $amount,
                        'recurring' => [
                            'interval' => $plan->interval_unit,
                            'interval_count' => $plan->interval_count,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('payment-gateways.callback', ['plugin' => 'stripe', 'session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url' => route('user.billing'),
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                    ],
                ],
            ]);

            Log::info('Stripe Checkout Session created', [
                'session_id' => $session->id,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
            ]);

            return $session->url;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment initialization failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw new \Exception('Payment initialization failed: '.$e->getMessage());
        }
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug('Stripe Webhook Received', ['payload' => $request->all()]);

        // Verify webhook signature
        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $this->config['webhook_secret']
            );
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Signature Verification Failed', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        try {
            // Handle different event types
            switch ($event->type) {
                case 'customer.subscription.created':
                    $this->handleSubscriptionCreated($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;

                case 'invoice.paid':
                    $this->handleInvoicePaid($event->data->object);
                    break;

                case 'invoice.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event->data->object);
                    break;
            }

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Stripe from retrying
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
            $this->stripe->subscriptions->cancel($subscription->subscription_id);
        } catch (ApiErrorException $e) {
            Log::error('Stripe subscription cancellation failed', [
                'subscription_id' => $subscription->subscription_id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Cancellation failed: '.$e->getMessage());
        }
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        try {
            $stripeSubscription = $this->stripe->subscriptions->retrieve($subscriptionId);

            return [
                'status' => $stripeSubscription->status,
                'current_period_end' => $stripeSubscription->current_period_end,
                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
            ];
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to retrieve subscription: '.$e->getMessage());
        }
    }

    // Private Helper Methods
    private function convertAmount(float $amount, string $currency): int
    {
        $currency = strtoupper($currency);

        if (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (int) round($amount, 0);
        } elseif (in_array($currency, self::SPECIAL_CURRENCIES)) {
            return (int) round($amount) * 100;
        } else {
            return (int) round($amount * 100, 0);
        }
    }

    private function reverseAmount(int $amount, string $currency): float
    {
        $currency = strtoupper($currency);

        if (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (float) $amount;
        } elseif (in_array($currency, self::SPECIAL_CURRENCIES)) {
            return (float) $amount / 100;
        } else {
            return (float) $amount / 100;
        }
    }

    private function getOrCreateCustomer(User $user): string
    {
        // Check if customer already exists
        $metadata = $user->metadata ?? [];
        $stripeCustomerId = $metadata['stripe_customer_id'] ?? null;

        if ($stripeCustomerId) {
            try {
                $this->stripe->customers->retrieve($stripeCustomerId);

                return $stripeCustomerId;
            } catch (ApiErrorException $e) {
                // Customer doesn't exist, create new
            }
        }

        // Create new customer
        try {
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            // Store customer ID in user metadata
            $user->update([
                'metadata' => array_merge($metadata, [
                    'stripe_customer_id' => $customer->id,
                ]),
            ]);

            return $customer->id;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create customer: '.$e->getMessage());
        }
    }

    private function handleSubscriptionCreated($stripeSubscription): void
    {
        $metadata = $stripeSubscription->metadata ?? [];

        if (empty($metadata->user_id) || empty($metadata->plan_id)) {
            return;
        }

        // Check if subscription already exists
        if (Subscription::where('subscription_id', $stripeSubscription->id)->exists()) {
            Log::info("Subscription already exists: {$stripeSubscription->id}");

            return;
        }

        $plan = Plan::find($metadata->plan_id);
        if (! $plan) {
            return;
        }

        Subscription::create([
            'user_id' => $metadata->user_id,
            'plan_id' => $metadata->plan_id,
            'payment_method' => 'Stripe',
            'subscription_id' => $stripeSubscription->id,
            'status' => $stripeSubscription->status === 'active' ? 'active' : 'pending',
            'amount' => $plan->price,
            'renewal_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ]);
    }

    private function handleSubscriptionUpdated($stripeSubscription): void
    {
        $subscription = Subscription::where('subscription_id', $stripeSubscription->id)->first();

        if (! $subscription) {
            return;
        }

        // Map Stripe status to our status
        $status = match ($stripeSubscription->status) {
            'active' => 'active',
            'unpaid', 'past_due' => 'cancelled', // Suspended
            'canceled' => 'cancelled',
            default => 'pending'
        };

        $subscription->update([
            'status' => $status,
            'renewal_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ]);

        // If activated, update user plan
        if ($status === 'active') {
            $user = $subscription->user;
            $plan = $subscription->plan;

            if ($user && $plan) {
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
        }
    }

    private function handleSubscriptionDeleted($stripeSubscription): void
    {
        $subscription = Subscription::where('subscription_id', $stripeSubscription->id)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now(),
            ]);

            // Send user notification
            $user = $subscription->user;
            if ($user) {
                try {
                    $user->notify(new SubscriptionCancelledNotification($subscription));
                } catch (\Exception $e) {
                    Log::error('Failed to send SubscriptionCancelledNotification', [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send admin notification
                AdminPaymentNotification::sendIfEnabled(
                    'subscription_cancelled',
                    $user,
                    $subscription
                );
            }
        }
    }

    private function handleInvoicePaid($invoice): void
    {
        if (empty($invoice->subscription)) {
            return;
        }

        // Check if transaction already exists
        if (Transaction::where('external_transaction_id', $invoice->id)->exists()) {
            return;
        }

        $subscription = Subscription::where('subscription_id', $invoice->subscription)->first();

        if (! $subscription) {
            return;
        }

        // Create transaction record
        $transaction = Transaction::create([
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'external_transaction_id' => $invoice->id,
            'amount' => $this->reverseAmount($invoice->amount_paid, $invoice->currency),
            'currency' => strtoupper($invoice->currency),
            'status' => 'completed',
            'type' => 'subscription',
            'payment_method' => 'Stripe',
            'transaction_date' => now(),
        ]);

        // Refill build credits and update plan
        $plan = $subscription->plan;
        $user = $subscription->user;
        if ($plan && $user) {
            $user->update([
                'plan_id' => $subscription->plan_id,
                'build_credits' => $plan->monthly_build_credits,
            ]);

            // Send user notification
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

    private function handlePaymentFailed($invoice): void
    {
        if (empty($invoice->subscription)) {
            return;
        }

        $subscription = Subscription::where('subscription_id', $invoice->subscription)->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelled']); // Suspended

            // Send user notification
            $user = $subscription->user;
            if ($user) {
                $amount = $this->reverseAmount($invoice->amount_due, $invoice->currency);
                try {
                    $user->notify(new PaymentFailedNotification($subscription, $amount, strtoupper($invoice->currency)));
                } catch (\Exception $e) {
                    Log::error('Failed to send PaymentFailedNotification', [
                        'user_id' => $user->id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Send admin notification
                AdminPaymentNotification::sendIfEnabled(
                    'payment_failed',
                    $user,
                    $subscription,
                    null,
                    ['amount' => $amount, 'currency' => strtoupper($invoice->currency)]
                );
            }
        }
    }

    private function handleChargeRefunded($charge): void
    {
        // Find transaction by charge ID
        $transaction = Transaction::where('external_transaction_id', 'LIKE', '%'.$charge->id.'%')->first();

        if ($transaction) {
            $refundedAmount = $this->reverseAmount($charge->amount_refunded, $charge->currency);
            $totalAmount = $this->reverseAmount($charge->amount, $charge->currency);

            $status = $refundedAmount >= $totalAmount ? 'refunded' : 'partially_refunded';

            $transaction->update(['status' => $status]);
        }
    }
}
