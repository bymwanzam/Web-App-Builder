<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppDesignSettings extends Component
{
    public App $app;

    // Form fields
    public $theme_color;

    public $enable_dynamic_colors;

    public $text_color_light;

    public $text_color_dark;

    public $layout_template;

    public $layout_style;

    public $progress_indicator;

    public $progress_indicator_color;

    public $show_app_bar_title;

    public $nav_tab_icon_color_light;

    public $nav_tab_icon_color_dark;

    public $active_tab_color_light;

    public $active_tab_color_dark;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        // Load current design settings from config
        $this->theme_color = $config->theme_color ?? '#21759B';
        $this->enable_dynamic_colors = $config->enable_dynamic_colors ?? false;
        $this->text_color_light = $config->text_color_light ?? '#000000';
        $this->text_color_dark = $config->text_color_dark ?? '#FFFFFF';
        $this->layout_template = $config->layout_template ?? 'app_bar_drawer';
        $this->layout_style = $config->layout_style ?? 'list';
        $this->progress_indicator = $config->progress_indicator ?? 'linear';
        $this->progress_indicator_color = $config->progress_indicator_color ?? $config->theme_color;
        $this->show_app_bar_title = $config->show_app_bar_title ?? true;
        $this->nav_tab_icon_color_light = $config->nav_tab_icon_color_light ?? '#6B7280';
        $this->nav_tab_icon_color_dark = $config->nav_tab_icon_color_dark ?? '#6B7280';
        $this->active_tab_color_light = $config->active_tab_color_light ?? $config->theme_color;
        $this->active_tab_color_dark = $config->active_tab_color_dark ?? $config->theme_color;
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
            'theme_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color_light' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color_dark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'enable_dynamic_colors' => ['boolean'],
            'layout_template' => ['required', 'in:app_bar_drawer,app_bar_tabs,app_bar,list'],
            'layout_style' => ['required', 'in:list,grid,magazine'],
            'progress_indicator' => ['required', 'in:disable,linear,circular'],
            'progress_indicator_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'show_app_bar_title' => ['boolean'],
            'nav_tab_icon_color_light' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'nav_tab_icon_color_dark' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'active_tab_color_light' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'active_tab_color_dark' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function clearColor($field)
    {
        $this->$field = null;
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $config = $this->app->androidWordPressConfig;
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Design settings saved successfully!'
            );

            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-design-settings');
    }
}
