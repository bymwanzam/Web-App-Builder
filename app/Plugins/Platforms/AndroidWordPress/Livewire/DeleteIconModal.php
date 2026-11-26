<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

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
            if ($this->app->androidWordPressConfig->uploaded_icon_zip) {
                \Storage::disk('public')->delete($this->app->androidWordPressConfig->uploaded_icon_zip);
            }

            if ($this->app->androidWordPressConfig->icon_extracted_path) {
                \Storage::disk('public')->deleteDirectory($this->app->androidWordPressConfig->icon_extracted_path);
            }

            // Delete primary app icon
            if ($this->app->androidWordPressConfig->app_icon) {
                \Storage::disk('public')->delete($this->app->androidWordPressConfig->app_icon);
            }

            // Update app config
            $this->app->androidWordPressConfig->update([
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
        return view('plugins.android-wordpress::livewire.delete-icon-modal');
    }
}
