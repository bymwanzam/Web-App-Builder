<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogsTable extends Component
{
    use WithPagination;

    public string $search = '';

    public array $actionFilter = [];

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 20;

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => []],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->actionFilter = [];
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::query()
            ->with(['user', 'actor'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($uq) {
                        $uq->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                        ->orWhere('action', 'like', '%'.$this->search.'%')
                        ->orWhere('ip_address', 'like', '%'.$this->search.'%');
                });
            })
            ->when(! empty($this->actionFilter), function ($query) {
                $query->whereIn('action', $this->actionFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $actionTypes = AuditLog::distinct()->pluck('action')->sort()->values();

        return view('livewire.admin.audit-logs-table', [
            'logs' => $logs,
            'actionTypes' => $actionTypes,
        ]);
    }
}
