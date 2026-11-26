<?php

namespace App\Livewire\User;

use App\Models\Plan;
use App\Services\PluginManager;
use Livewire\Attributes\On;
use Livewire\Component;

class PlansModal extends Component
{
    public $showModal = false;

    public $selectedPlan = null;

    public $isProcessing = false;

    public $showGatewaySelector = false;

    public $selectedGateway = null;

    public $selectedPlanForPayment = null;

    public $showInstructions = false;

    public $bankTransferInstructions = null;

    protected $pluginManager;

    public function boot(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Show the plans modal.
     */
    #[On('show-plans-modal')]
    public function show()
    {
        $this->showModal = true;
    }

    /**
     * Close the modal.
     */
    public function close()
    {
        $this->showModal = false;
        $this->selectedPlan = null;
        $this->isProcessing = false;
        $this->showGatewaySelector = false;
        $this->selectedGateway = null;
        $this->selectedPlanForPayment = null;
        $this->showInstructions = false;
        $this->bankTransferInstructions = null;
    }

    /**
     * Go back to plan selection from gateway selector.
     */
    public function backToPlans()
    {
        $this->showGatewaySelector = false;
        $this->selectedGateway = null;
        $this->selectedPlanForPayment = null;
        $this->showInstructions = false;
        $this->bankTransferInstructions = null;
        $this->isProcessing = false;
    }

    /**
     * Select a plan for subscription.
     */
    public function selectPlan($planId)
    {
        $this->isProcessing = true;

        $plan = Plan::findOrFail($planId);
        $user = auth()->user();
        $currentSubscription = $user->activeSubscription()->first();

        // Check if user already has this plan
        if ($currentSubscription && $currentSubscription->plan_id === $plan->id) {
            $this->dispatch('show-toast',
                type: 'success',
                message: 'You are already subscribed to this plan.'
            );
            $this->isProcessing = false;

            return;
        }

        // Free plans, and any plan for an admin, are assigned directly without payment
        if ($plan->price == 0 || $user->isAdmin()) {
            $this->subscribeToFreePlan($plan, $user->isAdmin() && $plan->price > 0);
            $this->isProcessing = false;

            return;
        }

        // For paid plans, check if payment gateways are available
        $activeGateways = $this->pluginManager->getActiveGateways();

        if ($activeGateways->isEmpty()) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No payment methods available. Please contact support.'
            );
            $this->isProcessing = false;

            return;
        }

