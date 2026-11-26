<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use App\Rules\VersionNameRule;
use Livewire\Attributes\On;
use Livewire\Component;

class BuildModal extends Component
{
    public App $app;

    public $showModal = false;

    public $platform = 'android-wordpress';

    public $buildType = 'debug';

    public $buildFormat = 'apk';

    public $versionName = null;

    public $selectedKeystoreId = null;

    public $selectedBuilderId = null;

    protected function rules()
    {
        return [
            'platform' => 'required|string|max:50',
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

        // Use platform plugin's identifier
        $platformPlugin = $this->app->getPlatformInstance();
        $this->platform = $platformPlugin ? $platformPlugin->getPlatformId() : 'android-wordpress';

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

            return;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['platform', 'buildType', 'buildFormat', 'versionName', 'selectedKeystoreId', 'selectedBuilderId']);
    }

    public function updatedPlatform()
    {
        $this->versionName = $this->app->incrementVersionName($this->platform);
    }

    public function updatedBuildType($value)
    {
        if ($value === 'release') {
            if (! $this->selectedKeystoreId && $this->availableKeystores->count() > 0) {
                $this->selectedKeystoreId = $this->availableKeystores->first()->id;
            }
        }
    }

    public function createBuild()
    {
        $this->validate();

        $user = auth()->user();

        if (! $user->plan) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'You need an active subscription to build apps. Please subscribe to a plan.'
            );
            $this->closeModal();

            return;
        }

        if ($this->availableBuilders->count() === 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No builders available for your plan. Please contact support or upgrade your plan.'
            );
            $this->closeModal();

            return;
        }

        $builder = null;
        $creditCost = 1;

        if ($this->selectedBuilderId) {
            $builder = \App\Models\AppBuilder::find($this->selectedBuilderId);

            if (! $builder || ! $user->plan->allowsBuilder($builder->id)) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Selected builder is not available for your plan.'
                );
                $this->closeModal();

                return;
            }

            if ($builder->status !== 'active') {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Selected builder is currently unavailable.'
                );
                $this->closeModal();

                return;
            }

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

        if (! $user->hasBuildCredits($this->platform, $creditCost)) {
            $this->dispatch('show-toast',
                type: 'error',
                message: "Insufficient build credits. This build requires {$creditCost} credit(s). Please upgrade your plan or wait for monthly reset."
            );
            $this->closeModal();

            return;
        }

        if ($this->buildType === 'release') {
            if (! $this->selectedKeystoreId) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Android keystore is required for release builds. Please select a keystore.'
                );
                $this->closeModal();

                return;
            }

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
            $versionName = $this->versionName;
            $versionCode = $this->app->incrementVersionCode();

            $build = $this->app->builds()->create([
                'platform' => $this->platform,
                'build_type' => $this->buildType,
                'build_format' => $this->buildFormat,
                'version_name' => $versionName,
                'version_code' => $versionCode,
                'status' => 'pending',
                'keystore_id' => $this->buildType === 'release' ? $this->selectedKeystoreId : null,
                'expires_at' => now()->addDays(30),
            ]);

            $this->app->version_name = $versionName;
            $this->app->save();

            $user->deductBuildCredits($this->platform, $creditCost);

            if (! $builder) {
                $allowedBuilderIds = $user->plan->allowed_builders;
                $builder = \App\Models\AppBuilder::selectOptimalServer($this->platform, $allowedBuilderIds);

                if (! $builder) {
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

            $this->dispatch('buildUpdated');

            $creditsRemaining = $user->fresh()->remaining_credits;
            $builderName = $builder ? $builder->name : 'available builder';
            $this->dispatch('show-toast',
                type: 'success',
                message: ucfirst($this->platform)." build queued successfully on {$builderName}! ({$creditsRemaining} credits remaining)"
            );

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to create build: '.$e->getMessage()
            );
            $this->closeModal();
        }
    }

    public function getBuildCreditCost()
    {
        if ($this->selectedBuilderId) {
            $builder = \App\Models\AppBuilder::find($this->selectedBuilderId);

            return $builder ? ($builder->credit_cost ?? 1) : 1;
        }

        $minCost = $this->availableBuilders->min('credit_cost');

        return $minCost ?? 1;
    }

    public function getShowSigningWarningProperty()
    {
        if ($this->buildType !== 'release') {
            return false;
        }

        return ! $this->selectedKeystoreId;
    }

    public function getSigningWarningMessageProperty()
    {
        return count($this->availableKeystores) === 0
            ? 'No Android keystores available. Please upload a keystore in the Signing section.'
            : 'Please select an Android keystore for this release build.';
    }

    public function getCanSubmitProperty()
    {
        if ($this->availableBuilders->count() === 0) {
            return false;
        }

        if ($this->buildType === 'release' && ! $this->selectedKeystoreId) {
            return false;
        }

        return true;
    }

    public function getNextVersionCodeProperty()
    {
        return $this->app->version_code + 1;
    }

    public function getAvailableKeystoresProperty()
    {
        return \App\Models\Keystore::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
    }

    public function getAvailableBuildersProperty()
    {
        $user = auth()->user();

        if (! $user->plan) {
            return collect();
        }

        return $user->plan->availableBuilders->filter(function ($builder) {
            return $builder->supportsPlatform($this->platform);
        });
    }

    public function getCanSelectBuilderProperty()
    {
        return $this->availableBuilders->count() > 1;
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.build-modal', [
            'user' => auth()->user(),
            'creditCost' => $this->getBuildCreditCost(),
        ]);
    }
}
