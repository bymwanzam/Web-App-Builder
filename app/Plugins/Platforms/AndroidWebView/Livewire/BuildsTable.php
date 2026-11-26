<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Models\AppBuild;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BuildsTable extends Component
{
    use WithPagination;

    public App $app;

    public $search = '';

    public $perPage = 10;

    public $platformFilter = [];

    public $statusFilter = [];

    public function mount(App $app)
    {
        $this->app = $app;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPlatformFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[On('buildUpdated')]
    #[On('buildDeleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function deleteBuild($buildId)
    {
        $build = AppBuild::findOrFail($buildId);

        // Ensure user owns this build's app
        if ($build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->dispatch('delete-build', id: $buildId);
    }

    public function viewLogs($buildId)
    {
        $this->dispatch('view-logs', id: $buildId);
    }

    public function downloadBuild($buildId)
    {
        $build = AppBuild::findOrFail($buildId);

        // Ensure user owns this build's app
        if ($build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->dispatch('download-build', id: $buildId);
    }

    public function viewPreview($buildId)
    {
        $build = AppBuild::findOrFail($buildId);

        // Ensure user owns this build's app
        if ($build->app->user_id !== auth()->id()) {
            abort(403);
        }

        $this->dispatch('build-selected', buildId: $buildId);
    }

    public function render()
    {
        $builds = $this->app->builds()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('version_name', 'like', '%'.$this->search.'%')
                        ->orWhere('platform', 'like', '%'.$this->search.'%')
                        ->orWhere('build_type', 'like', '%'.$this->search.'%')
                        ->orWhere('id', 'like', '%'.$this->search.'%');
                });
            })
            ->when(count($this->platformFilter) > 0, function ($query) {
                $query->whereIn('platform', $this->platformFilter);
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('plugins.android-webview::livewire.builds-table', [
            'builds' => $builds,
        ]);
    }
}
