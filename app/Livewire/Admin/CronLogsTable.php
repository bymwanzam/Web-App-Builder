<?php

namespace App\Livewire\Admin;

use App\Models\CronLog;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CronLogsTable extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = [];

    public $jobFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingJobFilter()
    {
        $this->resetPage();
    }

    #[On('jobExecuted')]
    public function refreshLogs()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = CronLog::query()
            ->excludePruning()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('job_name', 'like', '%'.$this->search.'%')
                        ->orWhere('message', 'like', '%'.$this->search.'%');
                });
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->when($this->jobFilter, function ($query) {
                $query->where('job_name', $this->jobFilter);
            })
            ->orderBy('started_at', 'desc')
            ->paginate(20);

        $jobs = CronLog::select('job_name')
            ->excludePruning()
            ->distinct()
            ->orderBy('job_name')
            ->pluck('job_name');

        return view('livewire.admin.cron-logs-table', [
            'logs' => $logs,
            'availableJobs' => $jobs,
        ]);
    }
}
