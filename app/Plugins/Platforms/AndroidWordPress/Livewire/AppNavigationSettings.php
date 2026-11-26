<?php

namespace App\Plugins\Platforms\AndroidWordPress\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppNavigationSettings extends Component
{
    public App $app;

    public $navigation_items = [];

    public $app_bar_buttons = [];

    public $drawer_items = [];

    public $pull_to_refresh;

    public $swipe_navigation;

    public $preserve_tab_state;

    /**
     * Available icons for navigation items and app bar buttons.
     * WordPress-specific icons focused on content navigation.
     */
    public $availableIcons = [
        'home' => 'Home',
        'newspaper' => 'Articles',
        'tag' => 'Tag',
        'folder' => 'Category',
        'magnifying-glass' => 'Search',
        'bookmark' => 'Bookmarks',
        'heart' => 'Favorites',
        'user' => 'Author',
        'calendar' => 'Recent',
        'fire' => 'Popular',
        'globe' => 'Web',
        'info' => 'Info',
        'settings' => 'Settings',
        'share' => 'Share',
    ];

    public function mount()
    {
        $config = $this->app->androidWordPressConfig;

        $this->navigation_items = $config->navigation_items ?? [];
        $this->app_bar_buttons = $config->app_bar_buttons ?? [];
        $this->drawer_items = $config->drawer_items ?? [];
        $this->pull_to_refresh = $config->pull_to_refresh ?? true;
        $this->swipe_navigation = $config->swipe_navigation ?? true;
        $this->preserve_tab_state = $config->preserve_tab_state ?? false;

        // Ensure home item exists
        $this->ensureHomeItem();
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
     * Ensure home item exists (shows all posts)
     */
    private function ensureHomeItem()
    {
        $config = $this->app->androidWordPressConfig;

        // Handle navigation tabs (app_bar_tabs layout)
        if ($config->layout_template === 'app_bar_tabs') {
            $homeTabIndex = null;
            foreach ($this->navigation_items as $index => $item) {
                if (isset($item['is_home']) && $item['is_home']) {
                    $homeTabIndex = $index;
                    break;
                }
            }

            if ($homeTabIndex !== null) {
                if ($homeTabIndex !== 0) {
                    $homeTab = $this->navigation_items[$homeTabIndex];
                    unset($this->navigation_items[$homeTabIndex]);
                    $this->navigation_items = array_values($this->navigation_items);
                    array_unshift($this->navigation_items, $homeTab);
                }
                $this->navigation_items[0]['action_type'] = 'all_posts';
            } else {
                array_unshift($this->navigation_items, [
                    'label' => 'Home',
                    'action_type' => 'all_posts',
                    'value' => '',
                    'icon' => 'home',
                    'is_home' => true,
                ]);
            }
        }

        // Handle drawer items (app_bar_drawer layout)
        if ($config->layout_template === 'app_bar_drawer') {
            $homeItemIndex = null;
            foreach ($this->drawer_items as $index => $item) {
                if (isset($item['is_home']) && $item['is_home']) {
                    $homeItemIndex = $index;
                    break;
                }
            }

            if ($homeItemIndex !== null) {
                if ($homeItemIndex !== 0) {
                    $homeItem = $this->drawer_items[$homeItemIndex];
                    unset($this->drawer_items[$homeItemIndex]);
                    $this->drawer_items = array_values($this->drawer_items);
                    array_unshift($this->drawer_items, $homeItem);
                }
                $this->drawer_items[0]['action_type'] = 'all_posts';
            } else {
                array_unshift($this->drawer_items, [
                    'label' => 'Home',
                    'action_type' => 'all_posts',
                    'value' => '',
                    'icon' => 'home',
                    'is_home' => true,
                ]);
            }
        }
    }

    protected function rules()
    {
        $rules = [
            'navigation_items' => ['nullable', 'array', 'max:9'],
            'navigation_items.*.label' => ['required', 'string', 'max:50'],
            'navigation_items.*.action_type' => ['required', 'in:all_posts,category,tag,search,bookmarks,hosted_page,internal,external'],
            'navigation_items.*.icon' => ['required', 'string'],
            'navigation_items.*.is_home' => ['nullable', 'boolean'],
            'app_bar_buttons' => ['nullable', 'array', 'max:3'],
            'app_bar_buttons.*.label' => ['required', 'string', 'max:50'],
            'app_bar_buttons.*.action_type' => ['required', 'in:search,bookmarks,share,hosted_page,internal,external'],
            'app_bar_buttons.*.icon' => ['required', 'string'],
            'drawer_items' => ['nullable', 'array', 'max:10'],
            'drawer_items.*.label' => ['required', 'string', 'max:50'],
            'drawer_items.*.action_type' => ['required', 'in:all_posts,category,tag,search,bookmarks,hosted_page,internal,external'],
            'drawer_items.*.icon' => ['required', 'string'],
            'drawer_items.*.is_home' => ['nullable', 'boolean'],
            'pull_to_refresh' => ['required', 'boolean'],
            'swipe_navigation' => ['required', 'boolean'],
            'preserve_tab_state' => ['required', 'boolean'],
        ];

        // Dynamic validation for value field based on action_type
        foreach ($this->navigation_items as $index => $item) {
            $isHome = isset($item['is_home']) && $item['is_home'];
            if (! $isHome) {
                $actionType = $item['action_type'] ?? 'category';
                $rules["navigation_items.{$index}.value"] = $this->getValueRules($actionType);
            }
        }

        foreach ($this->app_bar_buttons as $index => $item) {
            $actionType = $item['action_type'] ?? 'search';
            $rules["app_bar_buttons.{$index}.value"] = $this->getValueRules($actionType);
        }

        foreach ($this->drawer_items as $index => $item) {
            $isHome = isset($item['is_home']) && $item['is_home'];
            if (! $isHome) {
                $actionType = $item['action_type'] ?? 'category';
                $rules["drawer_items.{$index}.value"] = $this->getValueRules($actionType);
            }
        }

        return $rules;
    }

    private function getValueRules($actionType)
    {
        return match ($actionType) {
            'category', 'tag' => ['required', 'string', 'max:100'],
            'internal', 'external' => ['required', 'url'],
            'hosted_page' => ['required', 'integer', 'exists:app_pages,id'],
            'all_posts', 'search', 'bookmarks', 'share' => ['nullable'],
            default => ['nullable', 'string'],
        };
    }

    public function getAvailableHostedPages()
    {
        return $this->app->hostedPages()
            ->published()
            ->ordered()
            ->get(['id', 'title', 'slug']);
    }

    public function addNavigationItem()
    {
        $regularTabsCount = count(array_filter($this->navigation_items, function ($item) {
            return ! isset($item['is_home']) || ! $item['is_home'];
        }));

        if ($regularTabsCount < 8) {
            $this->navigation_items[] = [
                'label' => '',
                'action_type' => 'category',
                'value' => '',
                'icon' => 'folder',
            ];
        }
    }

    public function addAppBarButton()
    {
        if (count($this->app_bar_buttons) < 3) {
            $this->app_bar_buttons[] = [
                'label' => 'Search',
                'action_type' => 'search',
                'value' => '',
                'icon' => 'magnifying-glass',
            ];
        }
    }

    public function removeNavigationItem($index)
    {
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home tab cannot be deleted. It is required for navigation.'
            );

            return;
        }

