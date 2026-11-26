<?php

namespace App\Livewire\Admin;

use App\Models\AppBuilder;
use App\Services\PluginManager;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class BuilderForm extends Component
{
    public $showModal = false;

    public $builderId = null;

    public $name = '';

    public $url = '';

    public $port = 8080;

    public $server_key = '';

    public $max_queue = 5;

    public $platforms = [];

    public $status = 'active';

    public $credit_cost = 1;

    public $isEdit = false;

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'server_key' => 'required|string|max:255',
            'max_queue' => 'required|integer|min:1|max:100',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            'credit_cost' => 'required|integer|min:1|max:100',
        ];

        if ($this->isEdit) {
            $rules['server_key'] .= '|unique:app_builders,server_key,'.$this->builderId;
        } else {
            $rules['server_key'] .= '|unique:app_builders,server_key';
        }

        return $rules;
    }

    public function generateServerKey()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'This action is disabled in demo mode.'
            );

            return;
        }

        $this->server_key = Str::random(32);
    }

    #[On('edit-builder')]
    public function edit($id)
    {
        $this->resetForm();
        $builder = AppBuilder::findOrFail($id);

        $this->builderId = $builder->id;
        $this->name = $builder->name;
        $this->url = $builder->url;
        $this->port = $builder->port;
        $this->server_key = $builder->server_key;
        $this->max_queue = $builder->max_queue;
        $this->platforms = $builder->platforms ?? [];
        $this->status = $builder->status;
        $this->credit_cost = $builder->credit_cost ?? 1;
        $this->isEdit = true;

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->server_key = '';
        }

        $this->showModal = true;
    }

    #[On('create-builder')]
    public function create()
    {
        $this->resetForm();
        $this->generateServerKey();
        $this->showModal = true;
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'This cannot be modified in demo mode.'
            );

            return;
        }

        $this->validate();

        if ($this->isEdit) {
            $builder = AppBuilder::findOrFail($this->builderId);
            $builder->update([
                'name' => $this->name,
                'url' => $this->url,
                'port' => $this->port,
                'server_key' => $this->server_key,
                'max_queue' => $this->max_queue,
                'platforms' => $this->platforms,
                'status' => $this->status,
                'credit_cost' => $this->credit_cost,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Builder updated successfully.'
            );
        } else {
            AppBuilder::create([
                'name' => $this->name,
                'url' => $this->url,
                'port' => $this->port,
                'server_key' => $this->server_key,
                'max_queue' => $this->max_queue,
                'platforms' => $this->platforms,
                'status' => $this->status,
                'credit_cost' => $this->credit_cost,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Builder created successfully.'
            );
        }

        $this->dispatch('builderUpdated');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->builderId = null;
        $this->name = '';
        $this->url = '';
        $this->port = 8080;
        $this->server_key = '';
        $this->max_queue = 5;
        $this->platforms = [];
        $this->status = 'active';
        $this->credit_cost = 1;
        $this->isEdit = false;
        $this->resetValidation();
    }

    /**
     * Get available platform plugins.
     */
    public function getAvailablePlatformsProperty()
    {
        $pluginManager = app(PluginManager::class);

        return $pluginManager->getActivePlatforms()
            ->map(function ($platform) {
                return [
                    'id' => $platform->getPlatformId(),
                    'name' => $platform->getName(),
                ];
            });
    }

    public function render()
    {
        return view('livewire.admin.builder-form');
    }
}
