<?php

namespace App\Plugins\Shared\Builders;

use App\DataTransferObjects\BuildConfigContext;
use App\Models\AndroidWordPressConfig;
use App\Models\App;
use App\Models\AppBuild;
use App\Plugins\Shared\Models\AppPage;

/**
 * AndroidWordPressBuildConfigBuilder
 *
 * Builds the complete configuration array required by the Android WordPress build server.
 * Organizes configuration fields into logical categories:
 * - General settings (app name, site URL, package, orientation, language)
 * - WordPress settings (API namespace, content settings, features)
 * - Design settings (theme, colors, layout)
 * - Drawer settings (background, logo, title)
 * - Icon settings
 * - Splashscreen settings
 * - Navigation settings (tabs, drawer, buttons)
 * - OAuth settings (for WordPress.com authentication)
 * - Signing settings (keystore for release builds)
 * - Versioning (version name, version code)
 * - Push notifications (Firebase)
 */
class AndroidWordPressBuildConfigBuilder
{
    /**
     * The build context containing app and build models
     */
    protected BuildConfigContext $context;

    /**
     * The app instance (for convenience)
     */
    protected App $app;

    /**
     * The build instance (for convenience)
     */
    protected AppBuild $build;

    /**
     * Android WordPress configuration
     */
    protected AndroidWordPressConfig $config;

    /**
     * Platform configuration (OAuth settings)
     */
    protected array $platformConfig;

    /**
     * Create a new Android WordPress build config builder instance
     *
     * @param  BuildConfigContext  $context  The build context
     * @param  array  $platformConfig  Platform-level configuration (OAuth)
     */
    public function __construct(BuildConfigContext $context, array $platformConfig = [])
    {
        $this->context = $context;
        $this->app = $context->app;
        $this->build = $context->build;
        $this->config = $this->app->androidWordPressConfig;
        $this->platformConfig = $platformConfig;

        if (! $this->config) {
            throw new \Exception('Android WordPress configuration not found for app: '.$this->app->name);
        }
    }

    /**
     * Build the complete Android WordPress configuration array
     *
     * @return array<string, mixed> Complete build configuration
     */
    public function build(): array
    {
        return array_merge(
            $this->buildGeneralConfig(),
            $this->buildWordPressConfig(),
            $this->buildDesignConfig(),
            $this->buildDrawerConfig(),
            $this->buildIconConfig(),
            $this->buildSplashscreenConfig(),
            $this->buildNavigationConfig(),
            $this->buildOAuthConfig(),
            $this->buildSigningConfig(),
            $this->buildVersioningConfig(),
            $this->buildPushNotificationsConfig()
        );
    }

    /**
     * Build general application settings
     *
     * @return array<string, mixed> General configuration fields
     */
    protected function buildGeneralConfig(): array
    {
        return [
            'app_name' => $this->app->name,
            'app_id' => $this->config->package_name ?? 'com.example.app',
            'orientation' => $this->config->screen_orientation ?? 'portrait',
            'language' => $this->config->app_language ?? 'en',
        ];
    }

    /**
     * Build WordPress-specific settings
     *
     * @return array<string, mixed> WordPress configuration fields
     */
    protected function buildWordPressConfig(): array
    {
        return [
            // WordPress connection
            'site_url' => $this->config->site_url,
            'site_id' => $this->config->site_id ?? '',
            'api_namespace' => $this->config->api_namespace ?? 'wp/v2',

            // Content settings
            'post_types' => $this->config->post_types ?? ['post'],
            'categories_filter' => $this->config->categories_filter ?? [],
            'tags_filter' => $this->config->tags_filter ?? [],
            'posts_per_page' => $this->config->posts_per_page ?? 10,
            'show_author' => $this->config->show_author ?? true,
            'show_date' => $this->config->show_date ?? true,
            'show_excerpt' => $this->config->show_excerpt ?? true,
            'show_featured_image' => $this->config->show_featured_image ?? true,
            'show_categories' => $this->config->show_categories ?? true,
            'show_reading_time' => $this->config->show_reading_time ?? false,

            // Feature flags
            'enable_comments' => $this->config->enable_comments ?? true,
            'enable_search' => $this->config->enable_search ?? true,
            'enable_bookmarks' => $this->config->enable_bookmarks ?? true,
            'enable_offline_reading' => $this->config->enable_offline_reading ?? false,
            'enable_share' => $this->config->enable_share ?? true,
            'enable_user_auth' => $this->config->enable_user_auth ?? false,
        ];
    }

