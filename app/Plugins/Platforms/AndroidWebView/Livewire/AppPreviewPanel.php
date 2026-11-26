<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;

class AppPreviewPanel extends Component
{
    public App $app;

    public ?int $selectedBuildId = null;

    public ?AppBuild $selectedBuild = null;

    #[On('build-selected')]
    public function selectBuild($buildId)
    {
        $this->selectedBuildId = $buildId;
        $this->selectedBuild = AppBuild::with('app')->find($buildId);

        // Ensure build belongs to this app
        if ($this->selectedBuild && $this->selectedBuild->app_id !== $this->app->id) {
            $this->selectedBuild = null;
            $this->selectedBuildId = null;
        }
    }

    #[On('settings-updated')]
    public function refreshPreview()
    {
        // Refresh the app model to get latest changes
        $this->app = $this->app->fresh();

        // Refresh selected build if one is selected
        if ($this->selectedBuildId) {
            $this->selectedBuild = AppBuild::with('app')->find($this->selectedBuildId);
        }
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-preview-panel');
    }
}
