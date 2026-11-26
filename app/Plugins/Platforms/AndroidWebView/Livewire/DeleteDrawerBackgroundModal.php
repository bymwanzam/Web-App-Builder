<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDrawerBackgroundModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-drawer-background')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->app->androidWebViewConfig->drawer_background_image) {
                \Storage::disk('public')->delete($this->app->androidWebViewConfig->drawer_background_image);

                $this->app->androidWebViewConfig->update(['drawer_background_image' => null]);

                $this->showModal = false;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Drawer background image deleted successfully!'
                );

                $this->dispatch('settings-updated');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete background image. Please try again.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.delete-drawer-background-modal');
    }
}
