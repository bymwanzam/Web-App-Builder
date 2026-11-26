<?php

namespace App\Livewire\User;

use App\Models\Plan;
use App\Services\PluginManager;
use Livewire\Component;

class PaymentInitialization extends Component
{
    public $plan;

    public $gateway;

    public $isProcessing = false;

    public $error = null;

    protected $pluginManager;

    public function boot(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    public function mount(?Plan $plan = null, ?string $gateway = null)
    {
        $this->plan = $plan;
        $this->gateway = $gateway;
    }

    public function initiatePayment()
    {
        $this->isProcessing = true;
        $this->error = null;

        try {
            // Get the payment gateway plugin
            $paymentPlugin = $this->pluginManager->getActiveGateways()
                ->first(fn ($p) => strtolower($p->getName()) === strtolower($this->gateway));

            if (! $paymentPlugin) {
                throw new \Exception('Selected payment gateway is not available');
            }

            // Initiate payment
            $response = $paymentPlugin->initPayment($this->plan, auth()->user());

            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                // Extract URL and redirect using Livewire
                return redirect($response->getTargetUrl());
            }

            $this->error = 'Failed to initiate payment. Please try again.';
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.user.payment-initialization');
    }
}
