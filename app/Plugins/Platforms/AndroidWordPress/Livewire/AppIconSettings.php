<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use App\Services\IconExtractionService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppIconSettings extends Component
{
    use WithFileUploads;

    public App $app;

    public $iconZipFile;

    public function mount()
    {
        //
    }

    protected function rules()
    {
        return [
            'iconZipFile' => ['required', 'file', 'mimes:zip', 'max:10240'], // 10MB max
        ];
    }

    public function saveIcon()
    {
        $this->validate();

        try {
            $extractionService = new IconExtractionService;

            // Extract and validate the ZIP file
            $config = $this->app->androidWordPressConfig;
            $result = $extractionService->extractAndValidate(
                $this->iconZipFile->getRealPath(),
                $config->package_name
            );

            if (! $result['success']) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: $result['message']
                );

                return;
            }

            // Store the ZIP file
            $zipPath = $this->iconZipFile->storeAs(
                'icons/'.$config->package_name,
                'icon-'.time().'.zip',
                'public'
            );

            // Update config with icon paths
            $updateData = [
                'uploaded_icon_zip' => $zipPath,
                'icon_extracted_path' => $result['extracted_path'],
            ];

            // Update app_icon if a primary icon was extracted
            if (isset($result['app_icon_path']) && $result['app_icon_path']) {
                $updateData['app_icon'] = $result['app_icon_path'];
            }

            $config->update($updateData);

            // Clear the upload field
            $this->reset('iconZipFile');

            $this->dispatch('show-toast',
                type: 'success',
                message: 'App icon uploaded successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to upload icon: '.$e->getMessage()
            );
        }
    }

    #[On('settings-updated')]
    public function refreshApp()
    {
        // Refresh the app model from database
        $this->app = $this->app->fresh();

        // Clear file upload state
        $this->reset('iconZipFile');
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-icon-settings');
    }
}
