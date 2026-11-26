<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppNotificationsSettings extends Component
{
    use WithFileUploads;

    public App $app;

    public $enable_push_notifications;

    public $google_services_json;

    public $google_services_file;

    public $firebase_service_account;

    public $firebase_service_account_file;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        $this->enable_push_notifications = $config->enable_push_notifications ?? false;
        $this->google_services_json = $config->google_services_json;
        $this->firebase_service_account = $config->firebase_service_account ? true : false;
    }

    #[On('app-config-updated')]
    public function refreshFromAi(int $appId): void
    {
        if ($this->app->id === $appId) {
            $this->app->refresh();
            $this->mount();
        }
    }

    protected function rules()
    {
        return [
            'enable_push_notifications' => ['boolean'],
            'google_services_file' => ['nullable', 'file', 'mimes:json', 'max:1024'], // 1MB max
            'firebase_service_account_file' => ['nullable', 'file', 'mimes:json', 'max:2048'], // 2MB max
        ];
    }

    protected function messages()
    {
        return [
            'google_services_file.mimes' => 'The google-services.json file must be a JSON file.',
            'firebase_service_account_file.mimes' => 'The Firebase service account file must be a JSON file.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'enable_push_notifications' => $this->enable_push_notifications,
            ];

            // Handle google-services.json upload
            if ($this->google_services_file) {
                // Validate it's valid JSON with required fields
                $content = file_get_contents($this->google_services_file->getRealPath());
                $json = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Invalid JSON file. Please upload a valid google-services.json file.'
                    );

                    return;
                }

                // Verify required keys exist
                if (! isset($json['project_info']) || ! isset($json['client'])) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Invalid google-services.json. Missing required project_info or client section.'
                    );

                    return;
                }

                // Store the file
                $path = $this->google_services_file->storeAs(
                    'firebase/'.$this->app->id,
                    'google-services.json',
                    'local' // Use local disk for security
                );
                $data['google_services_json'] = $path;
                $this->google_services_json = $path;
            }

            // Handle Firebase service account upload
            if ($this->firebase_service_account_file) {
                // Validate it's valid JSON with required fields
                $content = file_get_contents($this->firebase_service_account_file->getRealPath());
                $json = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Invalid JSON file. Please upload a valid Firebase service account file.'
                    );

                    return;
                }

                // Verify required keys exist
                if (! isset($json['type']) || $json['type'] !== 'service_account') {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Invalid Firebase service account file. Missing or invalid type field.'
                    );

                    return;
                }

                // Store the content encrypted (not the file path)
                $data['firebase_service_account'] = $content;
                $this->firebase_service_account = true;
            }

            $config = $this->app->androidWordPressConfig;
            $config->update($data);

            $this->reset(['google_services_file', 'firebase_service_account_file']);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Push notification settings saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings: '.$e->getMessage()
            );
        }
    }

    public function deleteGoogleServices()
    {
        try {
            $config = $this->app->androidWordPressConfig;

            // Delete the file if it exists
            if ($config->google_services_json) {
                Storage::disk('local')->delete($config->google_services_json);
            }

            $config->update(['google_services_json' => null]);
            $this->google_services_json = null;

            $this->dispatch('show-toast',
                type: 'success',
                message: 'google-services.json deleted successfully!'
            );

            $this->dispatch('settings-updated');
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete file: '.$e->getMessage()
            );
        }
    }

    public function deleteFirebaseServiceAccount()
    {
        try {
            $config = $this->app->androidWordPressConfig;
            $config->update(['firebase_service_account' => null]);
            $this->firebase_service_account = false;

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Firebase service account deleted successfully!'
            );

            $this->dispatch('settings-updated');
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete credentials: '.$e->getMessage()
            );
        }
    }

    #[On('settings-updated')]
    public function refreshApp()
    {
        $this->app = $this->app->fresh();

        $config = $this->app->androidWordPressConfig;

        $this->google_services_json = $config->google_services_json;
        $this->firebase_service_account = $config->firebase_service_account ? true : false;

        $this->reset(['google_services_file', 'firebase_service_account_file']);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-notifications-settings');
    }
}
