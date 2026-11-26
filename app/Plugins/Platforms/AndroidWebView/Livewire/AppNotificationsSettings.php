<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Models\PushNotificationConfig;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppNotificationsSettings extends Component
{
    use WithFileUploads;

    public App $app;

    public $google_services_file;

    public $google_services_file_upload;

    public $firebase_credentials_file;

    public $firebase_credentials_file_upload;

    public $enabled;

    public function mount()
    {
        $config = $this->app->pushNotificationConfig;

        if ($config) {
            $this->google_services_file = $config->google_services_file;
            $this->firebase_credentials_file = $config->firebase_credentials_file;
            $this->enabled = $config->enabled;
        } else {
            $this->enabled = false;
        }
    }

    protected function rules()
    {
        return [
            'google_services_file_upload' => ['nullable', 'file', 'mimes:json', 'max:1024'],
            'firebase_credentials_file_upload' => ['nullable', 'file', 'mimes:json', 'max:2048'],
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'enabled' => $this->enabled,
            ];

            // Handle Firebase Credentials file upload (Service Account JSON)
            if ($this->firebase_credentials_file_upload) {
                $path = $this->firebase_credentials_file_upload->store('fcm/'.$this->app->app_id, 'private');
                $data['firebase_credentials_file'] = $path;
                $this->firebase_credentials_file = $path;
            }

            // Handle Google Services file upload
            if ($this->google_services_file_upload) {
                $path = $this->google_services_file_upload->store('fcm/'.$this->app->app_id, 'private');
                $data['google_services_file'] = $path;
                $this->google_services_file = $path;
            }

            // Create or update config
            PushNotificationConfig::updateOrCreate(
                ['app_id' => $this->app->id],
                $data
            );

            $this->reset(['google_services_file_upload', 'firebase_credentials_file_upload']);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Notification settings saved successfully!'
            );

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    public function deleteFirebaseCredentials()
    {
        try {
            $config = $this->app->pushNotificationConfig;

            if ($config && $config->firebase_credentials_file) {
                \Storage::disk('private')->delete($config->firebase_credentials_file);

                $config->update(['firebase_credentials_file' => null]);
                $this->firebase_credentials_file = null;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Firebase credentials deleted successfully!'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete Firebase credentials. Please try again.'
            );
        }
    }

    public function deleteGoogleServices()
    {
        try {
            $config = $this->app->pushNotificationConfig;

            if ($config && $config->google_services_file) {
                \Storage::disk('private')->delete($config->google_services_file);

                $config->update(['google_services_file' => null]);
                $this->google_services_file = null;

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Google Services file deleted successfully!'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to delete Google Services file. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-notifications-settings');
    }
}
