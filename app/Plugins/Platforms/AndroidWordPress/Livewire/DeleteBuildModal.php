<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteBuildModal extends Component
{
    public $showModal = false;

    public $buildId = null;

    public $platform = '';

    public $version = '';

    #[On('delete-build')]
    public function confirmDelete($id)
    {
        $build = AppBuild::findOrFail($id);

        // Ensure user owns this build's app
        if ($build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->buildId = $build->id;
        $this->platform = ucfirst($build->platform);
        $this->version = "{$build->version_name} (Build #{$build->version_code})";
        $this->showModal = true;
    }

    public function delete()
    {
        $build = AppBuild::findOrFail($this->buildId);

        // Ensure user owns this build's app
        if ($build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $build->delete();

        $this->showModal = false;
        $this->dispatch('buildDeleted');
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Build deleted successfully'
        );
        $this->reset(['buildId', 'platform', 'version']);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['buildId', 'platform', 'version']);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.delete-build-modal');
    }
}