        unset($this->navigation_items[$index]);
        $this->navigation_items = array_values($this->navigation_items);
    }

    public function removeAppBarButton($index)
    {
        unset($this->app_bar_buttons[$index]);
        $this->app_bar_buttons = array_values($this->app_bar_buttons);
    }

    public function moveUp($index)
    {
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast', type: 'error', message: 'Home tab must remain first.');

            return;
        }

        if ($index > 0) {
            $temp = $this->navigation_items[$index];
            $this->navigation_items[$index] = $this->navigation_items[$index - 1];
            $this->navigation_items[$index - 1] = $temp;
        }
    }

    public function moveDown($index)
    {
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast', type: 'error', message: 'Home tab must remain first.');

            return;
        }

        if ($index < count($this->navigation_items) - 1) {
            $temp = $this->navigation_items[$index];
            $this->navigation_items[$index] = $this->navigation_items[$index + 1];
            $this->navigation_items[$index + 1] = $temp;
        }
    }

    public function addDrawerItem()
    {
        $regularItemsCount = count(array_filter($this->drawer_items, function ($item) {
            return ! isset($item['is_home']) || ! $item['is_home'];
        }));

        if ($regularItemsCount < 9) {
            $this->drawer_items[] = [
                'label' => '',
                'action_type' => 'category',
                'value' => '',
                'icon' => 'folder',
            ];
        }
    }

    public function removeDrawerItem($index)
    {
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home item cannot be deleted.'
            );

            return;
        }

        unset($this->drawer_items[$index]);
        $this->drawer_items = array_values($this->drawer_items);
    }

    public function moveDrawerItemUp($index)
    {
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            return;
        }

        if ($index > 0) {
            $temp = $this->drawer_items[$index];
            $this->drawer_items[$index] = $this->drawer_items[$index - 1];
            $this->drawer_items[$index - 1] = $temp;
        }
    }

    public function moveDrawerItemDown($index)
    {
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            return;
        }

        if ($index < count($this->drawer_items) - 1) {
            $temp = $this->drawer_items[$index];
            $this->drawer_items[$index] = $this->drawer_items[$index + 1];
            $this->drawer_items[$index + 1] = $temp;
        }
    }

    public function save()
    {
        $this->ensureHomeItem();

        $validated = $this->validate();

        $config = $this->app->androidWordPressConfig;

        // Re-index arrays
        if (isset($validated['navigation_items'])) {
            ksort($validated['navigation_items']);
            $validated['navigation_items'] = array_values($validated['navigation_items']);
        }
        if (isset($validated['drawer_items'])) {
            ksort($validated['drawer_items']);
            $validated['drawer_items'] = array_values($validated['drawer_items']);
        }
        if (isset($validated['app_bar_buttons'])) {
            ksort($validated['app_bar_buttons']);
            $validated['app_bar_buttons'] = array_values($validated['app_bar_buttons']);
        }

        // Force-disable swipe navigation for drawer layout
        if ($config->layout_template === 'app_bar_drawer') {
            $validated['swipe_navigation'] = false;
        }

        try {
            $config->update($validated);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Navigation settings saved successfully!'
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
        $this->drawer_items = $config->drawer_items ?? [];
        $this->ensureHomeItem();
    }

    public function render()
    {
        return view('plugins.android-wordpress::livewire.app-navigation-settings');
    }
}
