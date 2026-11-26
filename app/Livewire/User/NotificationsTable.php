<?php

namespace App\Livewire\User;

use App\Models\App;
use App\Models\AppNotification;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsTable extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $statusFilter = [];

    public $appFilter = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAppFilter()
    {
        $this->resetPage();
    }

    #[On('notificationSent')]
    #[On('notificationDeleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function getStatusClass($status)
    {
        return match ($status) {
            'sent' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'pending' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'scheduled' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function render()
    {
        $user = auth()->user();

        // Get user's apps for filter dropdown
        $userApps = App::where('user_id', $user->id)
            ->whereHas('pushNotificationConfig', function ($query) {
                $query->where('enabled', true);
            })
            ->get();

        $notifications = AppNotification::query()
            ->where('user_id', $user->id)
            ->with(['app'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('body', 'like', '%'.$this->search.'%');
                });
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->when(count($this->appFilter) > 0, function ($query) {
                $query->whereIn('app_id', $this->appFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.user.notifications-table', [
            'notifications' => $notifications,
            'userApps' => $userApps,
        ]);
    }
}
