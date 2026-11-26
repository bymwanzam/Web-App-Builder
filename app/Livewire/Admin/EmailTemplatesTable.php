<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EmailTemplatesTable extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public $categoryFilter = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    #[On('templateUpdated')]
    public function refreshTemplates()
    {
        // This method will trigger a re-render
    }

    public function getCategoryBadgeColor($category)
    {
        return match ($category) {
            'build' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'payment' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'auth' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function render()
    {
        $templates = EmailTemplate::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('subject', 'like', '%'.$this->search.'%');
            })
            ->when(count($this->categoryFilter) > 0, function ($query) {
                $query->whereIn('category', $this->categoryFilter);
            })
            ->orderBy('category')
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.email-templates-table', [
            'templates' => $templates,
            'categories' => EmailTemplate::getCategories(),
        ]);
    }
}
