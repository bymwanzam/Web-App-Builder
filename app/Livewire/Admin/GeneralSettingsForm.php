<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class GeneralSettingsForm extends Component
{
    use WithFileUploads;

    public $site_name;

    public $site_description;

    public $site_tagline;

    public $purchase_code;

    public $logo;

    public $logo_dark;

    public $favicon;

    public $current_logo;

    public $current_logo_dark;

    public $current_favicon;

    public $default_theme;

    public $landing_page_enabled;

    public function mount()
    {
        $this->site_name = SystemSetting::get('site_name', 'Appy');
        $this->site_description = SystemSetting::get('site_description', '');
        $this->site_tagline = SystemSetting::get('site_tagline', 'Build native apps');
        $this->purchase_code = SystemSetting::get('purchase_code');
        $this->current_logo = SystemSetting::get('logo_path');
        $this->current_logo_dark = SystemSetting::get('logo_dark_path');
        $this->current_favicon = SystemSetting::get('favicon_path');
        $this->default_theme = SystemSetting::get('default_theme', 'system');
        $this->landing_page_enabled = SystemSetting::get('landing_page_enabled', true);

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->purchase_code = '';
        }
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

        $this->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_tagline' => 'nullable|string|max:100',
            'purchase_code' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'logo_dark' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,svg,ico|max:1024',
            'default_theme' => 'required|in:light,dark,system',
            'landing_page_enabled' => 'boolean',
        ]);

        // Save text settings
        SystemSetting::set('site_name', $this->site_name, 'string', 'general');
        SystemSetting::set('site_description', $this->site_description, 'string', 'general');
        SystemSetting::set('site_tagline', $this->site_tagline, 'string', 'general');
        SystemSetting::set('purchase_code', $this->purchase_code, 'string', 'general');
        SystemSetting::set('default_theme', $this->default_theme, 'string', 'general');
        SystemSetting::set('landing_page_enabled', $this->landing_page_enabled, 'boolean', 'general');

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo
            if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
                Storage::disk('public')->delete($this->current_logo);
            }
            // Store new logo
            $logoPath = $this->logo->store('system', 'public');
            SystemSetting::set('logo_path', $logoPath, 'string', 'general');
            $this->current_logo = $logoPath;
            $this->logo = null;
        }

        // Handle dark logo upload
        if ($this->logo_dark) {
            // Delete old dark logo
            if ($this->current_logo_dark && Storage::disk('public')->exists($this->current_logo_dark)) {
                Storage::disk('public')->delete($this->current_logo_dark);
            }
            // Store new dark logo
            $logoDarkPath = $this->logo_dark->store('system', 'public');
            SystemSetting::set('logo_dark_path', $logoDarkPath, 'string', 'general');
            $this->current_logo_dark = $logoDarkPath;
            $this->logo_dark = null;
        }

        // Handle favicon upload
        if ($this->favicon) {
            // Delete old favicon
            if ($this->current_favicon && Storage::disk('public')->exists($this->current_favicon)) {
                Storage::disk('public')->delete($this->current_favicon);
            }
            // Store new favicon
            $faviconPath = $this->favicon->store('system', 'public');
            SystemSetting::set('favicon_path', $faviconPath, 'string', 'general');
            $this->current_favicon = $faviconPath;
            $this->favicon = null;
        }

        $this->dispatch('show-toast',
            type: 'success',
            message: 'General settings updated successfully!'
        );
    }

    public function removeLogo()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        if ($this->current_logo && Storage::disk('public')->exists($this->current_logo)) {
            Storage::disk('public')->delete($this->current_logo);
        }
        SystemSetting::set('logo_path', null, 'string', 'general');
        $this->current_logo = null;
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Logo removed successfully!'
        );
    }

    public function removeDarkLogo()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        if ($this->current_logo_dark && Storage::disk('public')->exists($this->current_logo_dark)) {
            Storage::disk('public')->delete($this->current_logo_dark);
        }
        SystemSetting::set('logo_dark_path', null, 'string', 'general');
        $this->current_logo_dark = null;
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Dark logo removed successfully!'
        );
    }

    public function removeFavicon()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        if ($this->current_favicon && Storage::disk('public')->exists($this->current_favicon)) {
            Storage::disk('public')->delete($this->current_favicon);
        }
        SystemSetting::set('favicon_path', null, 'string', 'general');
        $this->current_favicon = null;
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Favicon removed successfully!'
        );
    }

    public function render()
    {
        return view('livewire.admin.general-settings-form');
    }
}
