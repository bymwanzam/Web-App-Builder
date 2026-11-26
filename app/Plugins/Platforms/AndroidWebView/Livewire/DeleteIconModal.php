<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteIconModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-icon')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            // Delete uploaded icon files
            if ($this->app->androidWebViewConfig->uploaded_icon_zip) {
                \Storage::disk('public')->delete($this->app->androidWebViewConfig->uploaded_icon_zip);
            }

            if ($this->app->androidWebViewConfig->icon_extracted_path) {
                \Storage::disk('public')->deleteDirectory($this->app->androidWebViewConfig->icon_extracted_path);
            }

            // Delete primary app icon
            if ($this->app->androidWebViewConfig->app_icon) {
                \Storage::disk('public')->delete($this->app->androidWebViewConfig->app_icon);
            }

            // Update app config
            $this->app->androidWebViewConfig->update([
                'uploaded_icon_zip' => null,
                'icon_extracted_path' => null,
                'app_icon' => null,
            ]);

            $this->showModal = false;

            $this->dispatch('show-toast',
                type: 'success',
                message: 'App icon removed successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to remove icon. Please try again.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.delete-icon-modal');
    }
}
