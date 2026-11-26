<?php

namespace App\Plugins\Platforms\AndroidWebView\Livewire;

use App\Models\App;
use Livewire\Attributes\On;
use Livewire\Component;

class AppNavigationSettings extends Component
{
    public App $app;

    // Form fields
    public $navigation_items = [];

    public $app_bar_buttons = [];

    public $drawer_items = [];

    public $pull_to_refresh;

    public $swipe_navigation;

    public $preserve_tab_state;

    /**
     * Available icons for navigation items and app bar buttons.
     *
     * These icons use Heroicons (https://heroicons.com/) which are cross-platform
     * compatible SVG paths. All icons work correctly on both Android and iOS platforms
     * as they are rendered as standard SVG elements.
     *
     * Available icons:
     * - home: Home icon
     * - shopping-cart: Shopping cart/e-commerce
     * - heart: Favorites/likes
     * - flame: Trending/popular
     * - newspaper: News/articles
     * - server: Data/API
     * - mobile: Mobile/phone
     * - globe: Web/internet
     * - chart: Analytics/statistics
     * - info: Information
     * - user: User/profile
     * - settings: Settings/configuration
     * - mail: Email/contact
     *
     * @var array<string, string>
     */
    public $availableIcons = [
        'home' => 'Home',
        'shopping-cart' => 'Shopping Cart',
        'heart' => 'Heart',
        'flame' => 'Flame',
        'newspaper' => 'Newspaper',
        'server' => 'Server',
        'mobile' => 'Mobile',
        'globe' => 'Globe',
        'chart' => 'Chart',
        'info' => 'Info',
        'user' => 'User',
        'settings' => 'Settings',
        'mail' => 'Mail',
    ];

