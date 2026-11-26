<?php

namespace App\Plugins\Platforms\AndroidWordPress;

use App\Contracts\PlatformPlugin;
use App\DataTransferObjects\BuildConfigContext;
use App\Models\AndroidWordPressConfig;
use App\Models\App;
use App\Models\AppBuild;
use App\Models\Keystore;
use App\Plugins\Platforms\AndroidWordPress\Livewire\AppCreationForm;
use App\Plugins\Shared\Builders\AndroidWordPressBuildConfigBuilder;
use App\Plugins\Shared\Models\AppPage;
use App\Plugins\Shared\Traits\AndroidPlatformTrait;
use App\Traits\HasAiFunctions;

class AndroidWordPressPlatform implements PlatformPlugin
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
        return 'Android WordPress';
    }

    public function getDescription(): string
    {
        return 'Build native Android apps for WordPress.com sites using the REST API';
    }

    public function getType(): string
    {
        return 'platform';
    }

    public function getIcon(): string
    {
        return 'plugins/android-wordpress/icon.jpg';
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
        // Platform is always configured. OAuth is optional (enables user auth features).
        return true;
    }

    public function validateConfig(array $config): void
    {
        // OAuth configuration is optional - validates if provided
        if (! empty($config['oauth_client_id']) && empty($config['oauth_client_secret'])) {
            throw new \Exception('OAuth Client Secret is required when Client ID is provided');
        }

        if (! empty($config['oauth_redirect_scheme'])) {
            if (! preg_match('/^[a-z][a-z0-9-]*$/', $config['oauth_redirect_scheme'])) {
                throw new \Exception('OAuth Redirect Scheme must be lowercase alphanumeric with hyphens');
            }
        }
    }

    /**
     * Platform Plugin Interface Methods
     */
    public function getPlatformId(): string
    {
        return 'android-wordpress';
    }

    public function getCreationFormView(): string
    {
        return 'plugins.android-wordpress::creation-form';
    }

    public function getEditorView(): string
    {
        return 'plugins.android-wordpress::editor.app-editor';
    }

    public function validateForBuild(App $app): array
    {
        $errors = [];
        $config = $app->androidWordPressConfig;

        if (! $config) {
            $errors[] = 'Android WordPress configuration not found';

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

        // Check keystore for release builds
        $keystoreId = $config->keystore_id;
        if (empty($keystoreId)) {
            $errors[] = 'Keystore is required for signing the app. Please upload a keystore in the Signing Keys section.';
        }

        // Check app icon
        if (empty($config->app_icon)) {
            $errors[] = 'App icon is required';
        }

        // Check WordPress site URL
        if (empty($config->site_url)) {
            $errors[] = 'WordPress site URL is required';
        } elseif (! filter_var($config->site_url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid WordPress site URL format';
        }

        // Validate OAuth is configured if user auth is enabled
        if ($config->enable_user_auth) {
            if (empty($this->config['oauth_client_id']) || empty($this->config['oauth_client_secret'])) {
                $errors[] = 'OAuth credentials must be configured in platform settings to enable user authentication';
            }
        }

        return $errors;
    }

    public function prepareBuildConfig(BuildConfigContext $context): array
    {
        $builder = new AndroidWordPressBuildConfigBuilder($context, $this->config);

        return $builder->build();
    }

    public function getCreationFormComponent(): ?string
    {
        return AppCreationForm::class;
    }

    public function createConfig(App $app, array $formData): void
    {
        // Use package name from form data (already validated for uniqueness)
        $packageName = $formData['package_name'] ?? null;

        if (! $packageName) {
            // Fallback: Generate unique package name from app name
            $packageName = $this->generatePackageId($app->name);
            $counter = 1;

            while (AndroidWordPressConfig::where('package_name', $packageName)->exists()) {
                $counter++;
                $packageName = $this->generatePackageId($app->name, $counter);
            }
        }

        // Create platform-specific config with form data
        AndroidWordPressConfig::create([
            'app_id' => $app->id,
            'site_url' => $formData['site_url'] ?? '',
            'package_name' => $packageName,
            'theme_color' => $formData['theme_color'] ?? '#21759B', // WordPress blue
            'text_color_light' => '#1E1E1E',
            'text_color_dark' => '#FFFFFF',
            'layout_template' => $formData['layout_template'] ?? 'app_bar',
            'layout_style' => $formData['layout_style'] ?? 'list',
            'screen_orientation' => 'system',
            'app_language' => 'en',
            // Features defaults
            'enable_comments' => true,
            'enable_search' => true,
            'enable_bookmarks' => true,
            'enable_offline_reading' => false,
            'enable_share' => true,
            'enable_user_auth' => false,
            // Content defaults
            'posts_per_page' => 10,
            'show_author' => true,
            'show_date' => true,
            'show_excerpt' => true,
            'show_featured_image' => true,
            'show_categories' => true,
            'show_reading_time' => false,
            // UI colors inherit theme_color
            'nav_tab_icon_color_light' => '#6B7280',
            'nav_tab_icon_color_dark' => '#6B7280',
            'active_tab_color_light' => $formData['theme_color'] ?? '#21759B',
            'active_tab_color_dark' => $formData['theme_color'] ?? '#21759B',
            'splashscreen_background' => $formData['theme_color'] ?? '#21759B',
            'splashscreen_duration' => 3,
            'pull_to_refresh' => true,
            'swipe_navigation' => false,
        ]);
    }

    public function updateConfig(App $app, array $formData): void
    {
        $config = $app->androidWordPressConfig;

        if (! $config) {
            throw new \Exception('Android WordPress configuration not found for this app');
        }

        $config->update([
            'site_url' => $formData['site_url'] ?? $config->site_url,
            'theme_color' => $formData['theme_color'] ?? $config->theme_color,
            'layout_template' => $formData['layout_template'] ?? $config->layout_template,
        ]);
    }

    /**
     * New Platform Plugin Interface Methods
     */
    public function getBuildModalComponent(): string
    {
        return 'plugins.android-wordpress.build-modal';
    }

    public function getAppCardView(): string
    {
        return 'plugins.android-wordpress::app-card';
    }

    public function getPreviewPanelComponent(): ?string
    {
        return 'plugins.android-wordpress.app-preview-panel';
    }

    public function getBuildTableComponent(): string
    {
        return 'plugins.android-wordpress.builds-table';
    }

    public function getDeleteBuildModalComponent(): string
    {
        return 'plugins.android-wordpress.delete-build-modal';
    }

    public function getBuildLogsModalComponent(): string
    {
        return 'plugins.android-wordpress.build-logs-modal';
    }

    public function getBuildDownloadModalComponent(): string
    {
        return 'plugins.android-wordpress.build-download-modal';
    }

    public function getConfigModelClass(): string
    {
        return AndroidWordPressConfig::class;
    }

    /**
     * Appetize.io Preview Support
     */
    public function supportsAppetize(): bool
    {
        return true;
    }

    public function getAppetizeConfig(AppBuild $build): array
    {
        $config = $build->app->androidWordPressConfig;

        return [
            'device' => 'pixel7',
            'osVersion' => '14.0',
            'appearance' => $config->enable_dynamic_colors ? 'dark' : 'light',
            'deviceColor' => 'white',
            'language' => 'en',
        ];
    }

    public function getAppetizePlatformId(): string
    {
        return 'android';
    }

    public function getAppetizeFileType(AppBuild $build): string
    {
        return $build->build_format;
    }

    public function getAppetizeDefaultDevice(): string
    {
        return 'pixel7';
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

            // WordPress-specific content settings
            'setContentSettings' => $this->aiSetContentSettings($app, $args),
            'setFeaturesSettings' => $this->aiSetFeaturesSettings($app, $args),

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
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return 'No configuration found for this app.';
        }

        $context = "=== CURRENT APP CONFIGURATION ===\n\n";

        // App basics
        $context .= "## App Information\n";
        $context .= "- Name: {$app->name}\n";
        $context .= "- Version: {$app->version_name} (code: {$app->version_code})\n";
        $context .= "- Package: {$config->package_name}\n";
        $context .= "- WordPress Site: {$config->site_url}\n\n";

        // General settings
        $context .= "## General Settings\n";
        $context .= "- Screen Orientation: {$config->screen_orientation}\n";
        $context .= "- Language: {$config->app_language}\n\n";

        // Design
        $context .= "## Design\n";
        $context .= "- Theme Color: {$config->theme_color}\n";
        $context .= "- Layout: {$config->layout_template}\n";
        $context .= "- Layout Style: {$config->layout_style}\n";
        $context .= '- Dynamic Colors: '.($config->enable_dynamic_colors ? 'Yes' : 'No')."\n\n";

        // Content Settings
        $context .= "## Content Settings\n";
        $context .= "- Posts Per Page: {$config->posts_per_page}\n";
        $context .= '- Show Author: '.($config->show_author ? 'Yes' : 'No')."\n";
        $context .= '- Show Date: '.($config->show_date ? 'Yes' : 'No')."\n";
        $context .= '- Show Excerpt: '.($config->show_excerpt ? 'Yes' : 'No')."\n";
        $context .= '- Show Featured Image: '.($config->show_featured_image ? 'Yes' : 'No')."\n";
        $context .= '- Show Categories: '.($config->show_categories ? 'Yes' : 'No')."\n";
        $context .= '- Show Reading Time: '.($config->show_reading_time ? 'Yes' : 'No')."\n\n";

        // Features
        $context .= "## Features\n";
        $context .= '- Comments: '.($config->enable_comments ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Search: '.($config->enable_search ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Bookmarks: '.($config->enable_bookmarks ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Offline Reading: '.($config->enable_offline_reading ? 'Enabled' : 'Disabled')."\n";
        $context .= '- Share: '.($config->enable_share ? 'Enabled' : 'Disabled')."\n";
        $context .= '- User Auth: '.($config->enable_user_auth ? 'Enabled' : 'Disabled')."\n\n";

        // Push Notifications
        $context .= "## Push Notifications\n";
        $context .= '- Enabled: '.($config->enable_push_notifications ? 'Yes' : 'No')."\n";
        $context .= '- Google Services File: '.($config->google_services_json ? 'Uploaded' : 'Not uploaded')."\n";
        $context .= '- Firebase Service Account: '.($config->firebase_service_account ? 'Uploaded' : 'Not uploaded')."\n";
        $hasFirebaseConfig = ! empty($config->google_services_json) && ! empty($config->firebase_service_account);
        $context .= '- Firebase Ready: '.($hasFirebaseConfig ? 'Yes' : 'No')."\n\n";

        // Navigation
        $context .= "## Navigation\n";
        if ($config->layout_template === 'app_bar_tabs') {
            $navItems = $config->navigation_items ?? [];
            $context .= '- Tabs: '.count($navItems)."\n";
            foreach ($navItems as $item) {
                $label = $item['label'] ?? 'Untitled';
                $actionType = $item['action_type'] ?? 'category';
                $value = $item['value'] ?? '';
                $context .= "  - {$label} ({$actionType}): {$value}\n";
            }
        }

        if ($config->layout_template === 'app_bar_drawer') {
            $drawerItems = $config->drawer_items ?? [];
            $context .= '- Drawer Items: '.count($drawerItems)."\n";
            foreach ($drawerItems as $item) {
                $label = $item['label'] ?? 'Untitled';
                $actionType = $item['action_type'] ?? 'category';
                $value = $item['value'] ?? '';
                $context .= "  - {$label} ({$actionType}): {$value}\n";
            }
        }

        $appBarButtons = $config->app_bar_buttons ?? [];
        $context .= '- App Bar Buttons: '.count($appBarButtons)."\n";
        $context .= '- Pull to Refresh: '.($config->pull_to_refresh ? 'Yes' : 'No')."\n\n";

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

    // =========================================================================
    // AI Function Implementations
    // =========================================================================

    private function aiGetAppConfig(App $app): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $pages = AppPage::where('app_id', $app->id)->get();
        $latestBuild = AppBuild::where('app_id', $app->id)->latest()->first();

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
                    'siteUrl' => $config->site_url,
                    'siteId' => $config->site_id,
                    'apiNamespace' => $config->api_namespace,
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
                    'layoutStyle' => $config->layout_style,
                ],
                'content' => [
                    'postsPerPage' => $config->posts_per_page,
                    'showAuthor' => $config->show_author,
                    'showDate' => $config->show_date,
                    'showExcerpt' => $config->show_excerpt,
                    'showFeaturedImage' => $config->show_featured_image,
                    'showCategories' => $config->show_categories,
                    'showReadingTime' => $config->show_reading_time,
                ],
                'features' => [
                    'enableComments' => $config->enable_comments,
                    'enableSearch' => $config->enable_search,
                    'enableBookmarks' => $config->enable_bookmarks,
                    'enableOfflineReading' => $config->enable_offline_reading,
                    'enableShare' => $config->enable_share,
                    'enableUserAuth' => $config->enable_user_auth,
                ],
                'notifications' => [
                    'enabled' => $config->enable_push_notifications ?? false,
                    'hasGoogleServices' => ! empty($config->google_services_json),
                    'hasFirebaseCredentials' => ! empty($config->firebase_service_account),
                    'firebaseReady' => ! empty($config->google_services_json) && ! empty($config->firebase_service_account),
                ],
                'navigation' => [
                    'items' => $config->navigation_items ?? [],
                    'drawerItems' => $config->drawer_items ?? [],
                    'appBarButtons' => $config->app_bar_buttons ?? [],
                    'pullToRefresh' => $config->pull_to_refresh,
                    'swipeNavigation' => $config->swipe_navigation,
                    'preserveTabState' => $config->preserve_tab_state,
                ],
                'assets' => [
                    'hasIcon' => ! empty($config->app_icon),
                    'hasKeystore' => ! empty($config->keystore_id),
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

    private function aiSetGeneralSettings(App $app, array $args): array
    {
        try {
            $config = $app->androidWordPressConfig;
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

            if (isset($args['siteUrl'])) {
                if (! filter_var($args['siteUrl'], FILTER_VALIDATE_URL)) {
                    return ['success' => false, 'error' => 'Invalid site URL format'];
                }
                $updates['site_url'] = $args['siteUrl'];
            }

            if (isset($args['versionName'])) {
                if (! preg_match('/^\d+\.\d+\.\d+$/', $args['versionName'])) {
                    return ['success' => false, 'error' => 'Version name must be in format X.Y.Z'];
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

            if (empty($appUpdates) && empty($updates)) {
                return ['success' => false, 'error' => 'No valid parameters provided'];
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

    private function aiSetDesignSettings(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
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
            $updates['active_tab_color_light'] = $args['themeColor'];
            $updates['active_tab_color_dark'] = $args['themeColor'];
        }

        if (isset($args['enableDynamicColors'])) {
            $updates['enable_dynamic_colors'] = (bool) $args['enableDynamicColors'];
        }

        if (isset($args['layoutTemplate'])) {
            $validTemplates = ['app_bar', 'app_bar_tabs', 'app_bar_drawer'];
            if (in_array($args['layoutTemplate'], $validTemplates)) {
                $updates['layout_template'] = $args['layoutTemplate'];
            }
        }

        if (isset($args['layoutStyle'])) {
            $validStyles = ['list', 'grid', 'magazine'];
            if (in_array($args['layoutStyle'], $validStyles)) {
                $updates['layout_style'] = $args['layoutStyle'];
            }
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

    private function aiSetColorScheme(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        $colorFields = [
            'textColorLight' => 'text_color_light',
            'textColorDark' => 'text_color_dark',
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

    private function aiSetContentSettings(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];

        if (isset($args['postsPerPage'])) {
            $ppp = (int) $args['postsPerPage'];
            if ($ppp < 5 || $ppp > 100) {
                return ['success' => false, 'error' => 'Posts per page must be between 5 and 100'];
            }
            $updates['posts_per_page'] = $ppp;
        }

        $boolFields = [
            'showAuthor' => 'show_author',
            'showDate' => 'show_date',
            'showExcerpt' => 'show_excerpt',
            'showFeaturedImage' => 'show_featured_image',
            'showCategories' => 'show_categories',
            'showReadingTime' => 'show_reading_time',
        ];

        foreach ($boolFields as $arg => $field) {
            if (isset($args[$arg])) {
                $updates[$field] = (bool) $args[$arg];
            }
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Content settings updated successfully',
            'applied' => $updates,
        ];
    }

    private function aiSetFeaturesSettings(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];

        $boolFields = [
            'enableComments' => 'enable_comments',
            'enableSearch' => 'enable_search',
            'enableBookmarks' => 'enable_bookmarks',
            'enableOfflineReading' => 'enable_offline_reading',
            'enableShare' => 'enable_share',
            'enableUserAuth' => 'enable_user_auth',
        ];

        foreach ($boolFields as $arg => $field) {
            if (isset($args[$arg])) {
                $updates[$field] = (bool) $args[$arg];
            }
        }

        // Validate OAuth is configured if enabling user auth
        if (isset($updates['enable_user_auth']) && $updates['enable_user_auth']) {
            if (empty($this->config['oauth_client_id']) || empty($this->config['oauth_client_secret'])) {
                return [
                    'success' => false,
                    'error' => 'OAuth credentials must be configured in platform settings to enable user authentication',
                ];
            }
        }

        if (! empty($updates)) {
            $config->update($updates);
        }

        return [
            'success' => true,
            'message' => 'Features settings updated successfully',
            'applied' => $updates,
        ];
    }

    private function aiSetNavigationItems(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $items = $args['items'] ?? [];
        if (count($items) > 5) {
            return ['success' => false, 'error' => 'Maximum 5 navigation items allowed'];
        }

        $validActionTypes = ['category', 'tag', 'search', 'bookmarks', 'hosted_page', 'internal', 'external'];

        $formattedItems = [];
        foreach ($items as $index => $item) {
            $actionType = $item['actionType'] ?? 'category';
            if (! in_array($actionType, $validActionTypes)) {
                $actionType = 'category';
            }

            $formattedItems[] = [
                'id' => $index + 1,
                'label' => $item['label'] ?? 'Tab '.($index + 1),
                'action_type' => $actionType,
                'value' => $item['value'] ?? '',
                'icon' => $item['icon'] ?? 'home',
                'is_home' => $item['isHome'] ?? ($index === 0),
            ];
        }

        $config->update(['navigation_items' => $formattedItems]);

        return [
            'success' => true,
            'message' => 'Navigation items updated successfully',
            'itemCount' => count($formattedItems),
        ];
    }

    private function aiSetDrawerItems(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $items = $args['items'] ?? [];
        if (count($items) > 10) {
            return ['success' => false, 'error' => 'Maximum 10 drawer items allowed'];
        }

        $validActionTypes = ['category', 'tag', 'search', 'bookmarks', 'hosted_page', 'internal', 'external'];

        $formattedItems = [];
        foreach ($items as $index => $item) {
            $actionType = $item['actionType'] ?? 'category';
            if (! in_array($actionType, $validActionTypes)) {
                $actionType = 'category';
            }

            $formattedItems[] = [
                'id' => $index + 1,
                'label' => $item['label'] ?? 'Menu Item '.($index + 1),
                'action_type' => $actionType,
                'value' => $item['value'] ?? '',
                'icon' => $item['icon'] ?? 'newspaper',
                'is_home' => $item['isHome'] ?? ($index === 0),
            ];
        }

        $updates = ['drawer_items' => $formattedItems];

        // Update layout template if enabling drawer
        if (! empty($formattedItems) && $config->layout_template !== 'app_bar_drawer') {
            $updates['layout_template'] = 'app_bar_drawer';
        }

        $config->update($updates);

        return [
            'success' => true,
            'message' => 'Drawer items updated successfully',
            'itemCount' => count($formattedItems),
        ];
    }

    private function aiSetDrawerCustomization(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
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
            $size = (int) $args['logoSize'];
            if ($size < 40 || $size > 200) {
                return ['success' => false, 'error' => 'Logo size must be between 40 and 200'];
            }
            $updates['drawer_logo_size'] = $size;
        }

        if (isset($args['title'])) {
            $updates['drawer_title'] = substr($args['title'], 0, 255);
        }

        if (isset($args['subtitle'])) {
            $updates['drawer_subtitle'] = substr($args['subtitle'], 0, 255);
        }

        if (isset($args['textTheme'])) {
            if (in_array($args['textTheme'], ['light', 'dark'])) {
                $updates['drawer_text_theme'] = $args['textTheme'];
            }
        }

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

    private function aiSetNavigationBehaviors(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];

        if (isset($args['pullToRefresh'])) {
            $updates['pull_to_refresh'] = (bool) $args['pullToRefresh'];
        }

        if (isset($args['swipeNavigation'])) {
            // Force-disable for drawer layout
            if ($config->layout_template === 'app_bar_drawer') {
                $updates['swipe_navigation'] = false;
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

        return [
            'success' => true,
            'message' => 'Navigation behaviors updated successfully',
            'applied' => $updates,
        ];
    }

    private function aiSetAppBarButtons(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $buttons = $args['buttons'] ?? [];
        if (count($buttons) > 3) {
            return ['success' => false, 'error' => 'Maximum 3 app bar buttons allowed'];
        }

        $validActionTypes = ['search', 'bookmarks', 'hosted_page', 'internal', 'external', 'share'];

        $formattedButtons = [];
        foreach ($buttons as $index => $button) {
            if (! isset($button['label']) || strlen($button['label']) > 50) {
                return ['success' => false, 'error' => "Button {$index}: label required (max 50 chars)"];
            }

            $actionType = $button['actionType'] ?? 'search';
            if (! in_array($actionType, $validActionTypes)) {
                $actionType = 'search';
            }

            $formattedButtons[] = [
                'label' => $button['label'],
                'action_type' => $actionType,
                'value' => $button['value'] ?? '',
                'icon' => $button['icon'] ?? 'search',
            ];
        }

        $config->update(['app_bar_buttons' => $formattedButtons]);

        return [
            'success' => true,
            'message' => 'App bar buttons updated successfully',
            'buttonCount' => count($formattedButtons),
        ];
    }

    private function aiSetSplashscreen(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        if (isset($args['backgroundColor'])) {
            if (! preg_match($hexPattern, $args['backgroundColor'])) {
                return ['success' => false, 'error' => 'Invalid background color format'];
            }
            $updates['splashscreen_background'] = $args['backgroundColor'];
        }

        if (isset($args['title'])) {
            $updates['splashscreen_title'] = substr($args['title'], 0, 255);
        }

        if (isset($args['subtitle'])) {
            $updates['splashscreen_subtitle'] = substr($args['subtitle'], 0, 255);
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

    private function aiSetSplashscreenDetails(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $updates = [];
        $hexPattern = '/^#[0-9A-Fa-f]{6}$/';

        if (isset($args['backgroundType'])) {
            if (in_array($args['backgroundType'], ['color', 'image'])) {
                $updates['splashscreen_background_type'] = $args['backgroundType'];
            }
        }

        if (isset($args['backgroundColor'])) {
            if (preg_match($hexPattern, $args['backgroundColor'])) {
                $updates['splashscreen_background'] = $args['backgroundColor'];
            }
        }

        if (isset($args['duration'])) {
            $duration = (int) $args['duration'];
            if ($duration >= 1 && $duration <= 10) {
                $updates['splashscreen_duration'] = $duration;
            }
        }

        if (isset($args['logoEnabled'])) {
            $updates['splashscreen_logo_enabled'] = (bool) $args['logoEnabled'];
        }

        if (isset($args['logoSize'])) {
            $size = (int) $args['logoSize'];
            if ($size >= 60 && $size <= 240) {
                $updates['splashscreen_logo_size'] = $size;
            }
        }

        if (isset($args['title'])) {
            $updates['splashscreen_title'] = substr($args['title'], 0, 255);
        }

        if (isset($args['subtitle'])) {
            $updates['splashscreen_subtitle'] = substr($args['subtitle'], 0, 255);
        }

        if (isset($args['textTheme'])) {
            if (in_array($args['textTheme'], ['light', 'dark'])) {
                $updates['splashscreen_text_theme'] = $args['textTheme'];
            }
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

    private function aiSetSigningKey(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $keystoreId = $args['keystoreId'] ?? null;

        if (! $keystoreId || $keystoreId === 0) {
            $config->update(['keystore_id' => null]);

            return [
                'success' => true,
                'message' => 'Signing key cleared. A keystore is required for release builds.',
            ];
        }

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
     * AI Function: Configure push notification settings
     */
    private function aiSetNotificationConfiguration(App $app, array $args): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $enabled = (bool) ($args['enabled'] ?? false);
        $config->update(['enable_push_notifications' => $enabled]);

        $hasGoogleServices = ! empty($config->google_services_json);
        $hasFirebaseCredentials = ! empty($config->firebase_service_account);
        $hasFirebaseConfig = $hasGoogleServices && $hasFirebaseCredentials;

        if ($enabled) {
            if ($hasFirebaseConfig) {
                $message = 'Push notifications enabled. Firebase is fully configured.';
            } elseif ($hasGoogleServices && ! $hasFirebaseCredentials) {
                $message = 'Push notifications enabled. Firebase service account file is missing - upload it in the Notifications tab.';
            } elseif (! $hasGoogleServices && $hasFirebaseCredentials) {
                $message = 'Push notifications enabled. google-services.json is missing - upload it in the Notifications tab.';
            } else {
                $message = 'Push notifications enabled. Please upload Firebase configuration files in the Notifications tab.';
            }
        } else {
            $message = 'Push notifications disabled.';
        }

        return [
            'success' => true,
            'message' => $message,
            'enabled' => $enabled,
            'hasGoogleServices' => $hasGoogleServices,
            'hasFirebaseCredentials' => $hasFirebaseCredentials,
            'firebaseConfigComplete' => $hasFirebaseConfig,
        ];
    }

    private function aiTriggerBuild(App $app, array $args): array
    {
        $format = $args['format'] ?? 'apk';
        $validFormats = ['apk', 'aab'];

        if (! in_array($format, $validFormats)) {
            return ['success' => false, 'error' => 'Invalid format. Use "apk" or "aab"'];
        }

        $errors = $this->validateForBuild($app);
        if (! empty($errors)) {
            return [
                'success' => false,
                'error' => 'App not ready for build',
                'validationErrors' => $errors,
            ];
        }

        $user = $app->user;
        if (! $user->hasBuildCredits()) {
            return [
                'success' => false,
                'error' => 'Insufficient build credits. Please upgrade your plan or purchase more credits.',
            ];
        }

        $build = AppBuild::create([
            'app_id' => $app->id,
            'platform' => 'android-wordpress',
            'build_format' => $format,
            'build_type' => 'release',
            'version_name' => $app->version_name,
            'version_code' => $app->version_code,
            'status' => 'pending',
            'keystore_id' => $app->androidWordPressConfig->keystore_id,
        ]);

        return [
            'success' => true,
            'message' => "Build started! Format: {$format}",
            'buildId' => $build->id,
            'status' => 'pending',
        ];
    }

    private function aiGetAppStatus(App $app): array
    {
        $config = $app->androidWordPressConfig;
        if (! $config) {
            return ['success' => false, 'error' => 'Configuration not found'];
        }

        $latestBuild = AppBuild::where('app_id', $app->id)->latest()->first();

        return [
            'success' => true,
            'app' => [
                'name' => $app->name,
                'version' => "{$app->version_name} ({$app->version_code})",
                'packageName' => $config->package_name,
                'siteUrl' => $config->site_url,
            ],
            'design' => [
                'themeColor' => $config->theme_color,
                'layoutTemplate' => $config->layout_template,
                'layoutStyle' => $config->layout_style,
            ],
            'features' => [
                'comments' => $config->enable_comments,
                'search' => $config->enable_search,
                'bookmarks' => $config->enable_bookmarks,
                'offlineReading' => $config->enable_offline_reading,
            ],
            'assets' => [
                'hasIcon' => ! empty($config->app_icon),
                'hasKeystore' => ! empty($config->keystore_id),
            ],
            'latestBuild' => $latestBuild ? [
                'format' => $latestBuild->build_format,
                'status' => $latestBuild->status,
                'createdAt' => $latestBuild->created_at->diffForHumans(),
            ] : null,
            'readyForBuild' => empty($this->validateForBuild($app)),
        ];
    }

    private function aiCreateHostedPage(App $app, array $args): array
    {
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages.'];
        }

        $maxPages = $user->plan?->getMaxHostedPagesPerApp();
        if ($maxPages !== null) {
            $currentCount = AppPage::where('app_id', $app->id)->count();
            if ($currentCount >= $maxPages) {
                return ['success' => false, 'error' => "Maximum of {$maxPages} hosted pages reached."];
            }
        }

        $title = $args['title'] ?? null;
        if (! $title || strlen($title) > 100) {
            return ['success' => false, 'error' => 'Title is required (max 100 chars)'];
        }

        $template = $args['template'] ?? 'blank';
        $status = $args['status'] ?? 'draft';

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

    private function aiUpdateHostedPage(App $app, array $args): array
    {
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages.'];
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

    private function aiEditHostedPage(App $app, array $args): array
    {
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages.'];
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
        ];
    }

    private function aiDeleteHostedPage(App $app, array $args): array
    {
        $user = $app->user;
        if (! $user->canUseHostedPages()) {
            return ['success' => false, 'error' => 'Your plan does not include hosted pages.'];
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

    private function getPageTemplateHtml(string $template, string $title): string
    {
        return match ($template) {
            'about-us' => "<h1>{$title}</h1>\n<p>Welcome! Learn more about us here.</p>",
            'contact' => "<h1>{$title}</h1>\n<p>Get in touch with us!</p>\n<p><strong>Email:</strong> contact@example.com</p>",
            'terms' => "<h1>{$title}</h1>\n<p>Last updated: ".date('F j, Y')."</p>\n<p>Your terms and conditions here.</p>",
            'faq' => "<h1>{$title}</h1>\n<h3>Q: How do I get started?</h3>\n<p>A: Download the app and follow the instructions.</p>",
            default => "<h1>{$title}</h1>\n<p>Add your content here.</p>",
        };
    }
}
