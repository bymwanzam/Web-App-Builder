<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDrawerLogoModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-drawer-logo')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->app->androidWebViewConfig->drawer_logo) {
                \Storage::disk('public')->delete($this->app->androidWebViewConfig->drawer_logo);

                $this->app->androidWebViewConfig->update(['drawer_logo' => null]);

                $this->showModal = false;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Drawer logo deleted successfully!'
                );

                $this->dispatch('settings-updated');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete logo. Please try again.'
            );
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.delete-drawer-logo-modal');
    }
}