    public function mount()
    {
        $config = $this->app->androidWebViewConfig;

        // Load current navigation settings
        $this->navigation_items = $this->migrateNavigationItems($config->navigation_items ?? []);
        $this->app_bar_buttons = $this->migrateNavigationItems($config->app_bar_buttons ?? []);
        $this->drawer_items = $this->migrateNavigationItems($config->drawer_items ?? []);
        $this->pull_to_refresh = $config->pull_to_refresh ?? true;
        $this->swipe_navigation = $config->swipe_navigation ?? true;
        $this->preserve_tab_state = $config->preserve_tab_state ?? false;

        // Ensure home item exists and is synced
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
     * Migrate legacy navigation items to new structure
     */
    private function migrateNavigationItems($items)
    {
        if (empty($items)) {
            return [];
        }

        return array_map(function ($item) {
            // If already has action_type, return as is
            if (isset($item['action_type'])) {
                return $item;
            }

            // Migrate old structure: {label, url, icon, external}
            // to new structure: {label, action_type, value, icon}
            return [
                'label' => $item['label'] ?? '',
                'action_type' => isset($item['external']) && $item['external'] ? 'external' : 'internal',
                'value' => $item['url'] ?? '',
                'icon' => $item['icon'] ?? 'home',
            ];
        }, $items);
    }

    /**
     * Ensure home item exists and is synced with main app URL
     * Applies to both navigation tabs and drawer items
     */
    private function ensureHomeItem()
    {
        $config = $this->app->androidWebViewConfig;

        // Handle navigation tabs (app_bar_tabs layout)
        if ($config->layout_template === 'app_bar_tabs') {
            // Find existing home tab
            $homeTabIndex = null;
            foreach ($this->navigation_items as $index => $item) {
                if (isset($item['is_home']) && $item['is_home']) {
                    $homeTabIndex = $index;
                    break;
                }
            }

            // If home tab exists, sync its URL to main app URL and ensure it's at index 0
            if ($homeTabIndex !== null) {
                // Ensure home tab is at index 0 FIRST (before any modifications)
                if ($homeTabIndex !== 0) {
                    $homeTab = $this->navigation_items[$homeTabIndex];
                    unset($this->navigation_items[$homeTabIndex]);
                    $this->navigation_items = array_values($this->navigation_items);
                    array_unshift($this->navigation_items, $homeTab);
                }

                // Now sync values (home is always at index 0 now)
                $this->navigation_items[0]['value'] = $config->url;
                $this->navigation_items[0]['action_type'] = 'internal';
            } else {
                // Create home tab at the beginning
                array_unshift($this->navigation_items, [
                    'label' => 'Home',
                    'action_type' => 'internal',
                    'value' => $config->url,
                    'icon' => 'home',
                    'is_home' => true,
                ]);
            }
        }

        // Handle drawer items (app_bar_drawer layout)
        if ($config->layout_template === 'app_bar_drawer') {
            // Find existing home item
            $homeItemIndex = null;
            foreach ($this->drawer_items as $index => $item) {
                if (isset($item['is_home']) && $item['is_home']) {
                    $homeItemIndex = $index;
                    break;
                }
            }

            // If home item exists, sync its URL to main app URL and ensure it's at index 0
            if ($homeItemIndex !== null) {
                // Ensure home item is at index 0 FIRST (before any modifications)
                if ($homeItemIndex !== 0) {
                    $homeItem = $this->drawer_items[$homeItemIndex];
                    unset($this->drawer_items[$homeItemIndex]);
                    $this->drawer_items = array_values($this->drawer_items);
                    array_unshift($this->drawer_items, $homeItem);
                }

                // Now sync values (home is always at index 0 now)
                $this->drawer_items[0]['value'] = $config->url;
                $this->drawer_items[0]['action_type'] = 'internal';
            } else {
                // Create home item at the beginning
                array_unshift($this->drawer_items, [
                    'label' => 'Home',
                    'action_type' => 'internal',
                    'value' => $config->url,
                    'icon' => 'home',
                    'is_home' => true,
                ]);
            }
        }
    }

    protected function rules()
    {
        $rules = [
            // Allow up to 9 items (1 home tab + 8 regular tabs)
            'navigation_items' => ['nullable', 'array', 'max:9'],
            'navigation_items.*.label' => ['required', 'string', 'max:50'],
            'navigation_items.*.action_type' => ['required', 'in:internal,external,share,email,call,hosted_page'],
            'navigation_items.*.icon' => ['required', 'string'],
            'navigation_items.*.is_home' => ['nullable', 'boolean'],
            'app_bar_buttons' => ['nullable', 'array', 'max:3'],
            'app_bar_buttons.*.label' => ['required', 'string', 'max:50'],
            'app_bar_buttons.*.action_type' => ['required', 'in:internal,external,share,email,call,hosted_page'],
            'app_bar_buttons.*.icon' => ['required', 'string'],
            'drawer_items' => ['nullable', 'array', 'max:10'],
            'drawer_items.*.label' => ['required', 'string', 'max:50'],
            'drawer_items.*.action_type' => ['required', 'in:internal,external,share,email,call,hosted_page'],
            'drawer_items.*.icon' => ['required', 'string'],
            'drawer_items.*.is_home' => ['nullable', 'boolean'],
            'pull_to_refresh' => ['required', 'boolean'],
            'swipe_navigation' => ['required', 'boolean'],
            'preserve_tab_state' => ['required', 'boolean'],
        ];

        // Dynamic validation for value field based on action_type
        // Home tab's value is auto-synced, so skip validation for it
        foreach ($this->navigation_items as $index => $item) {
            $isHome = isset($item['is_home']) && $item['is_home'];
            if (! $isHome) {
                $actionType = $item['action_type'] ?? 'internal';
                $rules["navigation_items.{$index}.value"] = $this->getValueRules($actionType);
            }
        }

        foreach ($this->app_bar_buttons as $index => $item) {
            $actionType = $item['action_type'] ?? 'internal';
            $rules["app_bar_buttons.{$index}.value"] = $this->getValueRules($actionType);
        }

        // Dynamic validation for drawer items
        // Home item's value is auto-synced, so skip validation for it
        foreach ($this->drawer_items as $index => $item) {
            $isHome = isset($item['is_home']) && $item['is_home'];
            if (! $isHome) {
                $actionType = $item['action_type'] ?? 'internal';
                $rules["drawer_items.{$index}.value"] = $this->getValueRules($actionType);
            }
        }

        return $rules;
    }

    /**
     * Get validation rules for value field based on action type
     */
    private function getValueRules($actionType)
    {
        return match ($actionType) {
            'internal', 'external' => ['required', 'url'],
            'email' => ['required', 'email'],
            'call' => ['required', 'regex:/^[+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/'],
            'share' => ['nullable'],
            'hosted_page' => ['required', 'integer', 'exists:app_pages,id'],
            default => ['required', 'string'],
        };
    }

    /**
     * Get available hosted pages for this app
     */
    public function getAvailableHostedPages()
    {
        return $this->app->hostedPages()
            ->published()
            ->ordered()
            ->get(['id', 'title', 'slug']);
    }

    public function addNavigationItem()
    {
        // Count only regular tabs (exclude home tab)
        $regularTabsCount = count(array_filter($this->navigation_items, function ($item) {
            return ! isset($item['is_home']) || ! $item['is_home'];
        }));

        // Allow up to 8 regular tabs (plus 1 home tab = 9 total)
        if ($regularTabsCount < 8) {
            $this->navigation_items[] = [
                'label' => '',
                'action_type' => 'internal',
                'value' => '',
                'icon' => 'home',
            ];
        }
    }

    public function addAppBarButton()
    {
        if (count($this->app_bar_buttons) < 3) {
            $this->app_bar_buttons[] = [
                'label' => '',
                'action_type' => 'internal',
                'value' => '',
                'icon' => 'home',
            ];
        }
    }

    public function removeNavigationItem($index)
    {
        // Check if this is the home tab
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home tab cannot be deleted. It is required for navigation.'
            );

            return;
        }

