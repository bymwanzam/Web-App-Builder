<?php

namespace App\Plugins\Shared\Builders;

use App\DataTransferObjects\BuildConfigContext;
use App\Models\AndroidWebViewConfig;
use App\Models\App;
use App\Models\AppBuild;
use App\Plugins\Shared\Models\AppPage;

/**
 * AndroidBuildConfigBuilder
 *
 * Builds the complete configuration array required by the Android build server.
 * Organizes the 52+ configuration fields into logical categories:
 * - General settings (app name, URL, package, orientation, language)
 * - Design settings (theme, colors, layout)
 * - Drawer settings (background, logo, title)
 * - Custom code (CSS/JS - plan gated)
 * - Icon settings
 * - Splashscreen settings
 * - Navigation settings (tabs, drawer, buttons)
 * - Permissions
 * - Signing settings (keystore for release builds)
 * - Versioning (version name, version code)
 * - Push notifications (Firebase)
 *
 * This builder can be used by any Android platform plugin.
 */
class AndroidBuildConfigBuilder
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
     * Android WebView configuration
     */
    protected AndroidWebViewConfig $config;

    /**
     * Create a new Android build config builder instance
     *
     * @param  BuildConfigContext  $context  The build context
     */
    public function __construct(BuildConfigContext $context)
    {
        $this->context = $context;
        $this->app = $context->app;
        $this->build = $context->build;
        $this->config = $this->app->androidWebViewConfig;

        if (! $this->config) {
            throw new \Exception('Android WebView configuration not found for app: '.$this->app->name);
        }
    }

    /**
     * Build the complete Android configuration array
     *
     * @return array<string, mixed> Complete build configuration
     */
    public function build(): array
    {
        return array_merge(
            $this->buildGeneralConfig(),
            $this->buildDesignConfig(),
            $this->buildDrawerConfig(),
            $this->buildWebViewConfig(),
            $this->buildCustomCodeConfig(),
            $this->buildIconConfig(),
            $this->buildSplashscreenConfig(),
            $this->buildNavigationConfig(),
            $this->buildPermissionsConfig(),
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
            'website_url' => $this->config->url,
            'app_id' => $this->config->package_name ?? 'com.example.app',
            'orientation' => $this->config->screen_orientation ?? 'portrait',
            'language' => $this->config->app_language ?? 'en',
            'user_agent' => $this->config->user_agent ?? '',
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
            'theme_color' => $this->config->theme_color ?? '#3B82F6',
            'text_color_light' => $this->config->text_color_light ?? '#000000',
            'text_color_dark' => $this->config->text_color_dark ?? '#FFFFFF',
            'enable_dynamic_colors' => $this->config->enable_dynamic_colors ?? false,
            'progress_indicator' => $this->config->progress_indicator ?? 'linear',
            'progress_color' => $this->config->progress_indicator_color ?? '#3B82F6',
            'show_page_title' => $this->config->show_app_bar_title ?? true,
            'tab_icon_color_light' => $this->config->nav_tab_icon_color_light ?? '#6B7280',
            'tab_icon_color_dark' => $this->config->nav_tab_icon_color_dark ?? '#6B7280',
            'active_tab_color_light' => $this->config->active_tab_color_light ?? '#3B82F6',
            'active_tab_color_dark' => $this->config->active_tab_color_dark ?? '#3B82F6',
            'layout_template' => $this->config->layout_template ?? 'appbar',
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
            'drawer_background_image' => $this->config->drawer_background_image ?? '',
            'drawer_logo_enabled' => $this->config->drawer_logo_enabled ?? false,
            'drawer_logo_light' => $this->config->drawer_logo_light ?? ($this->config->drawer_logo ?? ''),
            'drawer_logo_dark' => $this->config->drawer_logo_dark ?? ($this->config->drawer_logo ?? ''),
            'drawer_logo_size' => $this->config->drawer_logo_size ?? 80,
            'drawer_title' => $this->config->drawer_title ?? '',
            'drawer_subtitle' => $this->config->drawer_subtitle ?? '',
            'drawer_text_color_mode' => $this->config->drawer_text_theme ?? 'dark',
            // Drawer item colors (matching bottom nav pattern)
            'drawer_icon_color_light' => $this->config->drawer_icon_color_light ?? '#6B7280',
            'drawer_icon_color_dark' => $this->config->drawer_icon_color_dark ?? '#9CA3AF',
            'drawer_active_color_light' => $this->config->drawer_active_color_light ?? ($this->config->theme_color ?? '#3B82F6'),
            'drawer_active_color_dark' => $this->config->drawer_active_color_dark ?? ($this->config->theme_color ?? '#3B82F6'),
        ];
    }

    /**
     * Build WebView behavior settings
     *
     * Extracts WebView settings from the build_config JSON field where
     * AppWebViewSettings stores them.
     *
     * @return array<string, mixed> WebView configuration fields
     */
    protected function buildWebViewConfig(): array
    {
        $buildConfig = $this->config->build_config ?? [];

        return [
            'enable_javascript' => $buildConfig['enable_javascript'] ?? true,
            'enable_dom_storage' => $buildConfig['enable_dom_storage'] ?? true,
            'enable_zoom' => $buildConfig['enable_zoom'] ?? false,
            'enable_cache' => $buildConfig['enable_cache'] ?? true,
        ];
    }

    /**
     * Build custom code settings (plan-gated feature)
     *
     * @return array<string, mixed> Custom code configuration fields
     */
    protected function buildCustomCodeConfig(): array
    {
        // Only include custom code if user's plan allows it
        $canUseCustomCode = $this->app->user->plan && $this->app->user->plan->can_use_custom_code;

        return [
            'custom_css' => $canUseCustomCode ? ($this->config->custom_css ?? '') : '',
            'custom_javascript' => $canUseCustomCode ? ($this->config->custom_js ?? '') : '',
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
            'icon_zip_url' => $this->config->uploaded_icon_zip ? url("storage/{$this->config->uploaded_icon_zip}") : '',
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
                ? ($this->config->splashscreen_image ?? '')
                : ($this->config->splashscreen_background ?? '#3B82F6'),
            'splash_logo_enabled' => $this->config->splashscreen_logo_enabled ?? false,
            'splash_logo' => $this->config->splashscreen_logo ?? '',
            'splash_logo_size' => $this->config->splashscreen_logo_size ?? 120,
            'splash_title' => $this->config->splashscreen_title ?? '',
            'splash_subtitle' => $this->config->splashscreen_subtitle ?? '',
            'splash_text_color_mode' => $this->config->splashscreen_text_theme ?? 'light',
            'splash_delay' => $this->config->splashscreen_duration ?? 2,
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
            'pull_to_refresh' => $this->config->pull_to_refresh ?? true,
            // Force-disable swipe navigation for drawer layout to prevent gesture conflicts
            'swipe_navigation' => $this->config->layout_template === 'app_bar_drawer' ? false : ($this->config->swipe_navigation ?? false),
            'preserve_tab_state' => $this->config->preserve_tab_state ?? false,
        ];
    }

    /**
     * Build Android permissions configuration
     *
     * @return array<string, mixed> Permissions configuration fields
     */
    protected function buildPermissionsConfig(): array
    {
        return [
            'permission_location' => $this->config->permission_location ?? false,
            'permission_camera' => $this->config->permission_camera ?? false,
            'permission_storage' => $this->config->permission_storage ?? false,
            'permission_record_audio' => $this->config->permission_record_audio ?? false,
            'permission_read_contacts' => $this->config->permission_read_contacts ?? false,
            'permission_vibrate' => $this->config->permission_vibrate ?? false,
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
        return [
            'firebase_enabled' => $this->app->pushNotificationConfig?->enabled ?? false,
            'google_services_json' => $this->getGoogleServicesContent(),
        ];
    }

    /**
     * Transform navigation items for builder compatibility
     *
     * Renames 'value' field to 'url' as expected by Android builder.
     * For hosted_page action types, converts the page ID to the hosted page URL
     * and changes the action_type to 'internal' so it loads in the WebView.
     *
     * @param  array<int, array<string, mixed>>  $items  Navigation items to transform
     * @return array<int, array<string, mixed>> Transformed navigation items
     */
    protected function transformNavigationItems(array $items): array
    {
        return array_filter(array_map(function ($item) {
            // Handle hosted_page action type
            if (isset($item['action_type']) && $item['action_type'] === 'hosted_page') {
                $pageId = $item['value'] ?? null;
                if ($pageId) {
                    $page = AppPage::where('id', $pageId)
                        ->where('app_id', $this->app->id)
                        ->where('status', 'published')
                        ->first();

                    if ($page) {
                        // Convert to internal URL pointing to hosted page
                        $item['action_type'] = 'internal';
                        $item['url'] = route('hosted-page.show', [$this->app->id, $page->slug]);
                        unset($item['value']);
                    } else {
                        // Skip items with invalid or unpublished pages
                        return null;
                    }
                } else {
                    // Skip items without a page ID
                    return null;
                }
            } else {
                // Rename 'value' to 'url' for builder compatibility
                if (isset($item['value'])) {
                    $item['url'] = $item['value'];
                    unset($item['value']);
                }
            }

            return $item;
        }, $items));
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
