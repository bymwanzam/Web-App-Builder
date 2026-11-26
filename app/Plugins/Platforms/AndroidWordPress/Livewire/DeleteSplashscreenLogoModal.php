<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteSplashscreenLogoModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-splashscreen-logo')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->app->androidWordPressConfig->splashscreen_logo) {
                \Storage::disk('public')->delete($this->app->androidWordPressConfig->splashscreen_logo);

                $this->app->androidWordPressConfig->update(['splashscreen_logo' => null]);

                $this->showModal = false;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Splashscreen logo deleted successfully!'
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
        return view('plugins.android-wordpress::livewire.delete-splashscreen-logo-modal');
    }
}
