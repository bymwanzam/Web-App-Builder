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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CryptoComPlugin implements PaymentGatewayPlugin
{
    private array $config;

    private string $baseUrl = 'https://pay.crypto.com/api';

    private const ZERO_DECIMAL_CURRENCIES = ['CLP', 'JPY', 'KRW'];

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'Crypto.com Pay';
    }

    public function getDescription(): string
    {
        return 'Accept cryptocurrency and fiat payments via Crypto.com Pay';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/cryptocom/icon.jpg';
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

        // Test API connection
        try {
            Http::withBasicAuth($config['secret_key'], '')
                ->get("{$this->baseUrl}/products")
                ->throw();
        } catch (\Exception $e) {
            throw new \Exception('Invalid API credentials: '.$e->getMessage());
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        // Validate interval - Crypto.com only supports monthly
        // Note: Appy plans are monthly by default (from CLAUDE.md)
        // This validation is for completeness
        if (isset($plan->interval_unit) && $plan->interval_unit !== 'month') {
            throw new \Exception('Crypto.com Pay only supports monthly billing intervals');
        }

        // Calculate amount
        $amount = $this->convertAmount($plan->price, $plan->currency);

        // Get or create product with pricing plan (uses caching to avoid duplicates)
        $product = $this->getOrCreateProduct($plan);

        // Create customer
        $customer = $this->createCustomer($user);

        // Create subscription
        $response = Http::withBasicAuth($this->config['secret_key'], '')
            ->asJson()
            ->post("{$this->baseUrl}/subscriptions", [
                'customer_id' => $customer['id'],
                'return_url' => route('payment-gateways.callback', ['plugin' => 'cryptocom']),
                'billing_cycle_anchor' => now()->timestamp,
                'metadata' => [
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                ],
                'items' => [
                    [
                        'plan_id' => $product['pricing_plans'][0]['id'],
                        'quantity' => 1,
                    ],
                ],
            ])
            ->throw();

        $subscription = $response->json();

        Log::info('Crypto.com subscription created', [
            'subscription_id' => $subscription['id'],
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Redirect to Crypto.com
        return $subscription['subscription_url'];
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug('Crypto.com Webhook Received', ['payload' => $request->all()]);

        // Verify webhook signature
        if (! empty($this->config['webhook_secret'])) {
            if (! $this->verifyWebhookSignature($request)) {
                Log::error('Crypto.com Webhook Signature Verification Failed');

                return response('Invalid signature', 401);
            }
        }

        try {
            $payload = $request->json()->all();
            $eventType = $payload['type'] ?? '';

            if (empty($eventType)) {
                return response('No event type', 400);
            }

            // Handle subscription events
            if (str_starts_with($eventType, 'subscription.')) {
                $this->handleSubscriptionEvent($eventType, $payload);
            }

            // Handle invoice events
            if (str_starts_with($eventType, 'invoice.')) {
                $this->handleInvoiceEvent($eventType, $payload);
            }

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('Crypto.com webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Crypto.com from retrying
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
        Http::withBasicAuth($this->config['secret_key'], '')
            ->asJson()
            ->post("{$this->baseUrl}/subscriptions/{$subscription->subscription_id}/cancel")
            ->throw();
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        $response = Http::withBasicAuth($this->config['secret_key'], '')
            ->get("{$this->baseUrl}/subscriptions/{$subscriptionId}")
            ->throw();

        return $response->json();
    }

    // Private Helper Methods
    private function convertAmount(float $amount, string $currency): int
    {
        if (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (int) round($amount, 0);
        }

        return (int) round($amount * 100, 0);
    }

    private function reverseAmount(int $amount, string $currency): float
    {
        if (in_array($currency, self::ZERO_DECIMAL_CURRENCIES)) {
            return (float) $amount;
        }

        return (float) $amount / 100;
    }

    private function getOrCreateProduct(Plan $plan): array
    {
        $cacheKey = "cryptocom_product_{$plan->id}_{$plan->currency}";

        $cachedProductId = Cache::get($cacheKey);
        if ($cachedProductId) {
            try {
                $response = Http::withBasicAuth($this->config['secret_key'], '')
                    ->get("{$this->baseUrl}/products/{$cachedProductId}");
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                // Product doesn't exist, create new one
            }
        }

        $newProduct = $this->createProduct($plan);

        // Cache for 30 days
        Cache::put($cacheKey, $newProduct['id'], 60 * 60 * 24 * 30);

        return $newProduct;
    }

    private function createProduct(Plan $plan): array
    {
        $amount = $this->convertAmount($plan->price, $plan->currency);
        $intervalCount = $plan->interval_count ?? 1;

        $response = Http::withBasicAuth($this->config['secret_key'], '')
            ->asJson()
            ->post("{$this->baseUrl}/products", [
                'name' => config('app.name'),
                'active' => true,
                'description' => config('app.name').' subscription',
                'pricing_plans' => [
                    [
                        'amount' => $amount,
                        'currency' => $plan->currency,
                        'active' => true,
                        'description' => $plan->name,
                        'interval' => 'month',
                        'interval_count' => $intervalCount,
                        'purchase_type' => 'recurring',
                    ],
                ],
            ])
            ->throw();

        return $response->json();
    }

    private function createCustomer(User $user): array
    {
        $response = Http::withBasicAuth($this->config['secret_key'], '')
            ->asJson()
            ->post("{$this->baseUrl}/customers", [
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->throw();

        return $response->json();
    }

    private function handleSubscriptionEvent(string $eventType, array $payload): void
    {
        $subscriptionId = $payload['data']['id'] ?? null;

        if ($eventType === 'subscription.activated') {
            $metadata = $payload['data']['metadata'] ?? [];

            if (empty($metadata['user_id']) || empty($metadata['plan_id'])) {
                return;
            }

            // Check if subscription already exists
            if (Subscription::where('subscription_id', $subscriptionId)->exists()) {
                Log::info("Subscription already exists: {$subscriptionId}");

                return;
            }

            $plan = Plan::find($metadata['plan_id']);
            if (! $plan) {
                return;
            }

            $subscription = Subscription::create([
                'user_id' => $metadata['user_id'],
                'plan_id' => $metadata['plan_id'],
                'payment_method' => 'Crypto.com',
                'subscription_id' => $subscriptionId,
                'status' => 'active',
                'amount' => $plan->price,
                'renewal_at' => isset($payload['data']['current_period_end'])
                    ? \Carbon\Carbon::createFromTimestamp($payload['data']['current_period_end'])
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
        }

        if ($eventType === 'subscription.cancelled') {
            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
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
    }

    private function handleInvoiceEvent(string $eventType, array $payload): void
    {
        if ($eventType === 'invoice.paid') {
            $subscriptionId = $payload['data']['subscription_id'] ?? null;
            $invoiceId = $payload['data']['id'] ?? null;

            if (! $subscriptionId || ! $invoiceId) {
                return;
            }

            // Check if transaction already exists
            if (Transaction::where('external_transaction_id', $invoiceId)->exists()) {
                return;
            }

            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if (! $subscription) {
                return;
            }

            // Create transaction record
            $currency = $payload['data']['currency'] ?? 'USD';
            $amount = $payload['data']['amount_total'] ?? 0;

            $transaction = Transaction::create([
                'transaction_id' => 'TXN-'.strtoupper(uniqid()),
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'external_transaction_id' => $invoiceId,
                'amount' => $this->reverseAmount($amount, $currency),
                'currency' => strtoupper($currency),
                'status' => 'completed',
                'type' => 'subscription',
                'payment_method' => 'Crypto.com',
                'transaction_date' => now(),
            ]);

            // Refill build credits
            $plan = $subscription->plan;
            $user = $subscription->user;
            if ($plan && $user) {
                $user->update(['build_credits' => $plan->monthly_build_credits]);

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

            // Fetch and update renewal date
            try {
                $cryptoComSub = $this->getSubscriptionStatus($subscriptionId);
                if (isset($cryptoComSub['current_period_end'])) {
                    $subscription->update([
                        'renewal_at' => \Carbon\Carbon::createFromTimestamp($cryptoComSub['current_period_end']),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch subscription renewal date: '.$e->getMessage());
            }
        }

        if ($eventType === 'invoice.payment_failed') {
            $subscriptionId = $payload['data']['subscription_id'] ?? null;

            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->update(['status' => 'cancelled']); // Suspended

                // Send user notification
                $user = $subscription->user;
                if ($user) {
                    try {
                        $user->notify(new PaymentFailedNotification($subscription));
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
                        $subscription
                    );
                }
            }
        }
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('Pay-Signature');

        if (! $signature) {
            return false;
        }

        // Signature format: "t=timestamp,v1=signature"
        $parts = explode(',', $signature);
        if (count($parts) < 2) {
            return false;
        }

        $timestamp = Str::after($parts[0], 't=');
        $receivedSignature = Str::after($parts[1], 'v1=');

        // Compute signature
        $payload = $timestamp.'.'.$request->getContent();
        $computedSignature = hash_hmac('sha256', $payload, $this->config['webhook_secret']);

        return hash_equals($computedSignature, $receivedSignature);
    }
}
