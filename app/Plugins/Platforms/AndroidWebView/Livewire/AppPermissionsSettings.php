<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppPermissionsSettings extends Component
{
    public App $app;

    public $permission_location;

    public $permission_camera;

    public $permission_storage;

    public $permission_record_audio;

    public $permission_read_contacts;

    public $permission_vibrate;

    public function mount()
    {
        $config = $this->app->androidWebViewConfig;

        $this->permission_location = $config->permission_location;
        $this->permission_camera = $config->permission_camera;
        $this->permission_storage = $config->permission_storage;
        $this->permission_record_audio = $config->permission_record_audio;
        $this->permission_read_contacts = $config->permission_read_contacts;
        $this->permission_vibrate = $config->permission_vibrate;
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
            'permission_location' => ['boolean'],
            'permission_camera' => ['boolean'],
            'permission_storage' => ['boolean'],
            'permission_record_audio' => ['boolean'],
            'permission_read_contacts' => ['boolean'],
            'permission_vibrate' => ['boolean'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $config = $this->app->androidWebViewConfig;
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Permissions saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save permissions. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-permissions-settings');
    }
}
