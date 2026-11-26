<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDrawerLogoLightModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-drawer-logo-light')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->app->androidWordPressConfig->drawer_logo_light) {
                \Storage::disk('public')->delete($this->app->androidWordPressConfig->drawer_logo_light);

                $this->app->androidWordPressConfig->update(['drawer_logo_light' => null]);

                $this->showModal = false;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Drawer light logo deleted successfully!'
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
        return view('plugins.android-wordpress::livewire.delete-drawer-logo-light-modal');
    }
}
