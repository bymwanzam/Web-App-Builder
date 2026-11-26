<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppWebViewSettings extends Component
{
    public App $app;

    // WebView-specific settings
    public $user_agent;

    public $enable_javascript = true;

    public $enable_dom_storage = true;

    public $enable_zoom = false;

    public $enable_cache = true;

    public function mount()
    {
        $config = $this->app->androidWebViewConfig;
        $buildConfig = $config->build_config ?? [];

        // Load current WebView settings
        $this->user_agent = $config->user_agent ?? '';
        $this->enable_javascript = $buildConfig['enable_javascript'] ?? true;
        $this->enable_dom_storage = $buildConfig['enable_dom_storage'] ?? true;
        $this->enable_zoom = $buildConfig['enable_zoom'] ?? false;
        $this->enable_cache = $buildConfig['enable_cache'] ?? true;
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
            'user_agent' => ['nullable', 'string', 'max:1000'],
            'enable_javascript' => ['boolean'],
            'enable_dom_storage' => ['boolean'],
            'enable_zoom' => ['boolean'],
            'enable_cache' => ['boolean'],
        ];
    }

    public function save()
    {
        // Validate all fields
        $validated = $this->validate();

        try {
            $config = $this->app->androidWebViewConfig;

            // Get existing build_config and update with new values
            $buildConfig = $config->build_config ?? [];
            $buildConfig['enable_javascript'] = $validated['enable_javascript'];
            $buildConfig['enable_dom_storage'] = $validated['enable_dom_storage'];
            $buildConfig['enable_zoom'] = $validated['enable_zoom'];
            $buildConfig['enable_cache'] = $validated['enable_cache'];

            // Update config
            $config->update([
                'user_agent' => $validated['user_agent'],
                'build_config' => $buildConfig,
            ]);

            // Dispatch success toast
            $this->dispatch('show-toast',
                type: 'success',
                message: 'WebView settings saved successfully!'
            );

            // Dispatch event to refresh preview panel if needed
            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            // Dispatch error toast
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save WebView settings. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-web-view-settings');
    }
}
