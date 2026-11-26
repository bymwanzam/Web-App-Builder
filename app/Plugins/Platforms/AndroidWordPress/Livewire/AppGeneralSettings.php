<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class AppGeneralSettings extends Component
{
    public App $app;

    // Form fields
    public $name;

    public $site_url;

    public $app_id;

    public $screen_orientation;

    public $app_language;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        // Universal fields from app
        $this->name = $this->app->name;

        // Platform-specific fields from config
        $this->site_url = $config->site_url ?? '';
        $this->app_id = $config->package_name ?? '';
        $this->screen_orientation = $config->screen_orientation ?? 'system';
        $this->app_language = $config->app_language ?? 'en';
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
            'name' => ['required', 'string', 'max:255'],
            'site_url' => ['required', 'url', 'max:255'],
            'app_id' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z][a-z0-9]*(\.[a-z][a-z0-9]*)+$/',
                Rule::unique('android_wordpress_configs', 'package_name')->ignore($this->app->androidWordPressConfig->id ?? null),
            ],
            'screen_orientation' => ['required', 'in:system,portrait,landscape'],
            'app_language' => ['required', 'in:en'],
        ];
    }

    protected $messages = [
        'site_url.required' => 'WordPress site URL is required',
        'site_url.url' => 'WordPress site URL must be a valid URL',
        'app_id.regex' => 'APP ID must be in reverse domain format (e.g., com.example.app)',
        'app_id.unique' => 'This APP ID is already in use by another app',
    ];

    public function updated($propertyName)
    {
        // Skip validation for app_id since it's readonly
        if ($propertyName === 'app_id') {
            return;
        }

        // Real-time validation for the changed field
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        // Validate all fields
        $validated = $this->validate();

        try {
            // Update universal fields in app model
            $this->app->update([
                'name' => $validated['name'],
            ]);

            // Update platform-specific fields in config model
            $config = $this->app->androidWordPressConfig;
            $config->update([
                'site_url' => $validated['site_url'],
                'package_name' => $validated['app_id'],
                'screen_orientation' => $validated['screen_orientation'],
                'app_language' => $validated['app_language'],
            ]);

            // Dispatch success toast
            $this->dispatch('show-toast',
                type: 'success',
                message: 'General settings saved successfully!'
            );

            // Dispatch event to refresh preview panel if needed
            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            // Dispatch error toast
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    public function copyAppId()
    {
        // Dispatch browser event for clipboard copy
        $this->dispatch('copy-to-clipboard', text: $this->app_id);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-general-settings');
    }
}
