<?php

namespace App\Livewire\Admin;

use App\Services\DashboardStatsService;
use Livewire\Component;

class OverviewStats extends Component
{
    public string $dateRange = 'all';

    public bool $autoRefresh = false;

    public function mount()
    {
        $this->dateRange = 'all';
        $this->autoRefresh = false;
    }

    public function setDateRange(string $range)
    {
        $this->dateRange = $range;
        $this->dispatch('dateRangeChanged', range: $range);
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = ! $this->autoRefresh;
    }

    public function refreshStats()
    {
        app(DashboardStatsService::class)->clearCache();
        $this->dispatch('statsRefreshed');
    }

    public function render()
    {
        $stats = app(DashboardStatsService::class)->getStats($this->dateRange);

        return view('livewire.admin.overview-stats', [
            'stats' => $stats,
        ]);
    }
}
