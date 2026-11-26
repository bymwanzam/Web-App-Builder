<?php

namespace App\Livewire\User;

use App\Services\PluginManager;
use Illuminate\Support\Str;
use Livewire\Component;

class PaymentGatewaySelector extends Component
{
    public $selectedGateway = null;

    public $gateways = [];

    protected $pluginManager;

    public function boot(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    public function mount()
    {
        // Load all active payment gateways
        $this->gateways = $this->pluginManager->getActiveGateways()
            ->map(function ($gateway) {
                return [
                    'name' => $gateway->getName(),
                    'description' => $gateway->getDescription(),
                    'icon' => $gateway->getIcon(),
                    'slug' => Str::slug($gateway->getName()),
                ];
            })
            ->toArray();

        // Set first gateway as default selected
        if (! empty($this->gateways)) {
            $this->selectedGateway = $this->gateways[0]['slug'];
        }
    }

    public function selectGateway($slug)
    {
        $this->selectedGateway = $slug;
        $this->dispatch('gateway-selected', gateway: $slug);
    }

    public function render()
    {
        return view('livewire.user.payment-gateway-selector');
    }
}
