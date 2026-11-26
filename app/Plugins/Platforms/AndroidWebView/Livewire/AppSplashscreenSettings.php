<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AppSplashscreenSettings extends Component
{
    use WithFileUploads;

    public App $app;

    public $splashscreen_background_type;

    public $splashscreen_background;

    public $splashscreen_image;

    public $splashscreen_image_file;

    public $splashscreen_duration;

    public $splashscreen_animation;

    public $splashscreen_logo_enabled;

    public $splashscreen_logo;

    public $splashscreen_logo_file;

    public $splashscreen_title;

    public $splashscreen_subtitle;

    public $splashscreen_text_theme;

    public $splashscreen_logo_size;

    public function mount()
    {
        $config = $this->app->androidWebViewConfig;

        $this->splashscreen_background_type = $config->splashscreen_background_type ?? 'color';
        // Inherit theme_color for consistent branding (previously fell back to white)
        $this->splashscreen_background = $config->splashscreen_background ?? $config->theme_color;
        $this->splashscreen_image = $config->splashscreen_image;
        $this->splashscreen_duration = $config->splashscreen_duration ?? 3;
        $this->splashscreen_animation = $config->splashscreen_animation ?? 'fade';
        $this->splashscreen_logo_enabled = $config->splashscreen_logo_enabled ?? false;
        $this->splashscreen_logo = $config->splashscreen_logo;
        $this->splashscreen_logo_size = $config->splashscreen_logo_size ?? 120;
        $this->splashscreen_title = $config->splashscreen_title;
        $this->splashscreen_subtitle = $config->splashscreen_subtitle;
        $this->splashscreen_text_theme = $config->splashscreen_text_theme ?? 'dark';

        // Validate storage link exists
        if (! $this->isStorageLinked()) {
            $this->dispatch('show-toast',
                type: 'warning',
                message: 'Storage link not found. Run: php artisan storage:link'
            );
        }
    }

    #[On('app-config-updated')]
    public function refreshFromAi(int $appId): void
    {
        if ($this->app->id === $appId) {
            $this->app->refresh();
            $this->mount();
        }
    }

    /**
     * Check if public storage symlink exists
     */
    protected function isStorageLinked()
    {
        return is_link(public_path('storage'));
    }

    protected function rules()
    {
        return [
            'splashscreen_background_type' => ['required', 'in:color,image'],
            'splashscreen_background' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'splashscreen_image_file' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'splashscreen_duration' => ['required', 'integer', 'min:1', 'max:10'],
            'splashscreen_animation' => ['required', 'in:fade,slide,zoom,none'],
            'splashscreen_logo_enabled' => ['required', 'boolean'],
            'splashscreen_logo_file' => ['nullable', 'image', 'mimes:png', 'max:2048'],
            'splashscreen_logo_size' => ['required', 'integer', 'min:60', 'max:240'],
            'splashscreen_title' => ['nullable', 'string', 'max:255'],
            'splashscreen_subtitle' => ['nullable', 'string', 'max:255'],
            'splashscreen_text_theme' => ['required', 'in:light,dark'],
        ];
    }

    protected function messages()
    {
        return [
            'splashscreen_image_file.mimes' => 'The splashscreen background image must be a PNG file.',
            'splashscreen_logo_file.mimes' => 'The splashscreen logo must be a PNG file.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'splashscreen_background_type' => $this->splashscreen_background_type,
                'splashscreen_background' => $this->splashscreen_background,
                'splashscreen_duration' => $this->splashscreen_duration,
                'splashscreen_animation' => $this->splashscreen_animation,
                'splashscreen_logo_enabled' => $this->splashscreen_logo_enabled,
                'splashscreen_logo_size' => $this->splashscreen_logo_size,
                'splashscreen_title' => $this->splashscreen_title,
                'splashscreen_subtitle' => $this->splashscreen_subtitle,
                'splashscreen_text_theme' => $this->splashscreen_text_theme,
            ];

            // Handle background image upload
            if ($this->splashscreen_image_file) {
                $path = $this->splashscreen_image_file->store('splashscreens/'.$this->app->app_id, 'public');
                $data['splashscreen_image'] = $path;
                $this->splashscreen_image = $path;
            }

            // Handle logo upload
            if ($this->splashscreen_logo_file) {
                $path = $this->splashscreen_logo_file->store('splashscreens/'.$this->app->app_id.'/logos', 'public');
                $data['splashscreen_logo'] = $path;
                $this->splashscreen_logo = $path;
            }

            $config = $this->app->androidWebViewConfig;
            $config->update($data);

            $this->reset(['splashscreen_image_file', 'splashscreen_logo_file']);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Splashscreen settings saved successfully!'
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
        // Refresh the app model from database
        $this->app = $this->app->fresh();

        $config = $this->app->androidWebViewConfig;

        // Refresh component properties from fresh config data
        $this->splashscreen_image = $config->splashscreen_image;
        $this->splashscreen_logo = $config->splashscreen_logo;
        $this->splashscreen_logo_size = $config->splashscreen_logo_size ?? 120;

        // Clear file upload state
        $this->reset(['splashscreen_image_file', 'splashscreen_logo_file']);
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-splashscreen-settings');
    }
}
