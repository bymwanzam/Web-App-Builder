<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use App\Rules\VersionNameRule;
use Livewire\Attributes\On;
use Livewire\Component;

class BuildModal extends Component
{
    public App $app;

    public $showModal = false;

    public $platform = 'android';

    public $buildType = 'debug';

    public $buildFormat = 'apk';

    public $versionName = null;

    public $selectedKeystoreId = null;

    public $selectedBuilderId = null;

    protected function rules()
    {
        return [
            'platform' => 'required|string|max:50', // Support any platform plugin identifier
            'buildType' => 'required|in:debug,release',
            'buildFormat' => 'nullable|in:apk,aab',
            'versionName' => ['required', 'string', 'max:20', new VersionNameRule],
            'selectedKeystoreId' => 'nullable|exists:keystores,id',
        ];
    }

    public function mount(App $app)
    {
        $this->app = $app;
    }

    #[On('openBuildModal')]
    public function openModal()
    {
        $this->reset(['platform', 'buildType', 'buildFormat', 'versionName', 'selectedKeystoreId', 'selectedBuilderId']);

        // Use platform plugin's identifier instead of hardcoded value
        $platformPlugin = $this->app->getPlatformInstance();
        $this->platform = $platformPlugin ? $platformPlugin->getPlatformId() : 'android-webview';

        $this->buildType = 'debug';
        $this->buildFormat = 'apk';

        // Pre-fill with auto-incremented version name for current platform
        $this->versionName = $this->app->incrementVersionName($this->platform);

        // Set selected keys to app's default keys
        $config = $this->app->getConfig();
        $this->selectedKeystoreId = $config?->keystore_id;

        // If no keystore is set in config, auto-select the first available one
        if (! $this->selectedKeystoreId && $this->availableKeystores->count() > 0) {
            $this->selectedKeystoreId = $this->availableKeystores->first()->id;
        }

        // Reset builder selection (null = auto-select)
        $this->selectedBuilderId = null;

        // Check if any compatible builders exist
        if ($this->availableBuilders->count() === 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No builders available for the '.$this->platform.' platform. Please contact support or upgrade your plan.'
            );

            return; // Don't open modal
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['platform', 'buildType', 'buildFormat', 'versionName', 'selectedKeystoreId', 'selectedBuilderId']);
    }

    /**
     * Update version name when platform changes.
     */
    public function updatedPlatform()
    {
        // Update version name to the incremented version for the new platform
        $this->versionName = $this->app->incrementVersionName($this->platform);
    }

    /**
     * Auto-select first keystore when switching to release build type.
     */
    public function updatedBuildType($value)
    {
        if ($value === 'release') {
            // Auto-select first keystore if none is selected
            if (! $this->selectedKeystoreId && $this->availableKeystores->count() > 0) {
                $this->selectedKeystoreId = $this->availableKeystores->first()->id;
            }
        }
    }

    public function createBuild()
    {
        $this->validate();

        $user = auth()->user();

        // Check if user has a plan (required for builds)
        if (! $user->plan) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'You need an active subscription to build apps. Please subscribe to a plan.'
            );
            $this->closeModal();

            return;
        }

        // Get available builders for user's plan
        if ($this->availableBuilders->count() === 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No builders available for your plan. Please contact support or upgrade your plan.'
            );
            $this->closeModal();

            return;
        }

        // Determine which builder to use and get its credit cost
        $builder = null;
        $creditCost = 1;

        if ($this->selectedBuilderId) {
            $builder = \App\Models\AppBuilder::find($this->selectedBuilderId);

            // Verify builder is allowed for user's plan
            if (! $builder || ! $user->plan->allowsBuilder($builder->id)) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Selected builder is not available for your plan.'
                );
                $this->closeModal();

                return;
            }

            // Verify builder is active
            if ($builder->status !== 'active') {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Selected builder is currently unavailable.'
                );
                $this->closeModal();

                return;
            }

            // Verify builder supports app platform
            if (! $builder->supportsPlatform($this->platform)) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: "Selected builder does not support the {$this->platform} platform."
                );
                $this->closeModal();

                return;
            }

            $creditCost = $builder->credit_cost ?? 1;
        }

        // Check build credits with variable cost
        if (! $user->hasBuildCredits($this->platform, $creditCost)) {
            $this->dispatch('show-toast',
                type: 'error',
                message: "Insufficient build credits. This build requires {$creditCost} credit(s). Please upgrade your plan or wait for monthly reset."
            );
            $this->closeModal();

            return;
        }

        // For release builds, check signing credentials
        if ($this->buildType === 'release') {
            if ($this->platform === 'android-webview' && ! $this->selectedKeystoreId) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Android keystore is required for release builds. Please select a keystore.'
                );
                $this->closeModal();

                return;
            }

            // Verify selected keystore belongs to the user
            if ($this->platform === 'android-webview') {
                $keystore = \App\Models\Keystore::where('id', $this->selectedKeystoreId)
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $keystore) {
                    $this->dispatch('show-toast',
                        type: 'error',
                        message: 'Invalid keystore selected.'
                    );
                    $this->closeModal();

                    return;
                }
            }
        }

        // Check for existing in-progress build
        $existingBuild = $this->app->builds()
            ->where('platform', $this->platform)
            ->whereIn('status', ['pending', 'preparing', 'building_android', 'processing'])
            ->first();

        if ($existingBuild) {
            $this->dispatch('show-toast',
                type: 'error',
                message: "A {$this->platform} build is already in progress. Please wait for it to complete."
            );
            $this->closeModal();

            return;
        }

        try {
            // Use user-provided version name and increment version code
            $versionName = $this->versionName;
            $versionCode = $this->app->incrementVersionCode();

            // Create build record (unassigned - server will claim it via API)
            $build = $this->app->builds()->create([
                'platform' => $this->platform,
                'build_type' => $this->buildType,
                'build_format' => $this->buildFormat,
                'version_name' => $versionName,
                'version_code' => $versionCode,
                'status' => 'pending',
                'keystore_id' => $this->buildType === 'release' && $this->platform === 'android-webview' ? $this->selectedKeystoreId : null,
                'expires_at' => now()->addDays(30), // Build artifacts expire in 30 days
            ]);

            // Update app's version name (universal field)
            $this->app->version_name = $versionName;
            $this->app->save();

            // Deduct variable build credits
            $user->deductBuildCredits($this->platform, $creditCost);

            // Auto-select optimal builder if not already selected
            if (! $builder) {
                $allowedBuilderIds = $user->plan->allowed_builders;
                $builder = \App\Models\AppBuilder::selectOptimalServer($this->platform, $allowedBuilderIds);

                if (! $builder) {
                    // Build queued but no builder available
                    $creditsRemaining = $user->fresh()->remaining_credits;
                    $this->dispatch('show-toast',
                        type: 'warning',
                        message: ucfirst($this->platform)." build queued successfully! Your build will be processed when a builder becomes available. ({$creditsRemaining} credits remaining)"
                    );
                    $this->closeModal();

                    return;
                }

                $creditCost = $builder->credit_cost ?? 1;
            }

            // Dispatch event to refresh builds table
            $this->dispatch('buildUpdated');

            // Show success message
            $creditsRemaining = $user->fresh()->remaining_credits;
            $builderName = $builder ? $builder->name : 'available builder';
            $this->dispatch('show-toast',
                type: 'success',
                message: ucfirst($this->platform)." build queued successfully on {$builderName}! ({$creditsRemaining} credits remaining)"
            );

            $this->closeModal();
        } catch (\Exception $e) {
            // Show error message
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to create build: '.$e->getMessage()
            );
            $this->closeModal();
        }
    }

    public function getBuildCreditCost()
    {
        // If user has selected a specific builder, return its cost
        if ($this->selectedBuilderId) {
            $builder = \App\Models\AppBuilder::find($this->selectedBuilderId);

            return $builder ? ($builder->credit_cost ?? 1) : 1;
        }

        // Otherwise, return cost of cheapest available builder
        $minCost = $this->availableBuilders->min('credit_cost');

        return $minCost ?? 1;
    }

    /**
     * Check if signing keys are missing for release build.
     */
    public function getShowSigningWarningProperty()
    {
        if ($this->buildType !== 'release') {
            return false;
        }

        if ($this->platform === 'android-webview' && ! $this->selectedKeystoreId) {
            return true;
        }

        return false;
    }

    /**
     * Get the signing key warning message.
     */
    public function getSigningWarningMessageProperty()
    {
        return count($this->availableKeystores) === 0
            ? 'No Android keystores available. Please upload a keystore in the Signing section.'
            : 'Please select an Android keystore for this release build.';
    }

    /**
     * Check if the form can be submitted.
     */
    public function getCanSubmitProperty()
    {
        // Block if no builders available
        if ($this->availableBuilders->count() === 0) {
            return false;
        }

        // Block submission if release build requires signing keys
        if ($this->buildType === 'release') {
            if ($this->platform === 'android-webview' && ! $this->selectedKeystoreId) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the next version code for the build.
     */
    public function getNextVersionCodeProperty()
    {
        return $this->app->version_code + 1;
    }

    /**
     * Get available keystores for the current user.
     */
    public function getAvailableKeystoresProperty()
    {
        return \App\Models\Keystore::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    /**
     * Get builders available for current user's plan.
     * Filters by app platform to only show compatible builders.
     */
    public function getAvailableBuildersProperty()
    {
        $user = auth()->user();

        if (! $user->plan) {
            return collect();
        }

        // Filter builders by app platform
        return $user->plan->availableBuilders->filter(function ($builder) {
            return $builder->supportsPlatform($this->platform);
        });
    }

    /**
     * Check if user can select builder.
     */
    public function getCanSelectBuilderProperty()
    {
        return $this->availableBuilders->count() > 1;
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.build-modal', [
            'user' => auth()->user(),
            'creditCost' => $this->getBuildCreditCost(),
        ]);
    }
}