    /**
     * Build design and theming settings
     *
     * @return array<string, mixed> Design configuration fields
     */
    protected function buildDesignConfig(): array
    {
        return [
            'theme_color' => $this->config->theme_color ?? '#21759B',
            'text_color_light' => $this->config->text_color_light ?? '#1E1E1E',
            'text_color_dark' => $this->config->text_color_dark ?? '#FFFFFF',
            'enable_dynamic_colors' => $this->config->enable_dynamic_colors ?? true,
            'layout_template' => $this->config->layout_template ?? 'app_bar_tabs',
            'layout_style' => $this->config->layout_style ?? 'list',
            'show_page_title' => $this->config->show_app_bar_title ?? true,
            'tab_icon_color_light' => $this->config->nav_tab_icon_color_light ?? '#666666',
            'tab_icon_color_dark' => $this->config->nav_tab_icon_color_dark ?? '#AAAAAA',
            'active_tab_color_light' => $this->config->active_tab_color_light ?? '#21759B',
            'active_tab_color_dark' => $this->config->active_tab_color_dark ?? '#5BB3D9',
            'progress_indicator' => $this->config->progress_indicator ?? 'linear',
            'progress_color' => $this->config->progress_indicator_color ?? null,
        ];
    }

    /**
     * Build drawer menu settings
     *
     * @return array<string, mixed> Drawer configuration fields
     */
    protected function buildDrawerConfig(): array
    {
        return [
            'drawer_mode' => $this->config->drawer_mode ?? 'default',
            'drawer_background_color' => $this->config->drawer_background_color ?? '#FFFFFF',
            'drawer_background_image' => $this->config->drawer_background_image
                ? url("storage/{$this->config->drawer_background_image}")
                : '',
            'drawer_logo_enabled' => $this->config->drawer_logo_enabled ?? false,
            'drawer_logo_light' => $this->config->drawer_logo_light
                ? url("storage/{$this->config->drawer_logo_light}")
                : ($this->config->drawer_logo ? url("storage/{$this->config->drawer_logo}") : ''),
            'drawer_logo_dark' => $this->config->drawer_logo_dark
                ? url("storage/{$this->config->drawer_logo_dark}")
                : ($this->config->drawer_logo ? url("storage/{$this->config->drawer_logo}") : ''),
            'drawer_logo_size' => $this->config->drawer_logo_size ?? 80,
            'drawer_title' => $this->config->drawer_title ?? '',
            'drawer_subtitle' => $this->config->drawer_subtitle ?? '',
            'drawer_text_color_mode' => $this->config->drawer_text_theme ?? 'light',
            'drawer_icon_color_light' => $this->config->drawer_icon_color_light ?? '#666666',
            'drawer_icon_color_dark' => $this->config->drawer_icon_color_dark ?? '#AAAAAA',
            'drawer_active_color_light' => $this->config->drawer_active_color_light ?? '#21759B',
            'drawer_active_color_dark' => $this->config->drawer_active_color_dark ?? '#5BB3D9',
        ];
    }

    /**
     * Build app icon settings
     *
     * @return array<string, mixed> Icon configuration fields
     */
    protected function buildIconConfig(): array
    {
        return [
            'icon_zip_url' => $this->config->uploaded_icon_zip
                ? url("storage/{$this->config->uploaded_icon_zip}")
                : '',
        ];
    }

    /**
     * Build splashscreen settings
     *
     * @return array<string, mixed> Splashscreen configuration fields
     */
    protected function buildSplashscreenConfig(): array
    {
        return [
            'splash_background_type' => $this->config->splashscreen_background_type ?? 'color',
            'splash_background' => $this->config->splashscreen_background_type === 'image'
                ? ($this->config->splashscreen_image ? url("storage/{$this->config->splashscreen_image}") : '')
                : ($this->config->splashscreen_background ?? '#21759B'),
            'splash_logo_enabled' => $this->config->splashscreen_logo_enabled ?? false,
            'splash_logo' => $this->config->splashscreen_logo
                ? url("storage/{$this->config->splashscreen_logo}")
                : '',
            'splash_logo_size' => $this->config->splashscreen_logo_size ?? 120,
            'splash_title' => $this->config->splashscreen_title ?? '',
            'splash_subtitle' => $this->config->splashscreen_subtitle ?? '',
            'splash_text_color_mode' => $this->config->splashscreen_text_theme ?? 'light',
            'splash_delay' => $this->config->splashscreen_duration ?? 3,
            'splash_animation' => $this->config->splashscreen_animation ?? 'fade',
        ];
    }

    /**
     * Build navigation settings (tabs, drawer items, app bar buttons)
     *
     * @return array<string, mixed> Navigation configuration fields
     */
    protected function buildNavigationConfig(): array
    {
        return [
            'bottom_tabs' => $this->transformNavigationItems($this->config->navigation_items ?? []),
            'drawer_items' => $this->transformNavigationItems($this->config->drawer_items ?? []),
            'appbar_buttons' => $this->transformNavigationItems($this->config->app_bar_buttons ?? []),
            'home_screen' => $this->config->home_screen ?? 'posts',
            'pull_to_refresh' => $this->config->pull_to_refresh ?? true,
            'swipe_navigation' => $this->config->layout_template === 'app_bar_drawer'
                ? false
                : ($this->config->swipe_navigation ?? false),
            'preserve_tab_state' => $this->config->preserve_tab_state ?? true,
        ];
    }

