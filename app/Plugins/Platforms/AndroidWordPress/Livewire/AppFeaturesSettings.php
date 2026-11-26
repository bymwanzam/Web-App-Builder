<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppFeaturesSettings extends Component
{
    public App $app;

    // Feature toggles
    public $enable_comments;

    public $enable_search;

    public $enable_bookmarks;

    public $enable_offline_reading;

    public $enable_share;

    public $enable_user_auth;

    public $enable_push_notifications;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        $this->enable_comments = $config->enable_comments ?? true;
        $this->enable_search = $config->enable_search ?? true;
        $this->enable_bookmarks = $config->enable_bookmarks ?? true;
        $this->enable_offline_reading = $config->enable_offline_reading ?? false;
        $this->enable_share = $config->enable_share ?? true;
        $this->enable_user_auth = $config->enable_user_auth ?? false;
        $this->enable_push_notifications = $config->enable_push_notifications ?? false;
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
            'enable_comments' => ['boolean'],
            'enable_search' => ['boolean'],
            'enable_bookmarks' => ['boolean'],
            'enable_offline_reading' => ['boolean'],
            'enable_share' => ['boolean'],
            'enable_user_auth' => ['boolean'],
            'enable_push_notifications' => ['boolean'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $config = $this->app->androidWordPressConfig;
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Feature settings saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-features-settings');
    }
}
