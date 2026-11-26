<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\SystemSetting;
use Livewire\Component;

class AppSettingsForm extends Component
{
    public $default_plan_id;

    public function mount()
    {
        $this->default_plan_id = SystemSetting::get('default_plan_id', null);
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        // Validate: allow empty (None) or valid plan ID
        $this->validate([
            'default_plan_id' => 'nullable|exists:plans,id',
        ]);

        // Get old default plan id
        $oldDefaultPlanId = SystemSetting::get('default_plan_id', null);

        // Convert empty string to null for storage
        $newPlanId = $this->default_plan_id ?: null;

        // Update the system setting
        if ($newPlanId) {
            SystemSetting::set('default_plan_id', $newPlanId, 'integer', 'app');
        } else {
            // Store empty/null value
            SystemSetting::set('default_plan_id', '', 'string', 'app');
        }

        // Sync Plan.is_default column
        // Reset the old default plan
        if ($oldDefaultPlanId) {
            Plan::where('id', $oldDefaultPlanId)->update(['is_default' => false]);
        }

        // Set the new default plan (if one is selected)
        if ($newPlanId) {
            Plan::where('id', $newPlanId)->update(['is_default' => true]);
        }

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Plan settings updated successfully!'
        );
    }

    public function render()
    {
        $plans = Plan::where('status', 'active')->get();

        return view('livewire.admin.app-settings-form', [
            'plans' => $plans,
        ]);
    }
}
