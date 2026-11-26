<?php

namespace App\Livewire\Admin;

use App\Models\AppBuilder;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteBuilderModal extends Component
{
    public $showModal = false;

    public $builderId = null;

    public $builderName = '';

    #[On('delete-builder')]
    public function confirmDelete($id)
    {
        $builder = AppBuilder::findOrFail($id);

        $this->builderId = $builder->id;
        $this->builderName = $builder->name;
        $this->showModal = true;
    }

    public function delete()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Deletion is disabled in demo mode.'
            );
            $this->closeModal();

            return;
        }

        $builder = AppBuilder::findOrFail($this->builderId);

        // Check if builder has active builds
        if ($builder->current_builds_count > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Cannot delete builder with active builds. Please wait for builds to complete.'
            );
            $this->closeModal();

            return;
        }

        $builder->delete();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Builder deleted successfully.'
        );

        $this->dispatch('builderDeleted');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.delete-builder-modal');
    }
}
