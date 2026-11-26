<?php

namespace App\Livewire;

use App\Models\App;
use App\Models\Plugin;
use App\Services\PluginManager;
use Livewire\Attributes\On;
use Livewire\Component;

class AppForm extends Component
{
    public $showModal = false;

    public $appId = null;

    // Wizard step (1 = platform selection, 2 = configuration)
    public $step = 1;

    public $selectedPlatform = null;

    public $selectedPlatformName = null;

    public $availablePlatforms = [];

    // Universal field
    public $name = '';

    // Platform-specific config data from child component
    public $platformConfigData = [];

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function updatedName($value)
    {
        // Emit app name changes to platform-specific form components
        $this->dispatch('app-name-updated', name: $value);
    }

    #[On('request-app-name')]
    public function provideAppName()
    {
        // Respond to request for current app name
        $this->dispatch('app-name-updated', name: $this->name);
    }

    #[On('create-app')]
    public function create()
    {
        $this->reset(['appId', 'name', 'platformConfigData', 'selectedPlatform', 'selectedPlatformName']);
        $this->step = 1;
        $this->loadAvailablePlatforms();
        $this->showModal = true;
    }

    /**
     * Load available platform plugins.
     */
    public function loadAvailablePlatforms()
    {
        $pluginManager = app(PluginManager::class);
        $platforms = $pluginManager->getActivePlatforms();

        $this->availablePlatforms = $platforms->map(function ($platform) {
            return [
                'slug' => $platform->getPlatformId(),
                'name' => $platform->getName(),
                'icon' => $platform->getIcon(),
                'description' => $platform->getDescription(),
            ];
        })->toArray();
    }

    /**
     * Select a platform and proceed to step 2.
     */
    public function selectPlatform($slug)
    {
        $this->selectedPlatform = $slug;

        // Store the platform display name for UI
        $platform = collect($this->availablePlatforms)->firstWhere('slug', $slug);
        $this->selectedPlatformName = $platform['name'] ?? ucfirst($slug);

        $this->step = 2;

        // Load platform defaults
        $pluginManager = app(PluginManager::class);
        $platformInstance = $pluginManager->getPlatformBySlug($slug);

        if ($platformInstance) {
            $defaults = $platformInstance->getDefaultConfig();
            // Defaults are already set in create() method
        }
    }

    /**
     * Go back to platform selection.
     */
    public function previousStep()
    {
        $this->step = 1;
        $this->selectedPlatform = null;
        $this->selectedPlatformName = null;
    }

    #[On('edit-app')]
    public function edit($id)
    {
        $app = App::where('user_id', auth()->id())->findOrFail($id);

        $this->appId = $app->id;
        $this->name = $app->name;
        $this->step = 2; // Skip platform selection when editing
        $this->showModal = true;
    }

    public function save()
    {
        // Validate universal fields
        $this->validate();

        if ($this->appId) {
            // Edit mode: Update only universal fields (name)
            $app = App::where('user_id', auth()->id())->findOrFail($this->appId);
            $app->update(['name' => $this->name]);

            $this->showModal = false;
            $this->dispatch('appUpdated');
            $this->reset(['appId', 'name', 'platformConfigData', 'step', 'selectedPlatform', 'selectedPlatformName']);
        } else {
            // Create mode: Request platform component to validate
            // Platform component will emit 'platform-config-validated' event with validated data
            $this->dispatch('validate-platform-config');
        }
    }

    #[On('platform-config-validated')]
    public function handlePlatformConfigValidated($configData)
    {
        // Platform component has validated its data, now create the app
        if (! $this->selectedPlatform) {
            throw new \Exception('Platform must be selected');
        }

        // Find platform plugin
        $platformPlugin = Plugin::where('slug', $this->selectedPlatform)
            ->where('type', 'platform')
            ->where('status', 'active')
            ->first();

        if (! $platformPlugin) {
            throw new \Exception('Platform plugin not found or inactive');
        }

        // Get platform instance
        $platform = $platformPlugin->getInstance();

        // Create app with universal fields only
        $app = App::create([
            'user_id' => auth()->id(),
            'platform_plugin_id' => $platformPlugin->id,
            'name' => $this->name,
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        // Delegate platform-specific config creation to platform plugin
        $platform->createConfig($app, $configData);

        // Success! Redirect to app editor
        $this->showModal = false;
        $this->dispatch('appUpdated');
        $this->reset(['appId', 'name', 'platformConfigData', 'step', 'selectedPlatform', 'selectedPlatformName']);

        return redirect()->route('user.app', $app);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['appId', 'name', 'platformConfigData', 'step', 'selectedPlatform', 'selectedPlatformName']);
        $this->resetValidation();
    }

    /**
     * Get the selected platform instance.
     */
    public function getSelectedPlatformInstance()
    {
        if (! $this->selectedPlatform) {
            return null;
        }

        $pluginManager = app(PluginManager::class);

        return $pluginManager->getPlatformBySlug($this->selectedPlatform);
    }

    public function render()
    {
        return view('livewire.app-form');
    }
}
