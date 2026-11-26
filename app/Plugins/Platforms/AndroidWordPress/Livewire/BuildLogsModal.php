<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;

class BuildLogsModal extends Component
{
    public $showModal = false;

    public $build = null;

    #[On('view-logs')]
    public function viewLogs($id)
    {
        $this->build = AppBuild::findOrFail($id);

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

    public function downloadLogs()
    {
        if (! $this->build || ! $this->build->build_logs) {
            return;
        }

        $filename = "build-{$this->build->id}-logs.txt";
        $content = $this->build->build_logs;

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.build-logs-modal');
    }
}
