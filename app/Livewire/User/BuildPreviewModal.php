<?php

namespace App\Livewire\User;

use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;

class BuildPreviewModal extends Component
{
    public ?AppBuild $build = null;

    public bool $showModal = false;

    #[On('view-preview')]
    public function viewPreview($id)
    {
        $this->build = AppBuild::with('app')->findOrFail($id);

        // Ensure user owns this build's app
        if ($this->build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->build = null;
    }

    public function render()
    {
        return view('livewire.user.build-preview-modal');
    }
}
