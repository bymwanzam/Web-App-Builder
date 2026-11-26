<?php

namespace App\Livewire\Admin;

use App\Models\Plugin;
use App\Services\PluginManager;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\On;
use Livewire\Component;

class PluginConfigModal extends Component
{
    public $showModal = false;

    public $pluginId;

    public $pluginName;

    public $pluginSlug;

    public $hasCustomView = false;

    public $config = [];

    #[On('configure-plugin')]
    public function open($id)
    {
        $plugin = Plugin::findOrFail($id);

        $this->pluginId = $id;
        $this->pluginName = $plugin->name;
        $this->pluginSlug = $plugin->slug;

        // Check if plugin has custom configuration view
        $typeDirectory = match ($plugin->type) {
            'payment_gateway' => 'PaymentGateways',
            'bridge' => 'Bridges',
            'notification' => 'Notifications',
            default => 'Other',
        };

        $customViewPath = app_path("Plugins/{$typeDirectory}/{$plugin->slug}/resources/views/config-form.blade.php");
        $this->hasCustomView = File::exists($customViewPath);

        // Load existing config
        $this->config = $plugin->config ?? [];

        // Hide all config values in demo mode
        if (config('app.demo')) {
            $this->config = [];
        }

        $this->showModal = true;
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Plugin configuration is disabled in demo mode.'
            );

            return;
        }

        $plugin = Plugin::findOrFail($this->pluginId);

        try {
            $pluginManager = app(PluginManager::class);
            $pluginManager->configure($plugin, $this->config);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Plugin configured successfully!'
            );

            $this->dispatch('plugin-configured');
            $this->close();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to display errors in the form
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to configure plugin: '.$e->getMessage()
            );
        }
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset(['pluginId', 'pluginName', 'pluginSlug', 'hasCustomView', 'config']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.plugin-config-modal');
    }
}
