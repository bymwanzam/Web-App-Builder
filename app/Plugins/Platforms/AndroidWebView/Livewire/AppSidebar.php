<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppSidebar extends Component
{
    public App $app;

    public $activeSection = 'general';

    public function switchSection($section)
    {
        $this->activeSection = $section;
        $this->dispatch('section-changed', $section);
    }

    #[On('section-changed')]
    public function updateActiveSection($section)
    {
        $this->activeSection = $section;
    }

    public function shouldShowNavigation()
    {
        return $this->app->androidWebViewConfig->layout_template !== 'blank';
    }

    public function shouldShowHostedPages()
    {
        return auth()->user()->canUseHostedPages();
    }

    #[On('settings-updated')]
    public function refreshApp()
    {
        // Refresh the app model and config from database to get latest layout_template and other settings
        $this->app->refresh();
        $this->app->load('androidWebViewConfig');
    }

    #[On('app-config-updated')]
    public function refreshFromAi(int $appId): void
    {
        if ($this->app->id === $appId) {
            $this->app->refresh();
            $this->app->load('androidWebViewConfig');
        }
    }

    public function render()
    {
        // Eager load config for view
        $this->app->load('androidWebViewConfig');

        return view('plugins.android-webview::livewire.app-sidebar');
    }
}
