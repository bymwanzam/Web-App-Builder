<?php

namespace App\Livewire\Admin;

use App\Models\Plugin;
use App\Services\PluginManager;
use Livewire\Attributes\On;
use Livewire\Component;

class DeletePluginModal extends Component
{
    public $showModal = false;

    public $pluginId;

    public $pluginName;

    #[On('delete-plugin')]
    public function open($id)
    {
        $plugin = Plugin::findOrFail($id);

        $this->pluginId = $id;
        $this->pluginName = $plugin->name;
        $this->showModal = true;
    }

    public function delete()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Plugin management is disabled in demo mode.'
            );
            $this->close();

            return;
        }

        try {
            $plugin = Plugin::findOrFail($this->pluginId);

            // Prevent uninstalling core platform plugins
            if ($plugin->slug === 'android-webview') {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Cannot uninstall the core platform plugin.'
                );
                $this->close();

                return;
            }

            $pluginManager = app(PluginManager::class);

            $pluginManager->uninstall($plugin);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Plugin uninstalled successfully!'
            );

            $this->dispatch('plugin-deleted');
            $this->close();
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to uninstall plugin: '.$e->getMessage()
            );
        }
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset(['pluginId', 'pluginName']);
    }

    public function render()
    {
        return view('livewire.admin.delete-plugin-modal');
    }
}
