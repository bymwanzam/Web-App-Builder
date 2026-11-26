<?php

namespace App\Livewire\Admin;

use App\Models\Plugin;
use Livewire\Attributes\On;
use Livewire\Component;

class PluginsGrid extends Component
{
    public $search = '';

    public function updatingSearch()
    {
        // Can add pagination reset here if needed
    }

    public function configure($id)
    {
        $this->dispatch('configure-plugin', id: $id);
    }

    public function activate($id)
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Plugin management is disabled in demo mode.'
            );

            return;
        }

        try {
            $plugin = Plugin::findOrFail($id);
            $plugin->activate();

            $this->dispatch('plugin-activated');
            session()->flash('success', "{$plugin->name} has been activated successfully.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function deactivate($id)
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Plugin management is disabled in demo mode.'
            );

            return;
        }

        try {
            $plugin = Plugin::findOrFail($id);
            $plugin->deactivate();

            $this->dispatch('plugin-deactivated');
            session()->flash('success', "{$plugin->name} has been deactivated.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function uninstall($id)
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Plugin management is disabled in demo mode.'
            );

            return;
        }

        $this->dispatch('delete-plugin', id: $id);
    }

    #[On('plugin-configured')]
    #[On('plugin-deleted')]
    #[On('plugin-uploaded')]
    #[On('plugin-activated')]
    #[On('plugin-deactivated')]
    public function render()
    {
        // Show only installed plugins from database with search
        $plugins = Plugin::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('type', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.plugins-grid', [
            'plugins' => $plugins,
        ]);
    }
}
