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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackPlugin implements PaymentGatewayPlugin
{
    private array $config;

    private string $baseUrl = 'https://api.paystack.co';

    private const SUPPORTED_CURRENCIES = ['NGN', 'USD', 'GHS', 'ZAR', 'KES'];

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];
    }

    // Base Plugin Interface Methods
    public function getName(): string
    {
        return 'Paystack';
    }

    public function getDescription(): string
    {
        return 'Accept payments via Paystack - The modern payment gateway for Africa';
    }

    public function getType(): string
    {
        return 'payment_gateway';
    }

    public function getIcon(): string
    {
        return 'plugins/paystack/icon.jpg';
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
            Http::withToken($config['secret_key'])
                ->get("{$this->baseUrl}/balance")
                ->throw();
        } catch (\Exception $e) {
            throw new \Exception('Invalid API credentials: '.$e->getMessage());
        }
    }

    // Payment Gateway Interface Methods
    public function initPayment(Plan $plan, User $user): RedirectResponse|string
    {
        // Validate currency
        if (! in_array($plan->currency, self::SUPPORTED_CURRENCIES)) {
            throw new \Exception('Unsupported currency. Paystack supports: '.implode(', ', self::SUPPORTED_CURRENCIES));
        }

        // Calculate amount (amount in kobo for Paystack - multiply by 100)
        $amount = (int) round($plan->price * 100, 0);

        // Get or create Paystack plan (uses caching to avoid duplicates)
        $paystackPlan = $this->getOrCreatePlan($plan, $user);

        // Initialize transaction
        $response = Http::withToken($this->config['secret_key'])
            ->asJson()
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $user->email,
                'amount' => $amount,
                'currency' => $plan->currency,
                'plan' => $paystackPlan['plan_code'],
                'callback_url' => route('payment-gateways.callback', ['plugin' => 'paystack']),
                'metadata' => [
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                ],
            ])
            ->throw();

        $data = $response->json('data');

        Log::info('Paystack payment initialized', [
            'reference' => $data['reference'],
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]);

        // Redirect to Paystack
        return $data['authorization_url'];
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug('Paystack Webhook Received', ['payload' => $request->all()]);

        // Verify webhook signature
        if (! empty($this->config['webhook_secret'])) {
            if (! $this->verifyWebhookSignature($request)) {
                Log::error('Paystack Webhook Signature Verification Failed');

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

            // Handle payment events
            if ($event === 'charge.success') {
                $this->handleChargeSuccess($payload);
            }

            return response('Webhook handled', 200);
        } catch (\Exception $e) {
            Log::error('Paystack webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to prevent Paystack from retrying
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
        // Get subscription details including email_token
        $response = Http::withToken($this->config['secret_key'])
            ->get("{$this->baseUrl}/subscription/{$subscription->subscription_id}")
            ->throw();

        $paystackSubscription = $response->json('data');

        // Disable subscription
        Http::withToken($this->config['secret_key'])
            ->asJson()
            ->post("{$this->baseUrl}/subscription/disable", [
                'code' => $subscription->subscription_id,
                'token' => $paystackSubscription['email_token'],
            ])
            ->throw();
    }

    public function getSubscriptionStatus(string $subscriptionId): array
    {
        $response = Http::withToken($this->config['secret_key'])
            ->get("{$this->baseUrl}/subscription/{$subscriptionId}")
            ->throw();

        return $response->json('data');
    }

    // Private Helper Methods
    private function getOrCreatePlan(Plan $plan, User $user): array
    {
        $cacheKey = "paystack_plan_{$plan->id}_{$plan->currency}";

        // Check cache first
        $cachedPlanCode = Cache::get($cacheKey);
        if ($cachedPlanCode) {
            // Verify plan still exists in Paystack
            try {
                $response = Http::withToken($this->config['secret_key'])
                    ->get("{$this->baseUrl}/plan/{$cachedPlanCode}");
                if ($response->successful()) {
                    return ['plan_code' => $cachedPlanCode];
                }
            } catch (\Exception $e) {
                // Plan doesn't exist, create new one
            }
        }

        // Create new plan
        $newPlan = $this->createPlan($plan, $user);

        // Cache for 30 days
        Cache::put($cacheKey, $newPlan['plan_code'], 60 * 60 * 24 * 30);

        return $newPlan;
    }

    private function createPlan(Plan $plan, User $user): array
    {
        $amount = (int) round($plan->price * 100, 0);
        $interval = $this->getPaystackInterval($plan);

        $response = Http::withToken($this->config['secret_key'])
            ->asJson()
            ->post("{$this->baseUrl}/plan", [
                'name' => $plan->name,
                'description' => json_encode([
                    'plan_id' => $plan->id,
                    'user_id' => $user->id,
                ]),
                'amount' => $amount,
                'interval' => $interval,
                'currency' => $plan->currency,
            ])
            ->throw();

        return $response->json('data');
    }

    private function getPaystackInterval(Plan $plan): string
    {
        // Note: Appy plans are monthly by default (from CLAUDE.md)
        // This is simplified for MVP - adjust based on your plan structure

        // For monthly intervals
        if ($plan->interval_unit === 'month') {
            return match ($plan->interval_count ?? 1) {
                1 => 'monthly',
                3 => 'quarterly',
                6 => 'biannually',
                default => throw new \Exception('Unsupported monthly interval. Paystack supports 1, 3, or 6 months.')
            };
        }

        // For other intervals, only interval 1 is supported
        if (($plan->interval_count ?? 1) !== 1) {
            throw new \Exception('Paystack only supports interval 1 for daily, weekly, and yearly plans');
        }

        return match ($plan->interval_unit) {
            'day' => 'daily',
            'week' => 'weekly',
            'year' => 'annually',
            default => throw new \Exception('Unsupported interval unit: '.$plan->interval_unit)
        };
    }

    private function handleSubscriptionEvent(string $event, array $payload): void
    {
        $subscriptionCode = $payload['data']['subscription_code'] ?? null;

        if ($event === 'subscription.create') {
            // Parse metadata from plan description
            $metadata = json_decode($payload['data']['plan']['description'] ?? '{}', true);

            if (empty($metadata['user_id']) || empty($metadata['plan_id'])) {
                Log::error('Paystack subscription metadata missing', ['payload' => $payload]);

                return;
            }

            // Check if subscription already exists
            if (Subscription::where('subscription_id', $subscriptionCode)->exists()) {
                Log::info("Subscription already exists: {$subscriptionCode}");

                return;
            }

            $plan = Plan::find($metadata['plan_id']);
            if (! $plan) {
                Log::error("Plan not found: {$metadata['plan_id']}");

                return;
            }

            // Calculate renewal date from plan intervals
            $renewalAt = $this->calculateRenewalDate($plan);

            Subscription::create([
                'user_id' => $metadata['user_id'],
                'plan_id' => $metadata['plan_id'],
                'payment_method' => 'Paystack',
                'subscription_id' => $subscriptionCode,
                'status' => 'pending',
                'amount' => $plan->price,
                'renewal_at' => $renewalAt,
            ]);
        }

        if (str_starts_with($event, 'subscription.')) {
            $subscription = Subscription::where('subscription_id', $subscriptionCode)->first();

            if (! $subscription) {
                return;
            }

            // Map Paystack status to our status
            $paystackStatus = $payload['data']['status'] ?? '';
            $status = match ($paystackStatus) {
                'active' => 'active',
                'attention' => 'cancelled', // Suspended
                'complete' => 'expired',
                'cancelled', 'non-renewing' => 'cancelled',
                default => 'pending'
            };

            // Get renewal date from webhook payload or calculate from plan
            $renewalAt = null;
            if (isset($payload['data']['next_payment_date'])) {
                $renewalAt = \Carbon\Carbon::parse($payload['data']['next_payment_date']);
            } elseif ($subscription->plan) {
                $renewalAt = $this->calculateRenewalDate($subscription->plan);
            }

            $subscription->update([
                'status' => $status,
                'renewal_at' => $renewalAt,
            ]);

            // If activated, update user plan and refill credits
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
    }

    private function handleChargeSuccess(array $payload): void
    {
        $reference = $payload['data']['reference'] ?? null;
        $amount = ($payload['data']['amount'] ?? 0) / 100; // Convert from kobo

        if (! $reference) {
            return;
        }

        // Check if transaction already exists
        if (Transaction::where('external_transaction_id', $reference)->exists()) {
            return;
        }

        $metadata = $payload['data']['metadata'] ?? [];
        $userId = $metadata['user_id'] ?? null;

        if (! $userId) {
            return;
        }

        // Find subscription
        $subscription = Subscription::where('user_id', $userId)
            ->where('payment_method', 'Paystack')
            ->where('status', 'active')
            ->first();

        if (! $subscription) {
            return;
        }

        // Create transaction record
        $transaction = Transaction::create([
            'transaction_id' => 'TXN-'.strtoupper(uniqid()),
            'user_id' => $userId,
            'subscription_id' => $subscription->id,
            'external_transaction_id' => $reference,
            'amount' => $amount,
            'currency' => $payload['data']['currency'] ?? 'NGN',
            'status' => 'completed',
            'type' => 'subscription',
            'payment_method' => 'Paystack',
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
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Paystack-Signature');
        $computedSignature = hash_hmac('sha512', $request->getContent(), $this->config['webhook_secret']);

        return hash_equals($computedSignature, $signature);
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
