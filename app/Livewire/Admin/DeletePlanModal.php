<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Livewire\Attributes\On;
use Livewire\Component;

class DeletePlanModal extends Component
{
    public $planId;

    public $planName;

    public $showModal = false;

    #[On('delete-plan')]
    public function confirmDelete($id, $name)
    {
        $this->planId = $id;
        $this->planName = $name;
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

        if ($this->planId) {
            $plan = Plan::findOrFail($this->planId);

            // Prevent deletion of default plan
            if ($plan->is_default) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Cannot delete the default plan.'
                );
                $this->closeModal();

                return;
            }

            $plan->delete();
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Plan deleted successfully!'
            );
            $this->closeModal();
            $this->dispatch('planUpdated');
            $this->dispatch('closePlanForm');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->planId = null;
        $this->planName = null;
    }

    public function render()
    {
        return view('livewire.admin.delete-plan-modal');
    }
}
