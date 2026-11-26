<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteSplashscreenImageModal extends Component
{
    public App $app;

    public $showModal = false;

    #[On('delete-splashscreen-image')]
    public function openModal()
    {
        $this->showModal = true;
    }

    public function confirmDelete()
    {
        try {
            if ($this->app->androidWordPressConfig->splashscreen_image) {
                \Storage::disk('public')->delete($this->app->androidWordPressConfig->splashscreen_image);

                $this->app->androidWordPressConfig->update(['splashscreen_image' => null]);

                $this->showModal = false;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Splashscreen background image deleted successfully!'
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
        return view('plugins.android-wordpress::livewire.delete-splashscreen-image-modal');
    }
}
