<?php

namespace App\Livewire\Admin;

use App\Services\PluginManager;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadPluginModal extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $pluginFile;

    public $uploading = false;

    #[On('upload-plugin')]
    public function open()
    {
        $this->reset(['pluginFile', 'uploading']);
        $this->showModal = true;
    }

    public function upload()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast', type: 'error', message: 'Plugin uploads are disabled in demo mode.');

            return;
        }

        $this->validate([
            'pluginFile' => 'required|file|mimes:zip|max:10240', // Max 10MB
        ]);

        $this->uploading = true;

        try {
            $pluginManager = app(PluginManager::class);
            $plugin = $pluginManager->installFromZip($this->pluginFile);

            $this->dispatch('show-toast',
                type: 'success',
                message: "Plugin '{$plugin->name}' installed successfully!"
            );

            $this->dispatch('plugin-uploaded');
            $this->close();
        } catch (\Exception $e) {
            $this->uploading = false;

            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to install plugin: '.$e->getMessage()
            );
        }
    }

    public function close()
    {
        $this->showModal = false;
        $this->reset(['pluginFile', 'uploading']);
    }

    public function render()
    {
        return view('livewire.admin.upload-plugin-modal');
    }
}
