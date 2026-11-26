<?php

namespace Tests\Unit\Plugins\Shared\Builders;

use App\DataTransferObjects\BuildConfigContext;
use App\Models\App;
use App\Models\AppBuild;
use App\Models\Keystore;
use App\Models\Plan;
use App\Models\PushNotificationConfig;
use App\Models\User;
use App\Plugins\Shared\Builders\AndroidBuildConfigBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for AndroidBuildConfigBuilder
 *
 * Comprehensive test coverage for Android build configuration generation:
 * - Complete build configuration (52+ fields)
 * - Each configuration category (general, design, drawer, etc.)
 * - Plan-gated features (custom code)
 * - Signing configuration with keystore
 * - Firebase/Push notifications
 * - Navigation transformation
 * - Default values
 */
class AndroidBuildConfigBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected App $testApp;

    protected AppBuild $build;

    protected BuildConfigContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->testApp = App::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test App',
        ]);

        // Update Android WebView config
        $this->testApp->androidWebViewConfig->update([
            'url' => 'https://example.com',
            'package_name' => 'com.test.app',
        ]);

        $this->build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'version_name' => '1.0.0',
            'version_code' => 1,
            'build_format' => 'apk',
            'build_type' => 'debug',
        ]);

        $this->context = new BuildConfigContext($this->testApp, $this->build);
    }

    #[Test]
    public function it_builds_complete_configuration_with_all_required_fields(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertIsArray($config);

        // Verify all required fields are present
        $requiredFields = [
            'app_name', 'website_url', 'app_id', 'orientation', 'language', 'user_agent',
            'theme_color', 'text_color_light', 'text_color_dark', 'enable_dynamic_colors', 'progress_indicator', 'progress_color',
            'show_page_title', 'tab_icon_color_light', 'tab_icon_color_dark', 'active_tab_color_light', 'active_tab_color_dark', 'layout_template',
            'drawer_mode', 'drawer_background_color', 'drawer_background_image', 'drawer_logo_enabled',
            'drawer_logo_light', 'drawer_logo_dark', 'drawer_logo_size', 'drawer_title', 'drawer_subtitle',
            'drawer_text_color_mode', 'drawer_icon_color_light', 'drawer_icon_color_dark',
            'drawer_active_color_light', 'drawer_active_color_dark', 'custom_css', 'custom_javascript', 'icon_zip_url',
            'splash_background_type', 'splash_background', 'splash_logo_enabled', 'splash_logo',
            'splash_logo_size', 'splash_title', 'splash_subtitle', 'splash_text_color_mode',
            'splash_delay', 'splash_animation', 'bottom_tabs', 'drawer_items', 'appbar_buttons',
            'pull_to_refresh', 'swipe_navigation', 'preserve_tab_state',
            'permission_location', 'permission_camera', 'permission_storage', 'permission_record_audio',
            'permission_read_contacts', 'permission_vibrate', 'keystore_file', 'keystore_password',
            'key_alias', 'key_password', 'build_format', 'android_version_name', 'version_code',
            'firebase_enabled', 'google_services_json',
        ];

        foreach ($requiredFields as $field) {
            $this->assertArrayHasKey($field, $config, "Missing required field: {$field}");
        }
    }

    #[Test]
    public function it_builds_general_config_correctly(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('Test App', $config['app_name']);
        $this->assertEquals('https://example.com', $config['website_url']);
        $this->assertEquals('com.test.app', $config['app_id']);
        $this->assertEquals('system', $config['orientation']); // Builder default
        $this->assertEquals('en', $config['language']); // Builder default
    }

    #[Test]
    public function it_builds_design_config_with_defaults(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('#3b82f6', $config['theme_color']); // Factory sets this
        $this->assertFalse($config['enable_dynamic_colors']); // Database default
        $this->assertEquals('#000000', $config['text_color_light']); // Builder default (field is nullable)
        $this->assertEquals('#FFFFFF', $config['text_color_dark']); // Builder default (field is nullable)
        $this->assertEquals('circular', $config['progress_indicator']); // Database default
        $this->assertEquals('#3B82F6', $config['progress_color']); // Builder default (field is nullable)
        $this->assertTrue($config['show_page_title']); // Database default
        $this->assertEquals('app_bar_tabs', $config['layout_template']); // Database default
    }

    #[Test]
    public function it_builds_design_config_with_custom_values(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'theme_color' => '#FF5733',
            'enable_dynamic_colors' => true,
            'text_color_light' => '#FFFFFF',
            'text_color_dark' => '#000000',
            'progress_indicator' => 'circular',
            'layout_template' => 'app_bar_drawer',
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('#FF5733', $config['theme_color']);
        $this->assertTrue($config['enable_dynamic_colors']);
        $this->assertEquals('#FFFFFF', $config['text_color_light']);
        $this->assertEquals('#000000', $config['text_color_dark']);
        $this->assertEquals('circular', $config['progress_indicator']);
        $this->assertEquals('app_bar_drawer', $config['layout_template']);
    }

    #[Test]
    public function it_builds_drawer_config_with_defaults(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('default', $config['drawer_mode']); // Database default
        $this->assertEquals('#FFFFFF', $config['drawer_background_color']); // Builder default (field is nullable)
        $this->assertEquals('', $config['drawer_background_image']); // Builder default (field is nullable)
        $this->assertFalse($config['drawer_logo_enabled']); // Database default
        $this->assertEquals(48, $config['drawer_logo_size']); // Database default
        // Drawer item colors with defaults
        $this->assertEquals('#6B7280', $config['drawer_icon_color_light']); // Builder default
        $this->assertEquals('#9CA3AF', $config['drawer_icon_color_dark']); // Builder default
        $this->assertEquals('#3b82f6', $config['drawer_active_color_light']); // Uses theme_color as default
        $this->assertEquals('#3b82f6', $config['drawer_active_color_dark']); // Uses theme_color as default
    }

    #[Test]
    public function it_builds_drawer_item_colors_with_custom_values(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'drawer_icon_color_light' => '#FF5733',
            'drawer_icon_color_dark' => '#33FF57',
            'drawer_active_color_light' => '#5733FF',
            'drawer_active_color_dark' => '#FF33FF',
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('#FF5733', $config['drawer_icon_color_light']);
        $this->assertEquals('#33FF57', $config['drawer_icon_color_dark']);
        $this->assertEquals('#5733FF', $config['drawer_active_color_light']);
        $this->assertEquals('#FF33FF', $config['drawer_active_color_dark']);
    }

    #[Test]
    public function it_includes_custom_code_only_when_plan_allows(): void
    {
        // Without plan permission
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('', $config['custom_css']);
        $this->assertEquals('', $config['custom_javascript']);

        // With plan permission
        $plan = Plan::create([
            'name' => 'Test Plan',
            'price' => 9.99,
            'billing_period' => 'monthly',
            'can_use_custom_code' => true,
            'status' => 'active',
            'features' => ['custom_code'],
        ]);
        $this->user->update(['plan_id' => $plan->id]);
        $this->user->refresh();
        $this->testApp->refresh();

        $this->testApp->androidWebViewConfig->update([
            'custom_css' => '.test { color: red; }',
            'custom_js' => 'console.log("test");',
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('.test { color: red; }', $config['custom_css']);
        $this->assertEquals('console.log("test");', $config['custom_javascript']);
    }

    #[Test]
    public function it_builds_icon_config(): void
    {
        $this->testApp->androidWebViewConfig->update(['uploaded_icon_zip' => 'icons/test-icon.zip']);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertStringContainsString('storage/icons/test-icon.zip', $config['icon_zip_url']);
    }

    #[Test]
    public function it_builds_webview_config_with_defaults(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        // Default values when build_config is empty
        $this->assertTrue($config['enable_javascript']);
        $this->assertTrue($config['enable_dom_storage']);
        $this->assertFalse($config['enable_zoom']);
        $this->assertTrue($config['enable_cache']);
    }

    #[Test]
    public function it_builds_webview_config_with_custom_values(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'build_config' => [
                'enable_javascript' => false,
                'enable_dom_storage' => false,
                'enable_zoom' => true,
                'enable_cache' => false,
            ],
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertFalse($config['enable_javascript']);
        $this->assertFalse($config['enable_dom_storage']);
        $this->assertTrue($config['enable_zoom']);
        $this->assertFalse($config['enable_cache']);
    }

    #[Test]
    public function it_builds_splashscreen_config_with_color_background(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'splashscreen_background_type' => 'color',
            'splashscreen_background' => '#FF0000',
            'splashscreen_logo_enabled' => true,
            'splashscreen_logo_size' => 150,
            'splashscreen_title' => 'Welcome',
            'splashscreen_duration' => 3,
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('color', $config['splash_background_type']);
        $this->assertEquals('#FF0000', $config['splash_background']);
        $this->assertTrue($config['splash_logo_enabled']);
        $this->assertEquals(150, $config['splash_logo_size']);
        $this->assertEquals('Welcome', $config['splash_title']);
        $this->assertEquals(3, $config['splash_delay']);
    }

    #[Test]
    public function it_builds_splashscreen_config_with_image_background(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'splashscreen_background_type' => 'image',
            'splashscreen_image' => 'splash-bg.jpg',
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('image', $config['splash_background_type']);
        $this->assertEquals('splash-bg.jpg', $config['splash_background']);
    }

    #[Test]
    public function it_transforms_navigation_items(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'navigation_items' => [
                ['label' => 'Home', 'value' => 'https://example.com', 'icon' => 'home'],
                ['label' => 'About', 'value' => 'https://example.com/about', 'icon' => 'info'],
            ],
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertCount(2, $config['bottom_tabs']);
        $this->assertEquals('https://example.com', $config['bottom_tabs'][0]['url']);
        $this->assertEquals('https://example.com/about', $config['bottom_tabs'][1]['url']);
        $this->assertArrayNotHasKey('value', $config['bottom_tabs'][0]);
    }

    #[Test]
    public function it_disables_swipe_navigation_for_drawer_layout(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'layout_template' => 'app_bar_drawer',
            'swipe_navigation' => true, // User tries to enable it
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertFalse($config['swipe_navigation'], 'Swipe navigation should be disabled for drawer layout');
    }

    #[Test]
    public function it_allows_swipe_navigation_for_non_drawer_layouts(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'layout_template' => 'app_bar_tabs',
            'swipe_navigation' => true,
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertTrue($config['swipe_navigation']);
    }

    #[Test]
    public function it_builds_permissions_config_from_platform_config(): void
    {
        $this->testApp->androidWebViewConfig->update([
            'permission_location' => true,
            'permission_camera' => true,
            'permission_storage' => false,
            'permission_record_audio' => true,
            'permission_read_contacts' => false,
            'permission_vibrate' => true,
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertTrue($config['permission_location']);
        $this->assertTrue($config['permission_camera']);
        $this->assertFalse($config['permission_storage']);
        $this->assertTrue($config['permission_record_audio']);
        $this->assertFalse($config['permission_read_contacts']);
        $this->assertTrue($config['permission_vibrate']);
    }

    #[Test]
    public function it_builds_signing_config_for_debug_build(): void
    {
        $this->build->update(['build_type' => 'debug']);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('', $config['keystore_file']);
        $this->assertEquals('', $config['keystore_password']);
        $this->assertEquals('', $config['key_alias']);
        $this->assertEquals('', $config['key_password']);
    }

    #[Test]
    public function it_builds_signing_config_for_release_build_with_keystore(): void
    {
        $keystore = Keystore::factory()->create([
            'user_id' => $this->user->id,
            'alias' => 'test-key',
        ]);
        // Factory sets these encrypted: password123 and keypassword123

        $this->build->update([
            'build_type' => 'release',
            'keystore_id' => $keystore->id,
        ]);
        $this->build->refresh();

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals((string) $keystore->id, $config['keystore_file']);
        $this->assertEquals('password123', $config['keystore_password']);
        $this->assertEquals('test-key', $config['key_alias']);
        $this->assertEquals('keypassword123', $config['key_password']);
    }

    #[Test]
    public function it_uses_store_password_as_key_password_when_key_password_not_set(): void
    {
        // Create keystore with only store password set (no key password)
        $keystore = Keystore::create([
            'user_id' => $this->user->id,
            'name' => 'Test Keystore',
            'alias' => 'test-key',
            'encrypted_keystore' => \Illuminate\Support\Facades\Crypt::encryptString('fake-data'),
            'encrypted_password' => \Illuminate\Support\Facades\Crypt::encryptString('store_pass123'),
            'encrypted_key_password' => null, // Explicitly no key password
        ]);

        $this->build->update([
            'build_type' => 'release',
            'keystore_id' => $keystore->id,
        ]);
        $this->build->refresh();

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('store_pass123', $config['key_password']);
    }

    #[Test]
    public function it_builds_versioning_config(): void
    {
        $this->build->update([
            'version_name' => '2.1.5',
            'version_code' => 42,
            'build_format' => 'aab',
        ]);

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertEquals('aab', $config['build_format']);
        $this->assertEquals('2.1.5', $config['android_version_name']);
        $this->assertEquals(42, $config['version_code']);
    }

    #[Test]
    public function it_builds_push_notifications_config_when_disabled(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertFalse($config['firebase_enabled']);
        $this->assertEquals('', $config['google_services_json']);
    }

    #[Test]
    public function it_builds_push_notifications_config_when_enabled(): void
    {
        Storage::fake('private');
        $googleServicesJson = '{"project_info":{"project_id":"test-project"}}';
        Storage::disk('private')->put('google-services/test.json', $googleServicesJson);

        PushNotificationConfig::create([
            'app_id' => $this->testApp->id,
            'platform' => 'android',
            'enabled' => true,
            'google_services_file' => 'google-services/test.json',
        ]);
        $this->testApp->refresh();

        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertTrue($config['firebase_enabled']);
        $this->assertEquals($googleServicesJson, $config['google_services_json']);
    }

    #[Test]
    public function it_handles_missing_optional_fields_with_defaults(): void
    {
        // Create minimal app with only required fields
        $minimalApp = App::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Minimal App',
        ]);

        // Update androidWebViewConfig with minimal required fields
        $minimalApp->androidWebViewConfig->update([
            'url' => 'https://minimal.com',
            'package_name' => 'com.minimal.app',
        ]);

        $minimalBuild = AppBuild::factory()->create([
            'app_id' => $minimalApp->id,
            'version_name' => '1.0.0',
            'version_code' => 1,
        ]);

        $context = new BuildConfigContext($minimalApp, $minimalBuild);
        $builder = new AndroidBuildConfigBuilder($context);
        $config = $builder->build();

        // Verify default values are used
        $this->assertEquals('system', $config['orientation']); // Database default
        $this->assertEquals('en', $config['language']); // Database default
        $this->assertEquals('#3b82f6', $config['theme_color']); // Factory sets this
        $this->assertEquals('app_bar_tabs', $config['layout_template']); // Database default
        $this->assertEquals(120, $config['splash_logo_size']); // Database default
        $this->assertTrue($config['pull_to_refresh']); // Database default
    }

    #[Test]
    public function it_handles_empty_navigation_arrays(): void
    {
        $builder = new AndroidBuildConfigBuilder($this->context);
        $config = $builder->build();

        $this->assertIsArray($config['bottom_tabs']);
        $this->assertIsArray($config['drawer_items']);
        $this->assertIsArray($config['appbar_buttons']);
        $this->assertCount(0, $config['bottom_tabs']);
        $this->assertCount(0, $config['drawer_items']);
        $this->assertCount(0, $config['appbar_buttons']);
    }
}
