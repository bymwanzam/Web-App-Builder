<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Livewire\Attributes\On;
use Livewire\Component;

class PlansGrid extends Component
{
    #[On('planUpdated')]
    public function refreshPlans()
    {
        // This method will trigger a re-render
    }

    public function render()
    {
        $plans = Plan::all();

        return view('livewire.admin.plans-grid', [
            'plans' => $plans,
        ]);
    }
}
