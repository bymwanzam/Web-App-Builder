<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppContentSettings extends Component
{
    public App $app;

    // Content display options
    public $posts_per_page;

    public $show_author;

    public $show_date;

    public $show_excerpt;

    public $show_featured_image;

    public $show_categories;

    public $show_reading_time;

    public $excerpt_length;

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        $this->posts_per_page = $config->posts_per_page ?? 10;
        $this->show_author = $config->show_author ?? true;
        $this->show_date = $config->show_date ?? true;
        $this->show_excerpt = $config->show_excerpt ?? true;
        $this->show_featured_image = $config->show_featured_image ?? true;
        $this->show_categories = $config->show_categories ?? true;
        $this->show_reading_time = $config->show_reading_time ?? false;
        $this->excerpt_length = $config->excerpt_length ?? 150;
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
            'posts_per_page' => ['required', 'integer', 'min:1', 'max:50'],
            'show_author' => ['boolean'],
            'show_date' => ['boolean'],
            'show_excerpt' => ['boolean'],
            'show_featured_image' => ['boolean'],
            'show_categories' => ['boolean'],
            'show_reading_time' => ['boolean'],
            'excerpt_length' => ['required', 'integer', 'min:50', 'max:500'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        try {
            $config = $this->app->androidWordPressConfig;
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Content settings saved successfully!'
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
        return view('plugins.android-wordpress::livewire.app-content-settings');
    }
}
