<?php

namespace App\Plugins\PaymentGateways;

use App\Contracts\PaymentGatewayPlugin;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\AdminPaymentNotification;
use App\Notifications\PaymentCompletedNotification;
use App\Notifications\SubscriptionActivatedNotification;
use App\Notifications\SubscriptionCancelledNotification;
use App\Notifications\SubscriptionExpiredNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayPalPlugin implements PaymentGatewayPlugin
{
    private array $config;

    private string $accessToken;

    private string $url;

    private string $productId;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];

        $this->url = ($this->config['sandbox'] ?? true)
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // PayPal requires minimum 6 characters for product ID
        $slug = Str::slug(config('app.name'));
        $this->productId = strlen($slug) >= 6 ? $slug : $slug.'-product';
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'PayPal';
    }

    public function getDescription(): string
    {
        return 'Accept recurring payments via PayPal subscriptions';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/paypal/icon.jpg';
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
        return ! empty($this->config['client_id'])
            && ! empty($this->config['client_secret']);
    }

    public function validateConfig(array $config): void
    {
        if (empty($config['client_id'])) {
            throw new \Exception('Client ID is required');
        }

        if (empty($config['client_secret'])) {
            throw new \Exception('Client Secret is required');
        }

        if (empty($config['webhook_id'])) {
            throw new \Exception('Webhook ID is required for security');
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        $this->authenticate();

        // Ensure product exists
        $productId = $this->getOrCreateProduct();

        // Create PayPal plan
        $paypalPlan = $this->createPlan($plan, $productId);

        // Store metadata in cache
        $uniqueId = Str::uuid()->toString();
        $clientIdHash = md5($this->config['client_id']);
        Cache::put("paypal_subscription_{$clientIdHash}_{$uniqueId}", [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ], 24 * 60 * 60); // 24 hours

        // Create subscription
        $response = Http::withToken($this->accessToken)
            ->asJson()
            ->post("{$this->url}/v1/billing/subscriptions", [
                'plan_id' => $paypalPlan['id'],
                'custom_id' => $uniqueId,
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'return_url' => route('payment-gateways.callback', ['plugin' => 'paypal']),
                    'cancel_url' => route('payment-gateways.callback', ['plugin' => 'paypal', 'cancelled' => 1]),
                ],
            ])
            ->throw();

        $body = $response->json();

        // Return PayPal approval URL
        foreach ($body['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return $link['href'];
            }
        }

        throw new \Exception('PayPal approval URL not found');
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug('PayPal Webhook Received', ['payload' => $request->all()]);

        // Verify signature if webhook_id is configured (production only)
        if (! empty($this->config['webhook_id']) && ! ($this->config['sandbox'] ?? true)) {
            if (! $this->verifyWebhookSignature($request)) {
                Log::error('PayPal Webhook Signature Verification Failed');

                return response('Invalid signature', 401);
            }
        } else {
            Log::debug('PayPal Webhook Signature Verification Skipped (sandbox mode)');
        }

        try {
            $payload = $request->json()->all();
            $eventType = $payload['event_type'] ?? '';

            if (empty($eventType)) {
                Log::warning('PayPal webhook received without event_type');

                return response('No event type', 400);
            }

            // Handle subscription lifecycle events
            if (str_starts_with($eventType, 'BILLING.SUBSCRIPTION.')) {
                $this->handleSubscriptionEvent($eventType, $payload);
            }

            // Handle payment events
            if ($eventType === 'PAYMENT.SALE.COMPLETED') {
                $this->handlePaymentCompleted($payload);
            }

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('PayPal webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent PayPal from retrying
            return response('Webhook received but processing failed', 200);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        // Simply redirect back to billing page
        // Webhook will handle the actual subscription activation
        return redirect()->route('user.billing');
    }

    public function cancelSubscription(Subscription $subscription): void
    {
        $this->authenticate();

        Http::withToken($this->accessToken)
            ->asJson()
            ->post("{$this->url}/v1/billing/subscriptions/{$subscription->subscription_id}/cancel", [
                'reason' => 'User requested cancellation',
            ])
            ->throw();
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        $this->authenticate();

        $response = Http::withToken($this->accessToken)
            ->get("{$this->url}/v1/billing/subscriptions/{$subscriptionId}")
            ->throw();

        return $response->json();
    }

    // Private Helper Methods
    private function authenticate(): void
    {
        $cacheKey = 'paypal_access_token_'.md5($this->config['client_id']);

        $this->accessToken = Cache::remember($cacheKey, 50 * 60, function () {
            $response = Http::withBasicAuth($this->config['client_id'], $this->config['client_secret'])
                ->asForm()
                ->post("{$this->url}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ])
                ->throw();

            return $response->json()['access_token'];
        });
    }

    private function getOrCreateProduct(): string
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->url}/v1/catalogs/products/{$this->productId}");

        if ($response->successful()) {
            return $this->productId;
        }

        // Create product
        $payload = [
            'id' => $this->productId,
            'name' => config('app.name'),
            'description' => 'Subscription to '.config('app.name'),
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ];

        Log::debug('PayPal Create Product Request', ['payload' => $payload]);

        $response = Http::withToken($this->accessToken)
            ->asJson()
            ->post("{$this->url}/v1/catalogs/products", $payload);

        if (! $response->successful()) {
            Log::error('PayPal Product Creation Failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'product_id' => $this->productId,
            ]);
            $response->throw();
        }

        return $response->json()['id'];
    }

    private function createPlan(Plan $plan, string $productId): array
    {
        $payload = [
            'product_id' => $productId,
            'name' => $plan->name,
            'description' => "Subscription to {$plan->name}",
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => 'MONTH',
                        'interval_count' => 1,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0, // Infinite
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => round($plan->price, 2),
                            'currency_code' => $plan->currency,
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'payment_failure_threshold' => 0,
                'setup_fee' => [
                    'value' => 0,
                    'currency_code' => $plan->currency,
                ],
            ],
        ];

        Log::debug('PayPal Create Plan Request', ['payload' => $payload]);

        $response = Http::withToken($this->accessToken)
            ->asJson()
            ->post("{$this->url}/v1/billing/plans", $payload)
            ->throw();

        return $response->json();
    }

    private function handleSubscriptionEvent(string $eventType, array $payload): void
    {
        $subscriptionId = $payload['resource']['id'] ?? null;
        $status = $payload['resource']['status'] ?? null;

        if ($eventType === 'BILLING.SUBSCRIPTION.CREATED') {
            $customId = $payload['resource']['custom_id'] ?? null;
            if (! $customId) {
                return;
            }

            // Check if subscription already exists
            if (Subscription::where('subscription_id', $subscriptionId)->exists()) {
                Log::info("Subscription already exists: {$subscriptionId}");

                return;
            }

            $clientIdHash = md5($this->config['client_id']);
            $metadata = Cache::get("paypal_subscription_{$clientIdHash}_{$customId}");

            if (! $metadata) {
                Log::error("PayPal subscription metadata not found for custom_id: {$customId}");

                return;
            }

            // Get plan to retrieve amount
            $plan = Plan::find($metadata['plan_id']);
            if (! $plan) {
                Log::error("Plan not found: {$metadata['plan_id']}");

                return;
            }

            $nextBillingTime = $payload['resource']['billing_info']['next_billing_time'] ?? null;

            Subscription::create([
                'user_id' => $metadata['user_id'],
                'plan_id' => $metadata['plan_id'],
                'payment_method' => 'PayPal',
                'subscription_id' => $subscriptionId,
                'status' => 'pending',
                'amount' => $plan->price,
                'renewal_at' => $nextBillingTime,
            ]);
        }

        if ($eventType === 'BILLING.SUBSCRIPTION.ACTIVATED') {
            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->update(['status' => 'active']);

                // Update user plan and refill build credits
                $user = $subscription->user;
                $plan = $subscription->plan;
                if ($user && $plan) {
                    // Cancel all other active/pending subscriptions for this user
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

        if ($eventType === 'BILLING.SUBSCRIPTION.CANCELLED') {
            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->update([
                    'status' => 'cancelled',
                    'ends_at' => $subscription->renewal_at ?? now(),
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

        if ($eventType === 'BILLING.SUBSCRIPTION.EXPIRED') {
            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->update([
                    'status' => 'expired',
                    'ends_at' => now(),
                ]);

                // Send user notification
                $user = $subscription->user;
                if ($user) {
                    try {
                        $user->notify(new SubscriptionExpiredNotification($subscription));
                    } catch (\Exception $e) {
                        Log::error('Failed to send SubscriptionExpiredNotification', [
                            'user_id' => $user->id,
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Send admin notification
                    AdminPaymentNotification::sendIfEnabled(
                        'subscription_expired',
                        $user,
                        $subscription
                    );
                }
            }
        }

        if ($eventType === 'BILLING.SUBSCRIPTION.SUSPENDED') {
            $subscription = Subscription::where('subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->update([
                    'status' => 'cancelled',
                    'ends_at' => now(),
                ]);
                Log::info("Subscription suspended: {$subscriptionId}");
            }
        }
    }

    private function handlePaymentCompleted(array $payload): void
    {
        $subscriptionId = $payload['resource']['billing_agreement_id'] ?? null;
        $transactionId = $payload['resource']['id'] ?? null;

        if (! $subscriptionId || ! $transactionId) {
            return;
        }

        // Check if transaction already recorded
        $existingTransaction = \App\Models\Transaction::where('external_transaction_id', $transactionId)->first();
        if ($existingTransaction) {
            return;
        }

        $subscription = Subscription::where('subscription_id', $subscriptionId)->first();

        if (! $subscription) {
            Log::error("Subscription not found for PayPal billing agreement: {$subscriptionId}");

            return;
        }

        // Create transaction record
        $transaction = \App\Models\Transaction::create([
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'external_transaction_id' => $transactionId,
            'amount' => $payload['resource']['amount']['total'] ?? 0,
            'currency' => $payload['resource']['amount']['currency'] ?? 'USD',
            'status' => 'completed',
            'type' => 'subscription',
            'payment_method' => 'PayPal',
            'transaction_date' => now(),
        ]);

        // Refill build credits on successful payment
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

        // Update renewal date
        try {
            $paypalSub = $this->getSubscriptionStatus($subscriptionId);
            $subscription->update([
                'renewal_at' => $paypalSub['billing_info']['next_billing_time'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update renewal date: '.$e->getMessage());
        }
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        $this->authenticate();

        $response = Http::withToken($this->accessToken)
            ->asJson()
            ->post("{$this->url}/v1/notifications/verify-webhook-signature", [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'webhook_id' => $this->config['webhook_id'],
                'webhook_event' => $request->json()->all(),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
            ]);

        $body = $response->json();

        return $response->successful() && ($body['verification_status'] ?? '') === 'SUCCESS';
    }
}