        // Store plan and show gateway selector
        $this->selectedPlanForPayment = $plan;
        $this->showGatewaySelector = true;
        $this->isProcessing = false;
    }

    /**
     * Select a payment gateway and initiate payment.
     */
    public function selectGateway($gatewaySlug)
    {
        if (! $this->selectedPlanForPayment) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No plan selected. Please try again.'
            );

            return;
        }

        $this->selectedGateway = $gatewaySlug;
        $this->isProcessing = true;

        // Handle Bank Transfer specially - show instructions first
        if ($gatewaySlug === 'bank-transfer') {
            try {
                // Load instructions from plugin config
                $plugin = \App\Models\Plugin::active()
                    ->byType('payment_gateway')
                    ->where('slug', 'bank-transfer')
                    ->first();

                if (! $plugin) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Bank Transfer payment method is not available.'
                    );
                    $this->isProcessing = false;

                    return;
                }

                $config = $plugin->config ?? [];
                $this->bankTransferInstructions = $config['instructions'] ?? 'No instructions available.';
                $this->showGatewaySelector = false;
                $this->showInstructions = true;
                $this->isProcessing = false;

                return;
            } catch (\Exception $e) {
                \Log::error('Failed to load Bank Transfer instructions', [
                    'error' => $e->getMessage(),
                ]);

                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Failed to load payment instructions. Please try again.'
                );
                $this->isProcessing = false;

                return;
            }
        }

        // For other gateways, initiate payment (redirect to external gateway)
        return $this->initiatePayment($this->selectedPlanForPayment, $gatewaySlug);
    }

    /**
     * Go back to gateway selection from instructions screen.
     */
    public function backToGatewaysFromInstructions()
    {
        $this->showInstructions = false;
        $this->bankTransferInstructions = null;
        $this->showGatewaySelector = true;
        $this->isProcessing = false;
    }

    /**
     * Confirm Bank Transfer payment and create transaction.
     */
    public function confirmBankTransfer()
    {
        if (! $this->selectedPlanForPayment) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No plan selected. Please try again.'
            );

            return;
        }

        $user = auth()->user();

        // Check for existing pending bank transfer subscription
        $existingPending = \App\Models\Subscription::where('user_id', $user->id)
            ->where('payment_method', 'Bank Transfer')
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'You already have a pending bank transfer. Please complete or cancel it first.'
            );

            return;
        }

        $this->isProcessing = true;

        try {
            $plan = $this->selectedPlanForPayment;
            $amount = round($plan->price, 2);

            // Calculate renewal date from plan intervals
            $intervalUnit = $plan->interval_unit ?? 'month';
            $intervalCount = $plan->interval_count ?? 1;
            $renewalAt = match ($intervalUnit) {
                'day' => now()->addDays($intervalCount),
                'week' => now()->addWeeks($intervalCount),
                'month' => now()->addMonths($intervalCount),
                'year' => now()->addYears($intervalCount),
                default => now()->addMonth(),
            };

            // Create subscription with pending status
            $subscription = \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'payment_method' => 'Bank Transfer',
                'subscription_id' => 'BT-'.strtoupper(\Illuminate\Support\Str::random(10)),
                'status' => 'pending',
                'amount' => $amount,
                'renewal_at' => $renewalAt,
            ]);

            // Create pending transaction record with bank transfer instructions in metadata
            $transaction = \App\Models\Transaction::create([
                'transaction_id' => 'TXN-'.strtoupper(\Illuminate\Support\Str::random(10)),
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
                    'bank_transfer_instructions' => $this->bankTransferInstructions,
                ],
            ]);

            // Notify all admins of new pending bank transfer (don't let email failure break payment)
            try {
                $admins = \App\Models\User::admins()->get();
                if ($admins->isNotEmpty()) {
                    foreach ($admins as $admin) {
                        $admin->notify(new \App\Notifications\BankTransferPendingNotification($transaction));
                    }
                } else {
                    \Log::warning('Bank Transfer notification failed: no admin users found', [
                        'transaction_id' => $transaction->id,
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'amount' => $amount,
                    ]);
                }
            } catch (\Exception $notificationException) {
                \Log::error('Failed to send bank transfer admin notification', [
                    'transaction_id' => $transaction->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }

            // Close modal
            $this->close();

            // Show success toast
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Payment initiated. Please complete the bank transfer. You can view instructions on your billing page.'
            );

            // Refresh subscription data
            $this->dispatch('subscriptionUpdated');
        } catch (\Exception $e) {
            \Log::error('Bank Transfer confirmation failed', [
                'plan_id' => $this->selectedPlanForPayment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to process payment. Please try again.'
            );
            $this->isProcessing = false;
        }
    }

    /**
     * Subscribe to free plan directly.
     */
    protected function subscribeToFreePlan($plan, bool $adminGrant = false)
    {
        $user = auth()->user();

        // Cancel existing subscription
        $currentSubscription = $user->activeSubscription()->first();
        if ($currentSubscription) {
            $currentSubscription->update([
                'status' => 'cancelled',
                'ends_at' => now(),
            ]);
        }

        // Create new subscription
        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'amount' => 0,
            'payment_method' => $adminGrant ? 'admin' : 'none',
            'renewal_at' => null, // Free plans and admin grants don't have billing
        ]);

        // Update user plan (refresh the relation so credits refill from the new plan)
        $user->update(['plan_id' => $plan->id]);
        $user->load('plan');

        // Refill build credits
        $user->refillBuildCredits();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Successfully subscribed to '.$plan->name.' plan!'
        );
        $this->dispatch('subscriptionUpdated');
        $this->close();
    }

    /**
     * Initiate payment through payment gateway plugin.
     */
    protected function initiatePayment($plan, $gatewaySlug = null)
    {
        try {
            // Get active payment gateway plugins
            $activeGateways = $this->pluginManager->getActiveGateways();

            if ($activeGateways->isEmpty()) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'No payment methods available. Please contact support.'
                );
                $this->isProcessing = false;

                return;
            }

            // Find specific gateway or use first one
            if ($gatewaySlug) {
                $paymentPlugin = $activeGateways->first(function ($gateway) use ($gatewaySlug) {
                    return \Illuminate\Support\Str::slug($gateway->getName()) === $gatewaySlug;
                });

                if (! $paymentPlugin) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Selected payment method not found.'
                    );
                    $this->isProcessing = false;

                    return;
                }
            } else {
                $paymentPlugin = $activeGateways->first();
            }

            // Store plan selection in session
            session(['pending_plan_id' => $plan->id]);

            // Initiate subscription payment
            $response = $paymentPlugin->initPayment($plan, auth()->user());

            // Handle redirect response or URL string
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                // Extract URL and redirect using Livewire
                return $this->redirect($response->getTargetUrl(), navigate: false);
            } elseif (is_string($response)) {
                // Direct URL string - redirect using Livewire
                return $this->redirect($response, navigate: false);
            }

            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to initiate payment. Please try again.'
            );
            $this->isProcessing = false;
        } catch (\Exception $e) {
            \Log::error('Payment initiation failed', [
                'plan_id' => $plan->id,
                'plan_price' => $plan->price,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('show-toast',
                type: 'error',
                message: 'Payment error: '.$e->getMessage()
            );
            $this->isProcessing = false;
        }
    }

    /**
     * Check if a plan is the user's current plan.
     */
    public function isCurrentPlan($planId)
    {
        $user = auth()->user();
        $currentSubscription = $user->activeSubscription()->first();

        return $currentSubscription && $currentSubscription->plan_id === $planId;
    }

    /**
     * Get available payment gateways for display.
     */
    public function getAvailableGateways()
    {
        return $this->pluginManager->getActiveGateways()
            ->map(function ($gateway) {
                return [
                    'name' => $gateway->getName(),
                    'description' => $gateway->getDescription(),
                    'icon' => $gateway->getIcon(),
                    'slug' => \Illuminate\Support\Str::slug($gateway->getName()),
                ];
            })
            ->toArray();
    }

    public function render()
    {
        $plans = Plan::where('status', 'active')->orderBy('price')->get();
        $currentSubscription = auth()->user()->activeSubscription()->first();
        $availableGateways = $this->getAvailableGateways();

        return view('livewire.user.plans-modal', [
            'plans' => $plans,
            'currentSubscription' => $currentSubscription,
            'availableGateways' => $availableGateways,
        ]);
    }
}
