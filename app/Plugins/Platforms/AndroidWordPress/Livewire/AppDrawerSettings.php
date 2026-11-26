<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppDrawerSettings extends Component
{
    use WithFileUploads;

    public App $app;

    public $drawer_mode;

    public $drawer_background_color;

    public $drawer_background_image;

    public $drawer_background_image_file;

    public $drawer_logo_enabled;

    public $drawer_logo_light;

    public $drawer_logo_light_file;

    public $drawer_logo_dark;

    public $drawer_logo_dark_file;

    public $drawer_title;

    public $drawer_subtitle;

    public $drawer_text_theme;

    public $drawer_logo_size;

    public $drawer_icon_color_light;

    public $drawer_icon_color_dark;

    public $drawer_active_color_light;

    public $drawer_active_color_dark;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        $this->drawer_mode = $config->drawer_mode ?? 'default';
        $this->drawer_background_color = $config->drawer_background_color;
        $this->drawer_background_image = $config->drawer_background_image;
        $this->drawer_logo_enabled = $config->drawer_logo_enabled ?? false;
        $this->drawer_logo_light = $config->drawer_logo_light;
        $this->drawer_logo_dark = $config->drawer_logo_dark;
        $this->drawer_logo_size = $config->drawer_logo_size ?? 80;
        $this->drawer_title = $config->drawer_title;
        $this->drawer_subtitle = $config->drawer_subtitle;
        $this->drawer_text_theme = $config->drawer_text_theme ?? 'dark';
        $this->drawer_icon_color_light = $config->drawer_icon_color_light ?? '#6B7280';
        $this->drawer_icon_color_dark = $config->drawer_icon_color_dark ?? '#9CA3AF';
        $this->drawer_active_color_light = $config->drawer_active_color_light ?? ($config->theme_color ?? '#21759B');
        $this->drawer_active_color_dark = $config->drawer_active_color_dark ?? ($config->theme_color ?? '#21759B');
    }

    #[On('app-config-updated')]
    public function refreshFromAi(int $appId): void
    {
        if ($this->app->id === $appId) {
            $this->app->refresh();
            $this->mount();
        }
    }

    protected function rules()
    {
        return [
            'drawer_mode' => ['required', 'in:default,color,image'],
            'drawer_background_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'drawer_background_image_file' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'drawer_logo_enabled' => ['required', 'boolean'],
            'drawer_logo_light_file' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'drawer_logo_dark_file' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'drawer_logo_size' => ['required', 'integer', 'min:40', 'max:200'],
            'drawer_title' => ['nullable', 'string', 'max:255'],
            'drawer_subtitle' => ['nullable', 'string', 'max:255'],
            'drawer_text_theme' => ['required', 'in:light,dark'],
            'drawer_icon_color_light' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'drawer_icon_color_dark' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'drawer_active_color_light' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'drawer_active_color_dark' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    protected function messages()
    {
        return [
            'drawer_background_image_file.mimes' => 'The drawer background image must be a PNG file.',
            'drawer_logo_light_file.mimes' => 'The light mode logo must be a PNG file.',
            'drawer_logo_dark_file.mimes' => 'The dark mode logo must be a PNG file.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'drawer_mode' => $this->drawer_mode,
                'drawer_background_color' => $this->drawer_background_color,
                'drawer_logo_enabled' => $this->drawer_logo_enabled,
                'drawer_logo_size' => $this->drawer_logo_size,
                'drawer_title' => $this->drawer_title,
                'drawer_subtitle' => $this->drawer_subtitle,
                'drawer_text_theme' => $this->drawer_text_theme,
                'drawer_icon_color_light' => $this->drawer_icon_color_light,
                'drawer_icon_color_dark' => $this->drawer_icon_color_dark,
                'drawer_active_color_light' => $this->drawer_active_color_light,
                'drawer_active_color_dark' => $this->drawer_active_color_dark,
            ];

            // Handle background image upload
            if ($this->drawer_background_image_file) {
                $path = $this->drawer_background_image_file->store('drawers/'.$this->app->id, 'public');
                $data['drawer_background_image'] = $path;
                $this->drawer_background_image = $path;
            }

            // Handle light mode logo upload
            if ($this->drawer_logo_light_file) {
                $path = $this->drawer_logo_light_file->store('drawers/'.$this->app->id.'/logos', 'public');
                $data['drawer_logo_light'] = $path;
                $this->drawer_logo_light = $path;
            }

            // Handle dark mode logo upload
            if ($this->drawer_logo_dark_file) {
                $path = $this->drawer_logo_dark_file->store('drawers/'.$this->app->id.'/logos', 'public');
                $data['drawer_logo_dark'] = $path;
                $this->drawer_logo_dark = $path;
            }

            $config = $this->app->androidWordPressConfig;
            $config->update($data);

            $this->reset(['drawer_background_image_file', 'drawer_logo_light_file', 'drawer_logo_dark_file']);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Drawer settings saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    #[On('settings-updated')]
    public function refreshApp()
    {
        $this->app->refresh();

        $config = $this->app->androidWordPressConfig;

        $this->reset(['drawer_background_image_file', 'drawer_logo_light_file', 'drawer_logo_dark_file']);

        $this->drawer_background_image = null;
        $this->drawer_logo_light = null;
        $this->drawer_logo_dark = null;
        $this->drawer_logo_size = null;

        $this->drawer_background_image = $config->drawer_background_image;
        $this->drawer_logo_light = $config->drawer_logo_light;
        $this->drawer_logo_dark = $config->drawer_logo_dark;
        $this->drawer_logo_size = $config->drawer_logo_size ?? 80;
        $this->drawer_icon_color_light = $config->drawer_icon_color_light ?? '#6B7280';
        $this->drawer_icon_color_dark = $config->drawer_icon_color_dark ?? '#9CA3AF';
        $this->drawer_active_color_light = $config->drawer_active_color_light ?? ($config->theme_color ?? '#21759B');
        $this->drawer_active_color_dark = $config->drawer_active_color_dark ?? ($config->theme_color ?? '#21759B');
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-drawer-settings');
    }
}
