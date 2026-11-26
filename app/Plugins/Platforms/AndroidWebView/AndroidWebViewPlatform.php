<?php

namespace App\Plugins\Platforms\AndroidWebView;

use App\Contracts\PlatformPlugin;
use App\DataTransferObjects\BuildConfigContext;
use App\Models\AndroidWebViewConfig;
use App\Models\App;
use App\Models\AppBuild;
use App\Models\Keystore;
use App\Models\PushNotificationConfig;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppCreationForm;
use App\Plugins\Shared\Builders\AndroidBuildConfigBuilder;
use App\Plugins\Shared\Models\AppPage;
use App\Plugins\Shared\Traits\AndroidPlatformTrait;
use App\Traits\HasAiFunctions;

class AndroidWebViewPlatform implements PlatformPlugin
{
    use AndroidPlatformTrait;
    use HasAiFunctions;

    private array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? [];
    }

    /**
     * Base Plugin Interface Methods
     */
    public function getName(): string
    {
        return 'Android WebView';
    }

    public function getDescription(): string
    {
        return 'Build Android apps that load website content in a native WebView with full customization';
    }

    public function getType(): string
    {
        return 'platform';
    }

    public function getIcon(): string
    {
        return 'plugins/android-webview/icon.jpg';
    }

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getAuthor(): string
    {
        return 'Titan Systems';
    }

    public function getAuthorUrl(): string
    {
        return 'https://codecanyon.net/user/titansystems';
    }

    public function isConfigured(): bool
    {
        // Android WebView platform is always configured (no global config needed)
        return true;
    }

    public function validateConfig(array $config): void
    {
        // No global configuration required for Android WebView platform
    }

    /**
     * Platform Plugin Interface Methods
     */
    public function getPlatformId(): string
    {
        return 'android-webview';
    }

    public function getCreationFormView(): string
    {
        return 'plugins.android-webview::creation-form';
    }

    public function getEditorView(): string
    {
        return 'plugins.android-webview::editor.app-editor';
    }

    public function validateForBuild(App $app): array
    {
        $errors = [];
        $config = $app->androidWebViewConfig;

        if (! $config) {
            $errors[] = 'Android WebView configuration not found';

            return $errors;
        }

        // Check package name
        $packageName = $config->package_name;
        if (empty($packageName)) {
            $errors[] = 'Package name is required';
        } elseif (! $this->validatePackageId($packageName)) {
            $errors[] = 'Invalid Android package name format';
        }

        // Check version name
        $versionName = $app->version_name;
        if (empty($versionName)) {
            $errors[] = 'Version name is required';
        }

        // Check keystore for AAB builds (required for upload to Play Store)
        $keystoreId = $config->keystore_id;
        if (empty($keystoreId)) {
            $errors[] = 'Keystore is required for signing the app. Please upload a keystore in the Signing Keys section.';
        }

        // Check app icon
        if (empty($config->app_icon)) {
            $errors[] = 'App icon is required';
        }

        // Check website URL (WebView-specific)
        if (empty($config->url)) {
            $errors[] = 'Website URL is required for WebView apps';
        } elseif (! filter_var($config->url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid website URL format';
        }

        return $errors;
    }

    public function prepareBuildConfig(BuildConfigContext $context): array
    {
        $builder = new AndroidBuildConfigBuilder($context);
        $baseConfig = $builder->build();

        $config = $context->app->androidWebViewConfig;
        $buildConfig = $config->build_config ?? [];

        // Add WebView-specific configuration
        $webViewConfig = [
            'content_type' => 'webview',
            'website_url' => $config->url,
            'user_agent' => $config->user_agent ?? '',
            'enable_javascript' => $buildConfig['enable_javascript'] ?? true,
            'enable_dom_storage' => $buildConfig['enable_dom_storage'] ?? true,
            'enable_zoom' => $buildConfig['enable_zoom'] ?? false,
            'enable_cache' => $buildConfig['enable_cache'] ?? true,
        ];

        return array_merge($baseConfig, $webViewConfig);
    }

    public function getCreationFormComponent(): ?string
    {
        return AppCreationForm::class;
    }

    public function createConfig(App $app, array $formData): void
    {
        // Use package name from form data (already validated for uniqueness)
        // Fallback to auto-generation if not provided (shouldn't happen with proper form validation)
        $packageName = $formData['package_name'] ?? null;

        if (! $packageName) {
            // Fallback: Generate unique package name from app name
            $packageName = $this->generatePackageId($app->name);
            $counter = 1;

            while (AndroidWebViewConfig::where('package_name', $packageName)->exists()) {
                $counter++;
                $packageName = $this->generatePackageId($app->name, $counter);
            }
        }

        // Create platform-specific config with form data
        AndroidWebViewConfig::create([
            'app_id' => $app->id,
            'url' => $formData['url'] ?? '',
            'package_name' => $packageName,
            'theme_color' => $formData['theme_color'] ?? '#3b82f6',
            'title_color' => $formData['title_color'] ?? 'automatic',
            'layout_template' => $formData['layout_template'] ?? 'app_bar_drawer',
            'screen_orientation' => 'system',
            'app_language' => 'en',
            // UI colors inherit theme_color for consistent branding
            'progress_indicator_color' => $formData['theme_color'] ?? '#3b82f6',
            'nav_tab_icon_color_light' => '#6B7280',
            'nav_tab_icon_color_dark' => '#6B7280',
            'active_tab_color_light' => $formData['theme_color'] ?? '#3b82f6',
            'active_tab_color_dark' => $formData['theme_color'] ?? '#3b82f6',
            'splashscreen_background' => $formData['theme_color'] ?? '#3b82f6',
            'splashscreen_duration' => 3, // Duration in seconds (1-10 range)
            'splashscreen_animation' => 'fade',
            'pull_to_refresh' => true,
            'swipe_navigation' => true,
        ]);
    }

    public function updateConfig(App $app, array $formData): void
    {
        $config = $app->androidWebViewConfig;

        if (! $config) {
            throw new \Exception('Android WebView configuration not found for this app');
        }

        // Update platform-specific fields
        $config->update([
            'url' => $formData['url'] ?? $config->url,
            'theme_color' => $formData['theme_color'] ?? $config->theme_color,
            'title_color' => $formData['title_color'] ?? $config->title_color,
            'layout_template' => $formData['layout_template'] ?? $config->layout_template,
        ]);
    }

    /**
     * New Platform Plugin Interface Methods
     */
    public function getBuildModalComponent(): string
    {
        return 'plugins.android-webview.build-modal';
    }

    public function getAppCardView(): string
    {
        return 'plugins.android-webview::app-card';
    }

    public function getPreviewPanelComponent(): ?string
    {
        return 'plugins.android-webview.app-preview-panel';
    }

    public function getBuildTableComponent(): string
    {
        return 'plugins.android-webview.builds-table';
    }

    public function getDeleteBuildModalComponent(): string
    {
        return 'plugins.android-webview.delete-build-modal';
    }

    public function getBuildLogsModalComponent(): string
    {
        return 'plugins.android-webview.build-logs-modal';
    }

    public function getBuildDownloadModalComponent(): string
    {
        return 'plugins.android-webview.build-download-modal';
    }

    public function getConfigModelClass(): string
    {
        return \App\Models\AndroidWebViewConfig::class;
    }

    /**
     * Check if this platform supports Appetize.io previews
     */
    public function supportsAppetize(): bool
    {
        return true; // Android WebView supports Appetize previews
    }

    /**
     * Get Appetize-specific configuration for this platform
     */
    public function getAppetizeConfig(\App\Models\AppBuild $build): array
    {
        $config = $build->app->androidWebViewConfig;

        return [
            'device' => 'pixel7',
            'osVersion' => '14.0',
            'appearance' => $config->enable_dark_mode ? 'dark' : 'light',
            'deviceColor' => $config->theme_color === '#000000' ? 'black' : 'white',
            'launchUrl' => $config->url,
            'language' => 'en',
        ];
    }

    /**
     * Get Appetize platform identifier
     */
    public function getAppetizePlatformId(): string
    {
        return 'android'; // Appetize uses 'android' for all Android platforms
    }

    /**
     * Get file type for Appetize upload
     */
    public function getAppetizeFileType(\App\Models\AppBuild $build): string
    {
        // Return the build format directly (apk, aab)
        // Appetize only supports APK, but we return actual format for validation
        return $build->build_format;
    }

    /**
     * Get default Appetize device for preview
     */
    public function getAppetizeDefaultDevice(): string
    {
        return 'pixel7'; // Default Android device for Appetize
    }

    /**
     * Execute an AI function call for this platform.
     */
    public function executeAiFunction(string $name, array $args, App $app): array
    {
        return match ($name) {
            // Core functions
            'getAppConfig' => $this->aiGetAppConfig($app),
            'setGeneralSettings' => $this->aiSetGeneralSettings($app, $args),
            'setDesignSettings' => $this->aiSetDesignSettings($app, $args),
            'setColorScheme' => $this->aiSetColorScheme($app, $args),

            // Navigation functions
            'setNavigationItems' => $this->aiSetNavigationItems($app, $args),
            'setDrawerItems' => $this->aiSetDrawerItems($app, $args),
            'setDrawerCustomization' => $this->aiSetDrawerCustomization($app, $args),
            'setNavigationBehaviors' => $this->aiSetNavigationBehaviors($app, $args),
            'setAppBarButtons' => $this->aiSetAppBarButtons($app, $args),

            // Splashscreen functions
            'setSplashscreen' => $this->aiSetSplashscreen($app, $args),
            'setSplashscreenDetails' => $this->aiSetSplashscreenDetails($app, $args),

            // Configuration functions
            'setWebViewConfiguration' => $this->aiSetWebViewConfiguration($app, $args),
            'setPermissions' => $this->aiSetPermissions($app, $args),
            'setCustomCode' => $this->aiSetCustomCode($app, $args),
            'setSigningKey' => $this->aiSetSigningKey($app, $args),
            'setNotificationConfiguration' => $this->aiSetNotificationConfiguration($app, $args),

            // Build functions
            'triggerBuild' => $this->aiTriggerBuild($app, $args),
            'getAppStatus' => $this->aiGetAppStatus($app),

            // Hosted pages functions
            'createHostedPage' => $this->aiCreateHostedPage($app, $args),
            'listHostedPages' => $this->aiListHostedPages($app),
            'updateHostedPage' => $this->aiUpdateHostedPage($app, $args),
            'editHostedPage' => $this->aiEditHostedPage($app, $args),
            'deleteHostedPage' => $this->aiDeleteHostedPage($app, $args),

            default => ['success' => false, 'error' => "Unknown function: {$name}"],
        };
    }

    /**
     * Get AI context for this app's current state.
     */
    public function getAiAppContext(App $app): string
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return 'No configuration found for this app.';
        }

        $context = "=== CURRENT APP CONFIGURATION ===\n\n";

        // App basics
        $context .= "## App Information\n";
        $context .= "- Name: {$app->name}\n";
        $context .= "- Version: {$app->version_name} (code: {$app->version_code})\n";
        $context .= "- Package: {$config->package_name}\n";
        $context .= "- URL: {$config->url}\n\n";

        // General settings
        $context .= "## General Settings\n";
        $context .= "- Screen Orientation: {$config->screen_orientation}\n";
        $context .= "- Language: {$config->app_language}\n\n";

        // Design
        $context .= "## Design\n";
        $context .= "- Theme Color: {$config->theme_color}\n";
        $context .= "- Layout: {$config->layout_template}\n";
        $context .= '- Dynamic Colors (Dark Mode): '.($config->enable_dynamic_colors ? 'Yes' : 'No')."\n";
        $context .= '- Progress Indicator: '.($config->progress_indicator ?? 'linear')."\n";
        $context .= '- Show App Bar Title: '.($config->show_app_bar_title ? 'Yes' : 'No')."\n\n";

        // Drawer
        if ($config->layout_template === 'app_bar_drawer') {
            $context .= "## Drawer\n";
            $context .= '- Mode: '.($config->drawer_mode ?? 'default')."\n";
            $context .= '- Logo Enabled: '.($config->drawer_logo_enabled ? 'Yes' : 'No')."\n";
            $context .= '- Title: '.($config->drawer_title ?: 'Not set')."\n";
            $context .= '- Subtitle: '.($config->drawer_subtitle ?: 'Not set')."\n";
            $drawerItems = $config->drawer_items ?? [];
            $context .= '- Items: '.count($drawerItems)."\n";
            foreach ($drawerItems as $item) {
                $label = $item['label'] ?? $item['title'] ?? 'Untitled';
                $actionType = $item['action_type'] ?? 'internal';
                $value = $item['value'] ?? $item['url'] ?? '';
                $context .= "  - {$label} ({$actionType}): {$value}\n";
            }
            $context .= "\n";
        }

        // Navigation
        $context .= "## Navigation\n";
        $navItems = $config->navigation_items ?? [];
        $context .= '- Tabs: '.count($navItems)."\n";
        foreach ($navItems as $item) {
            $label = $item['label'] ?? $item['title'] ?? 'Untitled';
            $actionType = $item['action_type'] ?? 'internal';
            $value = $item['value'] ?? $item['url'] ?? '';
            $context .= "  - {$label} ({$actionType}): {$value}\n";
        }
        $appBarButtons = $config->app_bar_buttons ?? [];
        $context .= '- App Bar Buttons: '.count($appBarButtons)."\n";
        $context .= '- Pull to Refresh: '.($config->pull_to_refresh ? 'Yes' : 'No')."\n";
        $context .= '- Swipe Navigation: '.($config->swipe_navigation ? 'Yes' : 'No')."\n";
        $context .= '- Preserve Tab State: '.($config->preserve_tab_state ? 'Yes' : 'No')."\n\n";

        // Splashscreen
        $context .= "## Splashscreen\n";
        $context .= '- Background Type: '.($config->splashscreen_background_type ?? 'color')."\n";
        $context .= "- Background Color: {$config->splashscreen_background}\n";
        $context .= '- Duration: '.($config->splashscreen_duration ?? 3)."s\n";
        $context .= '- Animation: '.($config->splashscreen_animation ?? 'fade')."\n";
        $context .= '- Logo Enabled: '.($config->splashscreen_logo_enabled ? 'Yes' : 'No')."\n";
        $context .= '- Title: '.($config->splashscreen_title ?: 'Not set')."\n\n";

        // WebView
        $buildConfig = $config->build_config ?? [];
        $context .= "## WebView Settings\n";
        $context .= '- User Agent: '.($config->user_agent ?: 'Default')."\n";
        $context .= '- JavaScript: '.(($buildConfig['enable_javascript'] ?? true) ? 'Enabled' : 'Disabled')."\n";
        $context .= '- DOM Storage: '.(($buildConfig['enable_dom_storage'] ?? true) ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Zoom: '.(($buildConfig['enable_zoom'] ?? false) ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Cache: '.(($buildConfig['enable_cache'] ?? true) ? 'Enabled' : 'Disabled')."\n\n";

        // Permissions
        $permissions = [];
        if ($config->permission_location) {
            $permissions[] = 'Location';
        }
        if ($config->permission_camera) {
            $permissions[] = 'Camera';
        }
        if ($config->permission_storage) {
            $permissions[] = 'Storage';
        }
        if ($config->permission_record_audio) {
            $permissions[] = 'Microphone';
        }
        if ($config->permission_read_contacts) {
            $permissions[] = 'Contacts';
        }
        if ($config->permission_vibrate) {
            $permissions[] = 'Vibrate';
        }
        $context .= "## Permissions\n";
        $context .= '- Enabled: '.(empty($permissions) ? 'None' : implode(', ', $permissions))."\n\n";

        // Custom Code
        $context .= "## Custom Code\n";
        $context .= '- CSS: '.(empty($config->custom_css) ? 'None' : strlen($config->custom_css).' bytes')."\n";
        $context .= '- JavaScript: '.(empty($config->custom_js) ? 'None' : strlen($config->custom_js).' bytes')."\n\n";

        // Assets
        $context .= "## Assets\n";
        $context .= '- App Icon: '.($config->app_icon ? 'Set' : 'Not set')."\n";
        $context .= '- Keystore: '.($config->keystore_id ? 'Set (ID: '.$config->keystore_id.')' : 'Not set')."\n\n";

        // Hosted Pages
        $pages = AppPage::where('app_id', $app->id)->get();
        $context .= "## Hosted Pages ({$pages->count()})\n";
        foreach ($pages as $page) {
            $context .= "- {$page->title} ({$page->status}): {$page->url}\n";
        }
        $context .= "\n";

        // Push Notifications
        $pushConfig = $app->pushNotificationConfig;
        $context .= "## Push Notifications\n";
        $context .= '- Enabled: '.($pushConfig?->enabled ? 'Yes' : 'No')."\n";
        $context .= '- Firebase Configured: '.(! empty($pushConfig?->firebase_credentials_file) ? 'Yes' : 'No')."\n\n";

        // Build Status
        $latestBuild = AppBuild::where('app_id', $app->id)->latest()->first();
        $context .= "## Build Status\n";
        if ($latestBuild) {
            $context .= "- Latest: {$latestBuild->build_format} ({$latestBuild->status}) - {$latestBuild->created_at->diffForHumans()}\n";
        } else {
            $context .= "- No builds yet\n";
        }
        $context .= '- Ready for Build: '.(empty($this->validateForBuild($app)) ? 'Yes' : 'No')."\n";

        return $context;
    }

    /**
     * AI Function: Set design settings
     */
    private function aiSetDesignSettings(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        if (isset($args['themeColor'])) {
            if (! preg_match($hexPattern, $args['themeColor'])) {
                return ['success' => false, 'error' => 'Invalid theme color format'];
            }
            $updates['theme_color'] = $args['themeColor'];
            $updates['progress_indicator_color'] = $args['themeColor'];
            $updates['active_tab_color_light'] = $args['themeColor'];
            $updates['active_tab_color_dark'] = $args['themeColor'];
        }

        if (isset($args['enableDarkMode'])) {
            $updates['enable_dynamic_colors'] = (bool) $args['enableDarkMode'];
        }

        if (isset($args['layoutTemplate'])) {
            $validTemplates = ['app_bar', 'app_bar_tabs', 'app_bar_drawer', 'blank'];
            if (in_array($args['layoutTemplate'], $validTemplates)) {
                $updates['layout_template'] = $args['layoutTemplate'];
            }
        }

        if (isset($args['progressIndicator'])) {
            $validIndicators = ['disable', 'linear', 'circular'];
            if (in_array($args['progressIndicator'], $validIndicators)) {
                $updates['progress_indicator'] = $args['progressIndicator'];
            }
        }

        if (isset($args['progressIndicatorColor'])) {
            if (preg_match($hexPattern, $args['progressIndicatorColor'])) {
                $updates['progress_indicator_color'] = $args['progressIndicatorColor'];
            }
        }

        if (isset($args['showAppBarTitle'])) {
            $updates['show_app_bar_title'] = (bool) $args['showAppBarTitle'];
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Design settings updated successfully',
            'applied' => $updates,
        ];
    }

    /**
     * AI Function: Set navigation items
     */
    private function aiSetNavigationItems(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $items = $args['items'] ?? [];
        if (count($items) > 5) {
            return ['success' => false, 'error' => 'Maximum 5 navigation items allowed'];
        }

        $validActionTypes = ['internal', 'external', 'share', 'email', 'call', 'hosted_page'];

        // Validate and format items
        $formattedItems = [];
        foreach ($items as $index => $item) {
            $actionType = $item['actionType'] ?? 'internal';
            if (! in_array($actionType, $validActionTypes)) {
                $actionType = 'internal';
            }

            // Handle legacy 'url' parameter
            $value = $item['value'] ?? $item['url'] ?? $config->url;

            $formattedItems[] = [
                'id' => $index + 1,
                'label' => $item['title'] ?? 'Tab '.($index + 1),
                'action_type' => $actionType,
                'value' => $value,
                'icon' => $item['icon'] ?? 'home',
            ];
        }

        $config->update(['navigation_items' => $formattedItems]);

        return [
            'success' => true,
            'message' => 'Navigation items updated successfully',
            'itemCount' => count($formattedItems),
        ];
    }

    /**
     * AI Function: Set drawer items
     */
    private function aiSetDrawerItems(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $enabled = $args['enabled'] ?? true;
        $items = $args['items'] ?? [];

        $validActionTypes = ['internal', 'external', 'share', 'email', 'call', 'hosted_page'];

        // Validate and format items
        $formattedItems = [];
        foreach ($items as $index => $item) {
            $actionType = $item['actionType'] ?? 'internal';
            if (! in_array($actionType, $validActionTypes)) {
                $actionType = 'internal';
            }

            // Handle legacy 'url' parameter
            $value = $item['value'] ?? $item['url'] ?? $config->url;

            $formattedItems[] = [
                'id' => $index + 1,
                'label' => $item['title'] ?? 'Menu Item '.($index + 1),
                'action_type' => $actionType,
                'value' => $value,
                'icon' => $item['icon'] ?? 'link',
            ];
        }

        $updates = [
            'drawer_items' => $formattedItems,
        ];

        // Update layout template if enabling drawer
        if ($enabled && $config->layout_template === 'app_bar') {
            $updates['layout_template'] = 'app_bar_drawer';
        }

        $config->update($updates);

        return [
            'success' => true,
            'message' => 'Drawer items updated successfully',
            'enabled' => $enabled,
            'itemCount' => count($formattedItems),
        ];
    }

    /**
     * AI Function: Set splashscreen
     */
    private function aiSetSplashscreen(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];

        if (isset($args['backgroundColor'])) {
            $updates['splashscreen_background'] = $args['backgroundColor'];
        }

        if (isset($args['title'])) {
            $updates['splashscreen_title'] = $args['title'];
        }

        if (isset($args['subtitle'])) {
            $updates['splashscreen_subtitle'] = $args['subtitle'];
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Splashscreen settings updated successfully',
            'applied' => $updates,
        ];
    }

    /**
     * AI Function: Set permissions
     */
    private function aiSetPermissions(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $enabled = [];

        if (isset($args['location'])) {
            $updates['permission_location'] = (bool) $args['location'];
            if ($args['location']) {
                $enabled[] = 'location';
            }
        }

        if (isset($args['camera'])) {
            $updates['permission_camera'] = (bool) $args['camera'];
            if ($args['camera']) {
                $enabled[] = 'camera';
            }
        }

        if (isset($args['storage'])) {
            $updates['permission_storage'] = (bool) $args['storage'];
            if ($args['storage']) {
                $enabled[] = 'storage';
            }
        }

        if (isset($args['microphone'])) {
            $updates['permission_record_audio'] = (bool) $args['microphone'];
            if ($args['microphone']) {
                $enabled[] = 'microphone';
            }
        }

        if (isset($args['readContacts'])) {
            $updates['permission_read_contacts'] = (bool) $args['readContacts'];
            if ($args['readContacts']) {
                $enabled[] = 'read_contacts';
            }
        }

        if (isset($args['vibrate'])) {
            $updates['permission_vibrate'] = (bool) $args['vibrate'];
            if ($args['vibrate']) {
                $enabled[] = 'vibrate';
            }
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Permissions updated successfully',
            'enabledPermissions' => $enabled,
        ];
    }

    /**
     * AI Function: Trigger build
     */
    private function aiTriggerBuild(App $app, array $args): array
    {
        $format = $args['format'] ?? 'apk';
        $validFormats = ['apk', 'aab'];

        if (! in_array($format, $validFormats)) {
            return ['success' => false, 'error' => 'Invalid format. Use "apk" or "aab"'];
        }

        // Validate app is ready for build
        $errors = $this->validateForBuild($app);
        if (! empty($errors)) {
            return [
                'success' => false,
                'error' => 'App not ready for build',
                'validationErrors' => $errors,
            ];
        }

        // Check user has build credits
        $user = $app->user;
        if (! $user->hasBuildCredits()) {
            return [
                'success' => false,
                'error' => 'Insufficient build credits. Please upgrade your plan or purchase more credits.',
            ];
        }

        // Create the build
        $build = AppBuild::create([
            'app_id' => $app->id,
            'platform' => 'android-webview',
            'build_format' => $format,
            'build_type' => 'release',
            'version_name' => $app->version_name,
            'version_code' => $app->version_code,
            'status' => 'pending',
            'keystore_id' => $app->androidWebViewConfig->keystore_id,
        ]);

        return [
            'success' => true,
            'message' => "Build started! Format: {$format}",
            'buildId' => $build->id,
            'status' => 'pending',
            'note' => $format === 'aab' ? 'AAB format is for Google Play Store upload.' : 'APK format is for direct installation and testing.',
        ];
    }

    /**
     * AI Function: Get app status
     */
    private function aiGetAppStatus(App $app): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $latestBuild = AppBuild::where('app_id', $app->id)->latest()->first();
        $pages = AppPage::where('app_id', $app->id)->get();

        return [
            'success' => true,
            'app' => [
                'name' => $app->name,
                'version' => "{$app->version_name} ({$app->version_code})",
                'packageName' => $config->package_name,
                'url' => $config->url,
            ],
            'design' => [
                'themeColor' => $config->theme_color,
                'layoutTemplate' => $config->layout_template,
                'darkModeEnabled' => $config->enable_dynamic_colors,
            ],
            'navigation' => [
                'tabCount' => count($config->navigation_items ?? []),
                'drawerItemCount' => count($config->drawer_items ?? []),
            ],
            'assets' => [
                'hasIcon' => ! empty($config->app_icon),
                'hasKeystore' => ! empty($config->keystore_id),
            ],
            'hostedPages' => $pages->count(),
            'latestBuild' => $latestBuild ? [
                'format' => $latestBuild->build_format,
                'status' => $latestBuild->status,
                'createdAt' => $latestBuild->created_at->diffForHumans(),
            ] : null,
            'readyForBuild' => empty($this->validateForBuild($app)),
        ];
    }

    /**
     * AI Function: Create hosted page
     */
    private function aiCreateHostedPage(App $app, array $args): array
    {
        // Check plan permission
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages. Please upgrade your plan.'];
        }

        // Check page limit
        $maxPages = $user->plan?->getMaxHostedPagesPerApp();
        if ($maxPages !== null) {
            $currentCount = AppPage::where('app_id', $app->id)->count();
            if ($currentCount >= $maxPages) {
                return ['success' => false, 'error' => "You have reached the maximum of {$maxPages} hosted pages for this app."];
            }
        }

        $title = $args['title'] ?? null;
        if (! $title || strlen($title) > 100) {
            return ['success' => false, 'error' => 'Title is required (max 100 chars)'];
        }

        $template = $args['template'] ?? 'blank';
        $status = $args['status'] ?? 'draft';

        $validTemplates = ['blank', 'about-us', 'contact', 'menu', 'terms', 'faq'];
        if (! in_array($template, $validTemplates)) {
            return ['success' => false, 'error' => 'Invalid template'];
        }

        $slug = AppPage::generateSlug($title, $app->id);

        $htmlContent = $this->getPageTemplateHtml($template, $title);

        $page = AppPage::create([
            'app_id' => $app->id,
            'title' => $title,
            'slug' => $slug,
            'html_content' => $htmlContent,
            'css_content' => '',
            'js_content' => '',
            'status' => $status,
            'sort_order' => AppPage::where('app_id', $app->id)->count() + 1,
        ]);

        return [
            'success' => true,
            'message' => "Page '{$title}' created successfully",
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'url' => $page->url,
            ],
        ];
    }

    /**
     * AI Function: List hosted pages
     */
    private function aiListHostedPages(App $app): array
    {
        $pages = AppPage::where('app_id', $app->id)->ordered()->get();

        return [
            'success' => true,
            'pages' => $pages->map(fn ($page) => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'url' => $page->url,
            ])->toArray(),
        ];
    }

    /**
     * AI Function: Update hosted page
     */
    private function aiUpdateHostedPage(App $app, array $args): array
    {
        // Check plan permission
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages. Please upgrade your plan.'];
        }

        $pageId = $args['pageId'] ?? null;
        if (! $pageId) {
            return ['success' => false, 'error' => 'pageId is required'];
        }

        $page = AppPage::where('app_id', $app->id)->where('id', $pageId)->first();
        if (! $page) {
            return ['success' => false, 'error' => 'Page not found'];
        }

        $updates = [];

        if (isset($args['title']) && strlen($args['title']) <= 100) {
            $updates['title'] = $args['title'];
            $updates['slug'] = AppPage::generateSlug($args['title'], $app->id, $page->id);
        }

        if (isset($args['status']) && in_array($args['status'], ['draft', 'published'])) {
            $updates['status'] = $args['status'];
        }

        if (! empty($updates)) {
            $page->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Page updated successfully',
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'url' => $page->url,
            ],
        ];
    }

    /**
     * AI Function: Delete hosted page
     */
    private function aiDeleteHostedPage(App $app, array $args): array
    {
        // Check plan permission
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages. Please upgrade your plan.'];
        }

        $pageId = $args['pageId'] ?? null;
        if (! $pageId) {
            return ['success' => false, 'error' => 'pageId is required'];
        }

        $page = AppPage::where('app_id', $app->id)->where('id', $pageId)->first();
        if (! $page) {
            return ['success' => false, 'error' => 'Page not found'];
        }

        $title = $page->title;
        $page->delete();

        return [
            'success' => true,
            'message' => "Page '{$title}' deleted successfully",
        ];
    }

    /**
     * AI Function: Edit hosted page content
     */
    private function aiEditHostedPage(App $app, array $args): array
    {
        // Check plan permission
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages. Please upgrade your plan.'];
        }

        $pageId = $args['pageId'] ?? null;
        if (! $pageId) {
            return ['success' => false, 'error' => 'pageId is required'];
        }

        $page = AppPage::where('app_id', $app->id)->where('id', $pageId)->first();
        if (! $page) {
            return ['success' => false, 'error' => 'Page not found'];
        }

        $updates = [];

        if (isset($args['htmlContent'])) {
            $updates['html_content'] = $args['htmlContent'];
        }

        if (isset($args['cssContent'])) {
            $updates['css_content'] = $args['cssContent'];
        }

        if (isset($args['jsContent'])) {
            $updates['js_content'] = $args['jsContent'];
        }

        if (! empty($updates)) {
            $page->update($updates);
        }

        return [
            'success' => true,
            'message' => "Page '{$page->title}' content updated successfully",
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'url' => $page->url,
                'htmlLength' => strlen($page->html_content ?? ''),
                'cssLength' => strlen($page->css_content ?? ''),
                'jsLength' => strlen($page->js_content ?? ''),
            ],
        ];
    }

    /**
     * AI Function: Get full app configuration
     */
    private function aiGetAppConfig(App $app): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $pushConfig = $app->pushNotificationConfig;
        $pages = AppPage::where('app_id', $app->id)->get();
        $latestBuild = AppBuild::where('app_id', $app->id)->latest()->first();
        $buildConfig = $config->build_config ?? [];

        return [
            'success' => true,
            'config' => [
                'app' => [
                    'id' => $app->id,
                    'name' => $app->name,
                    'versionName' => $app->version_name,
                    'versionCode' => $app->version_code,
                ],
                'general' => [
                    'url' => $config->url,
                    'packageName' => $config->package_name,
                    'screenOrientation' => $config->screen_orientation,
                    'appLanguage' => $config->app_language,
                ],
                'design' => [
                    'themeColor' => $config->theme_color,
                    'textColorLight' => $config->text_color_light,
                    'textColorDark' => $config->text_color_dark,
                    'enableDynamicColors' => $config->enable_dynamic_colors,
                    'layoutTemplate' => $config->layout_template,
                    'progressIndicator' => $config->progress_indicator,
                    'progressIndicatorColor' => $config->progress_indicator_color,
                    'showAppBarTitle' => $config->show_app_bar_title,
                    'navTabIconColorLight' => $config->nav_tab_icon_color_light,
                    'navTabIconColorDark' => $config->nav_tab_icon_color_dark,
                    'activeTabColorLight' => $config->active_tab_color_light,
                    'activeTabColorDark' => $config->active_tab_color_dark,
                ],
                'drawer' => [
                    'mode' => $config->drawer_mode,
                    'backgroundColor' => $config->drawer_background_color,
                    'hasBackgroundImage' => ! empty($config->drawer_background_image),
                    'logoEnabled' => $config->drawer_logo_enabled,
                    'logoSize' => $config->drawer_logo_size,
                    'title' => $config->drawer_title,
                    'subtitle' => $config->drawer_subtitle,
                    'textTheme' => $config->drawer_text_theme,
                    'iconColorLight' => $config->drawer_icon_color_light,
                    'iconColorDark' => $config->drawer_icon_color_dark,
                    'activeColorLight' => $config->drawer_active_color_light,
                    'activeColorDark' => $config->drawer_active_color_dark,
                    'items' => $config->drawer_items ?? [],
                ],
                'splashscreen' => [
                    'backgroundType' => $config->splashscreen_background_type,
                    'backgroundColor' => $config->splashscreen_background,
                    'hasImage' => ! empty($config->splashscreen_image),
                    'duration' => $config->splashscreen_duration,
                    'animation' => $config->splashscreen_animation,
                    'logoEnabled' => $config->splashscreen_logo_enabled,
                    'logoSize' => $config->splashscreen_logo_size,
                    'title' => $config->splashscreen_title,
                    'subtitle' => $config->splashscreen_subtitle,
                    'textTheme' => $config->splashscreen_text_theme,
                ],
                'navigation' => [
                    'items' => $config->navigation_items ?? [],
                    'appBarButtons' => $config->app_bar_buttons ?? [],
                    'pullToRefresh' => $config->pull_to_refresh,
                    'swipeNavigation' => $config->swipe_navigation,
                    'preserveTabState' => $config->preserve_tab_state,
                ],
                'webview' => [
                    'userAgent' => $config->user_agent,
                    'enableJavascript' => $buildConfig['enable_javascript'] ?? true,
                    'enableDomStorage' => $buildConfig['enable_dom_storage'] ?? true,
                    'enableZoom' => $buildConfig['enable_zoom'] ?? false,
                    'enableCache' => $buildConfig['enable_cache'] ?? true,
                ],
                'permissions' => [
                    'location' => $config->permission_location,
                    'camera' => $config->permission_camera,
                    'storage' => $config->permission_storage,
                    'microphone' => $config->permission_record_audio,
                    'readContacts' => $config->permission_read_contacts,
                    'vibrate' => $config->permission_vibrate,
                ],
                'customCode' => [
                    'hasCSS' => ! empty($config->custom_css),
                    'cssLength' => strlen($config->custom_css ?? ''),
                    'hasJS' => ! empty($config->custom_js),
                    'jsLength' => strlen($config->custom_js ?? ''),
                ],
                'assets' => [
                    'hasIcon' => ! empty($config->app_icon),
                    'hasKeystore' => ! empty($config->keystore_id),
                    'keystoreId' => $config->keystore_id,
                ],
                'notifications' => [
                    'enabled' => $pushConfig?->enabled ?? false,
                    'hasGoogleServices' => ! empty($pushConfig?->google_services_file),
                    'hasFirebaseCredentials' => ! empty($pushConfig?->firebase_credentials_file),
                ],
                'hostedPages' => $pages->map(fn ($p) => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'status' => $p->status,
                    'url' => $p->url,
                ])->toArray(),
                'latestBuild' => $latestBuild ? [
                    'id' => $latestBuild->id,
                    'format' => $latestBuild->build_format,
                    'status' => $latestBuild->status,
                    'createdAt' => $latestBuild->created_at->diffForHumans(),
                ] : null,
                'readyForBuild' => empty($this->validateForBuild($app)),
            ],
        ];
    }

    /**
     * AI Function: Set general settings
     */
    private function aiSetGeneralSettings(App $app, array $args): array
    {
        try {
            $config = $app->androidWebViewConfig;
            if (! $config) {
                return ['success' => false, 'error' => 'Configuration not found'];
            }

            $updates = [];
            $appUpdates = [];

            if (isset($args['appName'])) {
                if (strlen($args['appName']) > 255) {
                    return ['success' => false, 'error' => 'App name must be 255 characters or less'];
                }
                $appUpdates['name'] = $args['appName'];
            }

            if (isset($args['url'])) {
                if (! filter_var($args['url'], FILTER_VALIDATE_URL)) {
                    return ['success' => false, 'error' => 'Invalid URL format. Must include http:// or https://'];
                }
                $updates['url'] = $args['url'];
            }

            if (isset($args['versionName'])) {
                if (! preg_match('/^\d+\.\d+\.\d+$/', $args['versionName'])) {
                    return ['success' => false, 'error' => 'Version name must be in format X.Y.Z (e.g., 1.0.0)'];
                }
                $appUpdates['version_name'] = $args['versionName'];
            }

            if (isset($args['versionCode'])) {
                $versionCode = (int) $args['versionCode'];
                if ($versionCode < 1) {
                    return ['success' => false, 'error' => 'Version code must be a positive integer'];
                }
                $appUpdates['version_code'] = $versionCode;
            }

            if (isset($args['screenOrientation'])) {
                if (! in_array($args['screenOrientation'], ['system', 'portrait', 'landscape'])) {
                    return ['success' => false, 'error' => 'Invalid screen orientation'];
                }
                $updates['screen_orientation'] = $args['screenOrientation'];
            }

            if (isset($args['appLanguage'])) {
                $updates['app_language'] = $args['appLanguage'];
            }

            // Check if any valid parameters were provided
            if (empty($appUpdates) && empty($updates)) {
                return [
                    'success' => false,
                    'error' => 'No valid parameters provided. Use: url, appName, versionName, versionCode, screenOrientation, or appLanguage',
                ];
            }

            if (! empty($appUpdates)) {
                $app->update($appUpdates);
            }

            if (! empty($updates)) {
                $config->update($updates);
            }

            return [
                'success' => true,
                'message' => 'General settings updated successfully',
                'applied' => array_merge($appUpdates, $updates),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Failed to update settings: '.$e->getMessage()];
        }
    }

    /**
     * AI Function: Set color scheme
     */
    private function aiSetColorScheme(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        $colorFields = [
            'textColorLight' => 'text_color_light',
            'textColorDark' => 'text_color_dark',
            'progressIndicatorColor' => 'progress_indicator_color',
            'navTabIconColorLight' => 'nav_tab_icon_color_light',
            'navTabIconColorDark' => 'nav_tab_icon_color_dark',
            'activeTabColorLight' => 'active_tab_color_light',
            'activeTabColorDark' => 'active_tab_color_dark',
        ];

        foreach ($colorFields as $arg => $field) {
            if (isset($args[$arg])) {
                if (! preg_match($hexPattern, $args[$arg])) {
                    return ['success' => false, 'error' => "Invalid color format for {$arg}"];
                }
                $updates[$field] = $args[$arg];
            }
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Color scheme updated successfully',
            'applied' => $updates,
        ];
    }

    /**
     * AI Function: Set drawer customization
     */
    private function aiSetDrawerCustomization(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        if (isset($args['mode'])) {
            if (! in_array($args['mode'], ['default', 'color', 'image'])) {
                return ['success' => false, 'error' => 'Invalid drawer mode'];
            }
            $updates['drawer_mode'] = $args['mode'];
        }

        if (isset($args['backgroundColor'])) {
            if (! preg_match($hexPattern, $args['backgroundColor'])) {
                return ['success' => false, 'error' => 'Invalid background color format'];
            }
            $updates['drawer_background_color'] = $args['backgroundColor'];
        }

        if (isset($args['logoEnabled'])) {
            $updates['drawer_logo_enabled'] = (bool) $args['logoEnabled'];
        }

        if (isset($args['logoSize'])) {
            if ($args['logoSize'] < 40 || $args['logoSize'] > 200) {
                return ['success' => false, 'error' => 'Logo size must be between 40 and 200'];
            }
            $updates['drawer_logo_size'] = (int) $args['logoSize'];
        }

        if (isset($args['title'])) {
            if (strlen($args['title']) > 255) {
                return ['success' => false, 'error' => 'Title exceeds 255 character limit'];
            }
            $updates['drawer_title'] = $args['title'];
        }

        if (isset($args['subtitle'])) {
            if (strlen($args['subtitle']) > 255) {
                return ['success' => false, 'error' => 'Subtitle exceeds 255 character limit'];
            }
            $updates['drawer_subtitle'] = $args['subtitle'];
        }

        if (isset($args['textTheme'])) {
            if (! in_array($args['textTheme'], ['light', 'dark'])) {
                return ['success' => false, 'error' => 'Invalid text theme'];
            }
            $updates['drawer_text_theme'] = $args['textTheme'];
        }

        // Color fields
        $colorFields = [
            'iconColorLight' => 'drawer_icon_color_light',
            'iconColorDark' => 'drawer_icon_color_dark',
            'activeColorLight' => 'drawer_active_color_light',
            'activeColorDark' => 'drawer_active_color_dark',
        ];

        foreach ($colorFields as $arg => $field) {
            if (isset($args[$arg])) {
                if (! preg_match($hexPattern, $args[$arg])) {
                    return ['success' => false, 'error' => "Invalid {$arg} format"];
                }
                $updates[$field] = $args[$arg];
            }
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Drawer customization updated successfully',
            'applied' => $updates,
        ];
    }

    /**
     * AI Function: Set navigation behaviors
     */
    private function aiSetNavigationBehaviors(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $note = null;

        if (isset($args['pullToRefresh'])) {
            $updates['pull_to_refresh'] = (bool) $args['pullToRefresh'];
        }

        if (isset($args['swipeNavigation'])) {
            // Force-disable for drawer layout to prevent gesture conflicts
            if ($config->layout_template === 'app_bar_drawer') {
                $updates['swipe_navigation'] = false;
                $note = 'Swipe navigation is automatically disabled for drawer layouts.';
            } else {
                $updates['swipe_navigation'] = (bool) $args['swipeNavigation'];
            }
        }

        if (isset($args['preserveTabState'])) {
            $updates['preserve_tab_state'] = (bool) $args['preserveTabState'];
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        $response = [
            'success' => true,
            'message' => 'Navigation behaviors updated successfully',
            'applied' => $updates,
        ];

        if ($note) {
            $response['note'] = $note;
        }

        return $response;
    }

    /**
     * AI Function: Set app bar buttons
     */
    private function aiSetAppBarButtons(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $buttons = $args['buttons'] ?? [];

        if (count($buttons) > 3) {
            return ['success' => false, 'error' => 'Maximum 3 app bar buttons allowed'];
        }

        $validIcons = ['home', 'shopping-cart', 'heart', 'flame', 'newspaper', 'server',
            'mobile', 'globe', 'chart', 'info', 'user', 'settings', 'mail', 'share', 'refresh', 'menu'];
        $validActionTypes = ['internal', 'external', 'share', 'email', 'call', 'hosted_page'];

        $formattedButtons = [];
        foreach ($buttons as $index => $button) {
            if (! isset($button['label']) || strlen($button['label']) > 50) {
                return ['success' => false, 'error' => "Button {$index}: label required (max 50 chars)"];
            }

            $actionType = $button['actionType'] ?? 'internal';
            if (! in_array($actionType, $validActionTypes)) {
                return ['success' => false, 'error' => "Button {$index}: invalid actionType"];
            }

            $icon = $button['icon'] ?? 'settings';
            if (! in_array($icon, $validIcons)) {
                $icon = 'settings'; // Default to settings if invalid icon
            }

            $value = $button['value'] ?? '';

            $formattedButtons[] = [
                'label' => $button['label'],
                'action_type' => $actionType,
                'value' => $value,
                'icon' => $icon,
            ];
        }

        $config->update(['app_bar_buttons' => $formattedButtons]);

        return [
            'success' => true,
            'message' => 'App bar buttons updated successfully',
            'buttonCount' => count($formattedButtons),
        ];
    }

    /**
     * AI Function: Set splashscreen details
     */
    private function aiSetSplashscreenDetails(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        if (isset($args['backgroundType'])) {
            if (! in_array($args['backgroundType'], ['color', 'image'])) {
                return ['success' => false, 'error' => 'Invalid background type'];
            }
            $updates['splashscreen_background_type'] = $args['backgroundType'];
        }

        if (isset($args['backgroundColor'])) {
            if (! preg_match($hexPattern, $args['backgroundColor'])) {
                return ['success' => false, 'error' => 'Invalid background color format'];
            }
            $updates['splashscreen_background'] = $args['backgroundColor'];
        }

        if (isset($args['duration'])) {
            if ($args['duration'] < 1 || $args['duration'] > 10) {
                return ['success' => false, 'error' => 'Duration must be between 1 and 10 seconds'];
            }
            $updates['splashscreen_duration'] = (int) $args['duration'];
        }

        if (isset($args['animation'])) {
            if (! in_array($args['animation'], ['fade', 'slide', 'zoom', 'none'])) {
                return ['success' => false, 'error' => 'Invalid animation type'];
            }
            $updates['splashscreen_animation'] = $args['animation'];
        }

        if (isset($args['logoEnabled'])) {
            $updates['splashscreen_logo_enabled'] = (bool) $args['logoEnabled'];
        }

        if (isset($args['logoSize'])) {
            if ($args['logoSize'] < 60 || $args['logoSize'] > 240) {
                return ['success' => false, 'error' => 'Logo size must be between 60 and 240'];
            }
            $updates['splashscreen_logo_size'] = (int) $args['logoSize'];
        }

        if (isset($args['title'])) {
            if (strlen($args['title']) > 255) {
                return ['success' => false, 'error' => 'Title exceeds 255 character limit'];
            }
            $updates['splashscreen_title'] = $args['title'];
        }

        if (isset($args['subtitle'])) {
            if (strlen($args['subtitle']) > 255) {
                return ['success' => false, 'error' => 'Subtitle exceeds 255 character limit'];
            }
            $updates['splashscreen_subtitle'] = $args['subtitle'];
        }

        if (isset($args['textTheme'])) {
            if (! in_array($args['textTheme'], ['light', 'dark'])) {
                return ['success' => false, 'error' => 'Invalid text theme'];
            }
            $updates['splashscreen_text_theme'] = $args['textTheme'];
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Splashscreen details updated successfully',
            'applied' => $updates,
        ];
    }

    /**
     * AI Function: Set WebView configuration
     */
    private function aiSetWebViewConfiguration(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $buildConfigUpdates = $config->build_config ?? [];

        if (isset($args['userAgent'])) {
            if (strlen($args['userAgent']) > 1000) {
                return ['success' => false, 'error' => 'User agent string exceeds 1000 character limit'];
            }
            $updates['user_agent'] = $args['userAgent'];
        }

        if (isset($args['enableJavascript'])) {
            $buildConfigUpdates['enable_javascript'] = (bool) $args['enableJavascript'];
        }

        if (isset($args['enableDomStorage'])) {
            $buildConfigUpdates['enable_dom_storage'] = (bool) $args['enableDomStorage'];
        }

        if (isset($args['enableZoom'])) {
            $buildConfigUpdates['enable_zoom'] = (bool) $args['enableZoom'];
        }

        if (isset($args['enableCache'])) {
            $buildConfigUpdates['enable_cache'] = (bool) $args['enableCache'];
        }

        $updates['build_config'] = $buildConfigUpdates;
        $config->update($updates);

        return [
            'success' => true,
            'message' => 'WebView configuration updated successfully',
            'applied' => [
                'userAgent' => $updates['user_agent'] ?? $config->user_agent,
                'enableJavascript' => $buildConfigUpdates['enable_javascript'] ?? true,
                'enableDomStorage' => $buildConfigUpdates['enable_dom_storage'] ?? true,
                'enableZoom' => $buildConfigUpdates['enable_zoom'] ?? false,
                'enableCache' => $buildConfigUpdates['enable_cache'] ?? true,
            ],
        ];
    }

    /**
     * AI Function: Set custom code
     */
    private function aiSetCustomCode(App $app, array $args): array
    {
        // Check plan permission
        $user = $app->user;
        if (! $user->plan || ! $user->plan->can_use_custom_code) {
            return [
                'success' => false,
                'error' => 'Your plan does not include custom code access. Please upgrade your plan.',
            ];
        }

        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];

        if (isset($args['customCSS'])) {
            if (strlen($args['customCSS']) > 100000) {
                return ['success' => false, 'error' => 'CSS code exceeds 100KB limit'];
            }
            $updates['custom_css'] = $args['customCSS'];
        }

        if (isset($args['customJavaScript'])) {
            if (strlen($args['customJavaScript']) > 100000) {
                return ['success' => false, 'error' => 'JavaScript code exceeds 100KB limit'];
            }
            $updates['custom_js'] = $args['customJavaScript'];
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Custom code updated successfully',
            'applied' => [
                'cssLength' => strlen($updates['custom_css'] ?? $config->custom_css ?? ''),
                'jsLength' => strlen($updates['custom_js'] ?? $config->custom_js ?? ''),
            ],
        ];
    }

    /**
     * AI Function: Set signing key
     */
    private function aiSetSigningKey(App $app, array $args): array
    {
        $config = $app->androidWebViewConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $keystoreId = $args['keystoreId'] ?? null;

        // Clear keystore if 0 or null
        if (! $keystoreId || $keystoreId === 0) {
            $config->update(['keystore_id' => null]);

            return [
                'success' => true,
                'message' => 'Signing key cleared. A keystore is required for release builds.',
            ];
        }

        // Verify keystore belongs to user
        $keystore = Keystore::where('id', $keystoreId)
            ->where('user_id', $app->user_id)
            ->first();

        if (! $keystore) {
            return ['success' => false, 'error' => 'Keystore not found or does not belong to you'];
        }

        $config->update(['keystore_id' => $keystoreId]);

        return [
            'success' => true,
            'message' => "Signing key set to '{$keystore->name}'",
            'keystore' => [
                'id' => $keystore->id,
                'name' => $keystore->name,
            ],
        ];
    }

    /**
     * AI Function: Set notification configuration
     */
    private function aiSetNotificationConfiguration(App $app, array $args): array
    {
        $enabled = $args['enabled'] ?? false;

        $pushConfig = $app->pushNotificationConfig;

        if (! $pushConfig) {
            // Create new config if it doesn't exist
            PushNotificationConfig::create([
                'app_id' => $app->id,
                'enabled' => $enabled,
            ]);

            return [
                'success' => true,
                'message' => $enabled
                    ? 'Notifications enabled. Please upload Firebase configuration files via the app editor.'
                    : 'Notifications disabled.',
                'requiresFirebaseSetup' => $enabled,
            ];
        }

        $pushConfig->update(['enabled' => $enabled]);

        // Check if Firebase files are configured
        $hasFirebaseConfig = ! empty($pushConfig->google_services_file) &&
                             ! empty($pushConfig->firebase_credentials_file);

        return [
            'success' => true,
            'message' => $enabled
                ? ($hasFirebaseConfig ? 'Notifications enabled and configured.' : 'Notifications enabled but Firebase setup incomplete.')
                : 'Notifications disabled.',
            'hasFirebaseConfig' => $hasFirebaseConfig,
        ];
    }

    /**
     * Get HTML template for hosted page
     */
    private function getPageTemplateHtml(string $template, string $title): string
    {
        return match ($template) {
            'about-us' => "<h1>{$title}</h1>\n<p>Welcome to our company! We are dedicated to providing excellent service.</p>\n<h2>Our Mission</h2>\n<p>Add your mission statement here.</p>\n<h2>Our Team</h2>\n<p>Introduce your team members here.</p>",
            'contact' => "<h1>{$title}</h1>\n<p>Get in touch with us!</p>\n<h2>Contact Information</h2>\n<p><strong>Email:</strong> contact@example.com</p>\n<p><strong>Phone:</strong> +1 (555) 123-4567</p>\n<p><strong>Address:</strong> 123 Main St, City, Country</p>",
            'menu' => "<h1>{$title}</h1>\n<h2>Appetizers</h2>\n<ul>\n<li>Item 1 - \$9.99</li>\n<li>Item 2 - \$12.99</li>\n</ul>\n<h2>Main Course</h2>\n<ul>\n<li>Item 1 - \$18.99</li>\n<li>Item 2 - \$24.99</li>\n</ul>",
            'terms' => "<h1>{$title}</h1>\n<p>Last updated: ".date('F j, Y')."</p>\n<h2>1. Terms of Use</h2>\n<p>By using this application, you agree to these terms.</p>\n<h2>2. Privacy Policy</h2>\n<p>Your privacy is important to us.</p>\n<h2>3. Disclaimer</h2>\n<p>Add your disclaimer here.</p>",
            'faq' => "<h1>{$title}</h1>\n<h2>Frequently Asked Questions</h2>\n<h3>Q: How do I get started?</h3>\n<p>A: Simply download the app and follow the setup instructions.</p>\n<h3>Q: How can I contact support?</h3>\n<p>A: You can reach us at support@example.com.</p>",
            default => "<h1>{$title}</h1>\n<p>Add your content here.</p>",
        };
    }
}