    /**
     * Build OAuth configuration for WordPress.com authentication
     *
     * @return array<string, mixed> OAuth configuration fields
     */
    protected function buildOAuthConfig(): array
    {
        if (! ($this->config->enable_user_auth ?? false)) {
            return [
                'oauth_enabled' => false,
                'oauth_client_id' => '',
                'oauth_client_secret' => '',
                'oauth_redirect_scheme' => '',
                'oauth_scopes' => '',
            ];
        }

        return [
            'oauth_enabled' => true,
            'oauth_client_id' => $this->platformConfig['oauth_client_id'] ?? '',
            'oauth_client_secret' => $this->platformConfig['oauth_client_secret'] ?? '',
            'oauth_redirect_scheme' => $this->platformConfig['oauth_redirect_scheme'] ?? 'appy-wp',
            'oauth_scopes' => 'posts,comments,users',
        ];
    }

    /**
     * Build signing configuration for release builds
     *
     * @return array<string, mixed> Signing configuration fields
     */
    protected function buildSigningConfig(): array
    {
        $isReleaseBuild = $this->build->build_type === 'release';
        $hasKeystore = $isReleaseBuild && $this->build->keystore_id;

        return [
            'keystore_file' => $hasKeystore ? (string) $this->build->keystore_id : '',
            'keystore_password' => $hasKeystore && $this->build->keystore
                ? $this->build->keystore->getPassword()
                : '',
            'key_alias' => $hasKeystore && $this->build->keystore
                ? $this->build->keystore->alias
                : '',
            'key_password' => $hasKeystore && $this->build->keystore
                ? ($this->build->keystore->getKeyPassword() ?? $this->build->keystore->getPassword())
                : '',
        ];
    }

    /**
     * Build versioning configuration
     *
     * @return array<string, mixed> Versioning configuration fields
     */
    protected function buildVersioningConfig(): array
    {
        return [
            'build_format' => $this->build->build_format ?? 'apk',
            'android_version_name' => $this->build->version_name,
            'version_code' => $this->build->version_code,
        ];
    }

    /**
     * Build push notifications configuration
     *
     * @return array<string, mixed> Push notifications configuration fields
     */
    protected function buildPushNotificationsConfig(): array
    {
        $enabled = $this->config->enable_push_notifications ?? false;

        return [
            'firebase_enabled' => $enabled && ($this->app->pushNotificationConfig?->enabled ?? false),
            'google_services_json' => $this->getGoogleServicesContent(),
        ];
    }

    /**
     * Transform navigation items for builder compatibility
     *
     * WordPress navigation items can have action types:
     * - category: Filter posts by category ID
     * - tag: Filter posts by tag ID
     * - search: Show search screen
     * - bookmarks: Show bookmarked posts
     * - hosted_page: Load a hosted HTML page
     * - internal: Load URL in app WebView
     * - external: Open URL in browser
     *
     * @param  array<int, array<string, mixed>>  $items  Navigation items to transform
     * @return array<int, array<string, mixed>> Transformed navigation items
     */
    protected function transformNavigationItems(array $items): array
    {
        return array_values(array_filter(array_map(function ($item) {
            $actionType = $item['action_type'] ?? 'internal';

            // Handle hosted_page action type
            if ($actionType === 'hosted_page') {
                $pageId = $item['value'] ?? null;
                if ($pageId) {
                    $page = AppPage::where('id', $pageId)
                        ->where('app_id', $this->app->id)
                        ->where('status', 'published')
                        ->first();

                    if ($page) {
                        // Convert to URL pointing to hosted page
                        $item['url'] = route('hosted-page.show', [$this->app->id, $page->slug]);
                        unset($item['value']);
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                // Rename 'value' to 'url' for compatibility
                if (isset($item['value'])) {
                    $item['url'] = $item['value'];
                    unset($item['value']);
                }
            }

            return $item;
        }, $items)));
    }

    /**
     * Get the content of google-services.json for the app
     *
     * @return string JSON content or empty string if not configured
     */
    protected function getGoogleServicesContent(): string
    {
        $pushConfig = $this->app->pushNotificationConfig;

        if (! $pushConfig || ! $pushConfig->enabled || ! $pushConfig->google_services_file) {
            return '';
        }

        $path = \Illuminate\Support\Facades\Storage::disk('private')->path($pushConfig->google_services_file);

        if (! file_exists($path)) {
            return '';
        }

        return file_get_contents($path);
    }
}