        unset($this->navigation_items[$index]);
        $this->navigation_items = array_values($this->navigation_items); // Re-index array
    }

    public function removeAppBarButton($index)
    {
        unset($this->app_bar_buttons[$index]);
        $this->app_bar_buttons = array_values($this->app_bar_buttons); // Re-index array
    }

    public function moveUp($index)
    {
        // Prevent moving home tab
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home tab must remain first and cannot be moved.'
            );

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
        // Prevent moving home tab
        if (isset($this->navigation_items[$index]['is_home']) && $this->navigation_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home tab must remain first and cannot be moved.'
            );

            return;
        }

        // Prevent moving item above home tab (would push home down)
        if ($index + 1 < count($this->navigation_items) &&
            isset($this->navigation_items[$index + 1]['is_home']) &&
            $this->navigation_items[$index + 1]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Cannot move items below the home tab.'
            );

            return;
        }

        if ($index < count($this->navigation_items) - 1) {
            $temp = $this->navigation_items[$index];
            $this->navigation_items[$index] = $this->navigation_items[$index + 1];
            $this->navigation_items[$index + 1] = $temp;
        }
    }

    public function moveAppBarButtonUp($index)
    {
        if ($index > 0) {
            $temp = $this->app_bar_buttons[$index];
            $this->app_bar_buttons[$index] = $this->app_bar_buttons[$index - 1];
            $this->app_bar_buttons[$index - 1] = $temp;
        }
    }

    public function moveAppBarButtonDown($index)
    {
        if ($index < count($this->app_bar_buttons) - 1) {
            $temp = $this->app_bar_buttons[$index];
            $this->app_bar_buttons[$index] = $this->app_bar_buttons[$index + 1];
            $this->app_bar_buttons[$index + 1] = $temp;
        }
    }

    public function addDrawerItem()
    {
        // Count only regular items (exclude home item)
        $regularItemsCount = count(array_filter($this->drawer_items, function ($item) {
            return ! isset($item['is_home']) || ! $item['is_home'];
        }));

        // Allow up to 9 regular items (plus 1 home item = 10 total)
        if ($regularItemsCount < 9) {
            $this->drawer_items[] = [
                'label' => '',
                'action_type' => 'internal',
                'value' => '',
                'icon' => 'home',
            ];
        }
    }

    public function removeDrawerItem($index)
    {
        // Check if this is the home item
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home item cannot be deleted. It is required for navigation.'
            );

            return;
        }

        unset($this->drawer_items[$index]);
        $this->drawer_items = array_values($this->drawer_items);
    }

    public function moveDrawerItemUp($index)
    {
        // Prevent moving home item
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home item must remain first and cannot be moved.'
            );

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
        // Prevent moving home item
        if (isset($this->drawer_items[$index]['is_home']) && $this->drawer_items[$index]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Home item must remain first and cannot be moved.'
            );

            return;
        }

        // Prevent moving item above home item (would push home down)
        if ($index + 1 < count($this->drawer_items) &&
            isset($this->drawer_items[$index + 1]['is_home']) &&
            $this->drawer_items[$index + 1]['is_home']) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Cannot move items below the home item.'
            );

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
        // Ensure home item exists and is synced before saving
        $this->ensureHomeItem();

        // SAFEGUARD: Verify home tab is at index 0 (for tabs layout)
        $homeIndex = null;
        foreach ($this->navigation_items as $index => $item) {
            if (isset($item['is_home']) && $item['is_home']) {
                $homeIndex = $index;
                break;
            }
        }

        if ($homeIndex !== null && $homeIndex !== 0) {
            // This shouldn't happen with UI restrictions, but safeguard against direct API calls
            $homeTab = $this->navigation_items[$homeIndex];
            unset($this->navigation_items[$homeIndex]);
            $this->navigation_items = array_values($this->navigation_items);
            array_unshift($this->navigation_items, $homeTab);
        }

        // SAFEGUARD: Verify home item is at index 0 (for drawer layout)
        $homeDrawerIndex = null;
        foreach ($this->drawer_items as $index => $item) {
            if (isset($item['is_home']) && $item['is_home']) {
                $homeDrawerIndex = $index;
                break;
            }
        }

        if ($homeDrawerIndex !== null && $homeDrawerIndex !== 0) {
            // This shouldn't happen with UI restrictions, but safeguard against direct API calls
            $homeDrawerItem = $this->drawer_items[$homeDrawerIndex];
            unset($this->drawer_items[$homeDrawerIndex]);
            $this->drawer_items = array_values($this->drawer_items);
            array_unshift($this->drawer_items, $homeDrawerItem);
        }

        // Validate all fields
        $validated = $this->validate();

        $config = $this->app->androidWebViewConfig;

        // CRITICAL: Re-index arrays to ensure proper iteration order when saved to JSON
        // Livewire's validate() may return arrays with out-of-order iteration order
        // which causes items to appear in wrong order after JSON encode/decode cycle
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

        // Force-disable swipe navigation for drawer layout to prevent gesture conflicts
        if ($config->layout_template === 'app_bar_drawer') {
            $validated['swipe_navigation'] = false;
        }

        try {
            // Update the config
            $config->update($validated);

            // Dispatch success toast
            $this->dispatch('show-toast',
                type: 'success',
                message: 'Navigation settings saved successfully!'
            );

            // Dispatch event to refresh preview panel
            $this->dispatch('settings-updated');

        } catch (\Exception $e) {
            // Dispatch error toast
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Failed to save settings. Please try again.'
            );
        }
    }

    #[On('settings-updated')]
    public function refreshApp()
    {
        // Refresh the app model from database to get latest layout_template
        $this->app->refresh();

        $config = $this->app->androidWebViewConfig;

        // Reload drawer items
        $this->drawer_items = $this->migrateNavigationItems($config->drawer_items ?? []);

        // Ensure home item is synced with updated app URL (if changed in General Settings)
        $this->ensureHomeItem();
    }

    public function render()
    {
        return view('plugins.android-webview::livewire.app-navigation-settings');
    }
}
