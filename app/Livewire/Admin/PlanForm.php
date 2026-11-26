<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\SystemSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class PlanForm extends Component
{
    public $planId;

    public $name;

    public $price;

    public $status = 'active';

    public $features = [];

    public $monthly_build_credits = 10;

    public $unlimited_builds = false;

    public $can_use_custom_code = false;

    public $can_use_appetize = false;

    public $monthly_appetize_minutes = 0;

    public $unlimited_appetize = false;

    public $can_use_api = false;

    public $can_use_hosted_pages = false;

    public $max_hosted_pages_per_app = 5;

    public $unlimited_hosted_pages = false;

    public $can_use_ai_chat = false;

    public $monthly_ai_tokens = 0;

    public $unlimited_ai_tokens = false;

    public $allow_user_ai_api_key = false;

    public $allowed_builders = [];

    public $allow_all_builders = true;

    public $showModal = false;

    // Feature form fields
    public $featureName = '';

    public $featureIncluded = true;

    #[On('create-plan')]
    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-plan')]
    public function edit($id)
    {
        $this->resetForm();
        $plan = Plan::findOrFail($id);

        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->status = $plan->status;
        $this->features = $plan->features;
        $this->monthly_build_credits = $plan->monthly_build_credits === -1 ? 0 : $plan->monthly_build_credits;
        $this->unlimited_builds = $plan->monthly_build_credits === -1;
        $this->can_use_custom_code = $plan->can_use_custom_code;
        $this->can_use_appetize = $plan->can_use_appetize;
        $this->monthly_appetize_minutes = $plan->monthly_appetize_minutes === -1 ? 0 : ($plan->monthly_appetize_minutes ?? 0);
        $this->unlimited_appetize = $plan->monthly_appetize_minutes === -1;
        $this->can_use_api = $plan->can_use_api;
        $this->can_use_hosted_pages = $plan->can_use_hosted_pages;
        $this->max_hosted_pages_per_app = $plan->max_hosted_pages_per_app === null ? 0 : $plan->max_hosted_pages_per_app;
        $this->unlimited_hosted_pages = $plan->max_hosted_pages_per_app === null;
        $this->can_use_ai_chat = $plan->can_use_ai_chat;
        $this->monthly_ai_tokens = $plan->monthly_ai_tokens === -1 ? 0 : ($plan->monthly_ai_tokens ?? 0);
        $this->unlimited_ai_tokens = $plan->monthly_ai_tokens === -1;
        $this->allow_user_ai_api_key = $plan->allow_user_ai_api_key;
        $this->allowed_builders = $plan->allowed_builders ?? [];
        $this->allow_all_builders = $plan->allowed_builders === null;
        $this->showModal = true;
    }

    #[On('closePlanForm')]
    public function closeFromDelete()
    {
        $this->closeModal();
    }

    public function addFeature()
    {
        $this->validate([
            'featureName' => 'required|string|max:255',
        ]);

        $this->features[] = [
            'name' => $this->featureName,
            'included' => $this->featureIncluded,
        ];

        $this->featureName = '';
        $this->featureIncluded = true;
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
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

        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'features' => 'required|array|min:1',
            'monthly_build_credits' => $this->unlimited_builds ? 'nullable' : 'required|integer|min:1',
            'can_use_custom_code' => 'boolean',
            'can_use_appetize' => 'boolean',
            'monthly_appetize_minutes' => ($this->can_use_appetize && ! $this->unlimited_appetize) ? 'required|integer|min:1' : 'nullable',
            'can_use_api' => 'boolean',
            'can_use_hosted_pages' => 'boolean',
            'max_hosted_pages_per_app' => ($this->can_use_hosted_pages && ! $this->unlimited_hosted_pages) ? 'required|integer|min:1' : 'nullable',
            'can_use_ai_chat' => 'boolean',
            'monthly_ai_tokens' => ($this->can_use_ai_chat && ! $this->unlimited_ai_tokens) ? 'required|integer|min:1000' : 'nullable',
            'allow_user_ai_api_key' => 'boolean',
            'allowed_builders' => 'nullable|array',
            'allowed_builders.*' => 'exists:app_builders,id',
        ]);

        // Handle unlimited builds (-1)
        $buildCredits = $this->unlimited_builds ? -1 : $this->monthly_build_credits;

        // Handle unlimited Appetize minutes (-1)
        $appetizeMinutes = $this->can_use_appetize
            ? ($this->unlimited_appetize ? -1 : $this->monthly_appetize_minutes)
            : null;

        // Handle hosted pages (null = unlimited, number = specific limit)
        $maxHostedPages = $this->can_use_hosted_pages
            ? ($this->unlimited_hosted_pages ? null : $this->max_hosted_pages_per_app)
            : null;

        // Handle AI tokens (-1 = unlimited)
        $aiTokens = $this->can_use_ai_chat
            ? ($this->unlimited_ai_tokens ? -1 : $this->monthly_ai_tokens)
            : 0;

        // Handle allowed builders (null = all builders, array = specific builders)
        $allowedBuilders = $this->allow_all_builders ? null : $this->allowed_builders;

        if ($this->planId) {
            // Update existing plan
            $plan = Plan::findOrFail($this->planId);
            $plan->update([
                'name' => $this->name,
                'price' => $this->price,
                'status' => $this->status,
                'features' => $this->features,
                'monthly_build_credits' => $buildCredits,
                'can_use_custom_code' => $this->can_use_custom_code,
                'can_use_appetize' => $this->can_use_appetize,
                'monthly_appetize_minutes' => $appetizeMinutes,
                'can_use_api' => $this->can_use_api,
                'can_use_hosted_pages' => $this->can_use_hosted_pages,
                'max_hosted_pages_per_app' => $maxHostedPages,
                'can_use_ai_chat' => $this->can_use_ai_chat,
                'monthly_ai_tokens' => $aiTokens,
                'allow_user_ai_api_key' => $this->allow_user_ai_api_key,
                'allowed_builders' => $allowedBuilders,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Plan updated successfully!'
            );
        } else {
            // Create new plan
            Plan::create([
                'name' => $this->name,
                'price' => $this->price,
                'status' => $this->status,
                'features' => $this->features,
                'monthly_build_credits' => $buildCredits,
                'can_use_custom_code' => $this->can_use_custom_code,
                'can_use_appetize' => $this->can_use_appetize,
                'monthly_appetize_minutes' => $appetizeMinutes,
                'can_use_api' => $this->can_use_api,
                'can_use_hosted_pages' => $this->can_use_hosted_pages,
                'max_hosted_pages_per_app' => $maxHostedPages,
                'can_use_ai_chat' => $this->can_use_ai_chat,
                'monthly_ai_tokens' => $aiTokens,
                'allow_user_ai_api_key' => $this->allow_user_ai_api_key,
                'allowed_builders' => $allowedBuilders,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Plan created successfully!'
            );
        }

        $this->closeModal();
        $this->dispatch('planUpdated');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->planId = null;
        $this->name = '';
        $this->price = '';
        $this->status = 'active';
        $this->features = [];
        $this->monthly_build_credits = 10;
        $this->unlimited_builds = false;
        $this->can_use_custom_code = false;
        $this->can_use_appetize = false;
        $this->monthly_appetize_minutes = 0;
        $this->unlimited_appetize = false;
        $this->can_use_api = false;
        $this->can_use_hosted_pages = false;
        $this->max_hosted_pages_per_app = 5;
        $this->unlimited_hosted_pages = false;
        $this->can_use_ai_chat = false;
        $this->monthly_ai_tokens = 0;
        $this->unlimited_ai_tokens = false;
        $this->allow_user_ai_api_key = false;
        $this->allowed_builders = [];
        $this->allow_all_builders = true;
        $this->featureName = '';
        $this->featureIncluded = true;
        $this->resetValidation();
    }

    public function getAvailableBuildersProperty()
    {
        return \App\Models\AppBuilder::orderBy('name')->get();
    }

    public function getAiEnabledProperty()
    {
        return (bool) SystemSetting::get('ai_chat_enabled', false);
    }

    public function render()
    {
        return view('livewire.admin.plan-form');
    }
}
