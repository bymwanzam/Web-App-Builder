<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UsersTable extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $statusFilter = [];

    public $roleFilter = [];

    public $planFilter = [];

    #[On('userUpdated')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingPlanFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->with('plan')
            ->withCount('apps')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->when(count($this->statusFilter) > 0, function ($query) {
                $query->whereIn('status', $this->statusFilter);
            })
            ->when(count($this->roleFilter) > 0, function ($query) {
                $query->whereIn('role', $this->roleFilter);
            })
            ->when(count($this->planFilter) > 0, function ($query) {
                $query->whereHas('plan', function ($q) {
                    $q->whereIn('name', $this->planFilter);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.users-table', [
            'users' => $users,
        ]);
    }
}
