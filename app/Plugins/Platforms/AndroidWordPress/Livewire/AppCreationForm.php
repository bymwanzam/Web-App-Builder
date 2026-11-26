<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\AndroidWordPressConfig;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class AppCreationForm extends Component
{
    // Platform-specific form fields
    public $site_url = '';

    public $package_name = '';

    public $auto_generate_package = true;

    public $package_available = null;

    public $theme_color = '#21759B'; // WordPress blue

    public $text_color_light = '#1E1E1E';

    public $text_color_dark = '#FFFFFF';

    public $layout_template = 'app_bar_tabs';

    protected function rules()
    {
        return [
            'site_url' => ['required', 'url', 'max:255', 'regex:/wordpress\.com/i'],
            'package_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*)+$/i',
                Rule::unique('android_wordpress_configs', 'package_name'),
            ],
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color_light' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color_dark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'layout_template' => ['required', 'in:list,app_bar,app_bar_tabs,app_bar_drawer'],
        ];
    }

    protected $messages = [
        'site_url.required' => 'WordPress site URL is required',
        'site_url.url' => 'WordPress site URL must be a valid URL',
        'site_url.regex' => 'Site URL must be a WordPress.com site (e.g., myblog.wordpress.com)',
        'package_name.required' => 'APP ID is required',
        'package_name.regex' => 'APP ID must be in reverse domain format (e.g., com.example.app)',
        'package_name.unique' => 'This APP ID is already in use by another app',
        'theme_color.regex' => 'Theme color must be a valid hex color (e.g., #21759B)',
    ];

    #[On('validate-platform-config')]
    public function validateAndEmit()
    {
        $validated = $this->validate();

        // Emit validated data back to parent AppForm
        $this->dispatch('platform-config-validated', $validated);
    }

    #[On('app-name-updated')]
    public function handleAppNameUpdate($name)
    {
        // Auto-generate package name when app name changes (if auto-generate is enabled)
        if ($this->auto_generate_package) {
            $this->package_name = $this->generatePackageName($name);
            $this->checkPackageAvailability();
        }
    }

    public function updatedAutoGeneratePackage($value)
    {
        if ($value) {
            // Get app name from parent component via event
            $this->dispatch('request-app-name');
        } else {
            // Clear the package name when manual mode is enabled
            if (empty($this->package_name)) {
                $this->package_name = '';
            }
        }
    }

    public function updatedPackageName($value)
    {
        // Disable auto-generate when user manually types
        if (! $this->auto_generate_package) {
            $this->checkPackageAvailability();
        }
    }

    /**
     * Generate package name from app name.
     */
    protected function generatePackageName(string $appName): string
    {
        // Convert to lowercase and remove all non-alphanumeric
        $slug = strtolower($appName);
        $slug = preg_replace('/[^a-z0-9]/', '', $slug);

        // Ensure it starts with a letter
        if (! preg_match('/^[a-z]/', $slug)) {
            $slug = 'app'.$slug;
        }

        // Default to simple slug if empty
        if (empty($slug)) {
            $slug = 'app';
        }

        // Generate package name with uniqueness check
        $packageName = "com.{$slug}.wordpress";
        $counter = 1;

        while (AndroidWordPressConfig::where('package_name', $packageName)->exists()) {
            $counter++;
            $packageName = "com.{$slug}{$counter}.wordpress";
        }

        return $packageName;
    }

    /**
     * Check if package name is available.
     */
    protected function checkPackageAvailability()
    {
        if (empty($this->package_name)) {
            $this->package_available = null;

            return;
        }

        // Check format first
        if (! preg_match('/^[a-z][a-z0-9_]*(\.[a-z][a-z0-9_]*)+$/i', $this->package_name)) {
            $this->package_available = false;

            return;
        }

        // Check uniqueness
        $exists = AndroidWordPressConfig::where('package_name', $this->package_name)->exists();
        $this->package_available = ! $exists;
    }

    public function mount()
    {
        // Initialize with a default package name
        $this->package_name = 'com.example.wordpress';
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-creation-form');
    }
}
