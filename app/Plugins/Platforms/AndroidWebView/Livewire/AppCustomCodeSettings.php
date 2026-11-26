<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Component;

class AppCustomCodeSettings extends Component
{
    public App $app;

    public $custom_css;

    public $custom_js;

    public $canEdit = false;

    public function mount()
    {
        $config = $this->app->androidWebViewConfig;

        $this->custom_css = $config->custom_css;
        $this->custom_js = $config->custom_js;

        // Check if user's plan allows custom code
        $user = auth()->user();
        $this->canEdit = $user->plan && $user->plan->can_use_custom_code;
    }

    protected function rules()
    {
        return [
            'custom_css' => ['nullable', 'string', 'max:100000'],
            'custom_js' => ['nullable', 'string', 'max:100000'],
        ];
    }

    public function save()
    {
        // Check permission before saving
        if (! $this->canEdit) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Your plan does not include custom code access. Please upgrade your plan.'
            );

            return;
        }

        $validated = $this->validate();

        try {
            $config = $this->app->androidWebViewConfig;
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Custom code saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save custom code. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-custom-code-settings');
    }
}
