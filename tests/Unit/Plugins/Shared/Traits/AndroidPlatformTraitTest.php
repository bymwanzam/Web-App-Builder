<?php

namespace Tests\Unit\Plugins\Shared\Traits;

use App\Models\App;
use App\Models\PushNotificationConfig;
use App\Models\User;
use App\Plugins\Shared\Traits\AndroidPlatformTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for AndroidPlatformTrait
 *
 * Comprehensive test coverage for shared Android platform logic:
 * - Build format support
 * - Builder type identification
 * - Default configuration
 * - Permission definitions
 * - Package ID generation and validation
 * - Navigation transformation
 * - Firebase/Google Services integration
 */
class AndroidPlatformTraitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test stub class that uses the trait
     */
    protected object $traitInstance;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the trait for testing
        $this->traitInstance = new class
        {
            use AndroidPlatformTrait;
        };
    }

    #[Test]
    public function it_returns_supported_build_formats(): void
    {
        $formats = $this->traitInstance->getSupportedBuildFormats();

        $this->assertIsArray($formats);
        $this->assertCount(2, $formats);
        $this->assertContains('apk', $formats);
        $this->assertContains('aab', $formats);
    }

    #[Test]
    public function it_returns_correct_builder_type(): void
    {
        $builderType = $this->traitInstance->getBuilderType();

        $this->assertEquals('android', $builderType);
    }

    #[Test]
    public function it_returns_default_config_with_correct_structure(): void
    {
        $config = $this->traitInstance->getDefaultConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('version_name', $config);
        $this->assertArrayHasKey('min_sdk', $config);
        $this->assertArrayHasKey('target_sdk', $config);
        $this->assertArrayHasKey('build_tools_version', $config);
        $this->assertArrayHasKey('permissions', $config);
    }

    #[Test]
    public function it_has_correct_default_values(): void
    {
        $config = $this->traitInstance->getDefaultConfig();

        $this->assertEquals('1.0.0', $config['version_name']);
        $this->assertEquals(24, $config['min_sdk']);
        $this->assertEquals(34, $config['target_sdk']);
        $this->assertEquals('34.0.0', $config['build_tools_version']);
    }

    #[Test]
    public function it_has_internet_permission_enabled_by_default(): void
    {
        $config = $this->traitInstance->getDefaultConfig();

        $this->assertTrue($config['permissions']['internet']);
        $this->assertFalse($config['permissions']['location']);
        $this->assertFalse($config['permissions']['camera']);
        $this->assertFalse($config['permissions']['storage']);
    }

    #[Test]
    public function it_returns_available_permissions_with_correct_structure(): void
    {
        $permissions = $this->traitInstance->getAvailablePermissions();

        $this->assertIsArray($permissions);
        $this->assertArrayHasKey('internet', $permissions);
        $this->assertArrayHasKey('location', $permissions);
        $this->assertArrayHasKey('camera', $permissions);
        $this->assertArrayHasKey('storage', $permissions);
        $this->assertArrayHasKey('record_audio', $permissions);
        $this->assertArrayHasKey('read_contacts', $permissions);
        $this->assertArrayHasKey('vibrate', $permissions);
    }

    #[Test]
    public function each_permission_has_required_fields(): void
    {
        $permissions = $this->traitInstance->getAvailablePermissions();

        foreach ($permissions as $key => $permission) {
            $this->assertArrayHasKey('label', $permission, "Permission {$key} missing label");
            $this->assertArrayHasKey('description', $permission, "Permission {$key} missing description");
            $this->assertArrayHasKey('required', $permission, "Permission {$key} missing required flag");
        }
    }

    #[Test]
    public function internet_permission_is_marked_as_required(): void
    {
        $permissions = $this->traitInstance->getAvailablePermissions();

        $this->assertTrue($permissions['internet']['required']);
        $this->assertFalse($permissions['location']['required']);
        $this->assertFalse($permissions['camera']['required']);
    }

    #[Test]
    public function it_generates_valid_package_id_from_app_name(): void
    {
        $packageId = $this->traitInstance->generatePackageId('My Awesome App');

        $this->assertEquals('com.myawesomeapp.app', $packageId);
    }

    #[Test]
    public function it_handles_special_characters_in_app_name(): void
    {
        $packageId = $this->traitInstance->generatePackageId('Test App 2024!@#$%');

        $this->assertEquals('com.testapp2024.app', $packageId);
    }

    #[Test]
    public function it_adds_prefix_if_app_name_starts_with_number(): void
    {
        $packageId = $this->traitInstance->generatePackageId('123 App');

        $this->assertEquals('com.app123app.app', $packageId);
    }

    #[Test]
    public function it_adds_attempt_suffix_when_specified(): void
    {
        $packageId1 = $this->traitInstance->generatePackageId('Test App', 1);
        $packageId2 = $this->traitInstance->generatePackageId('Test App', 2);
        $packageId3 = $this->traitInstance->generatePackageId('Test App', 3);

        $this->assertEquals('com.testapp.app', $packageId1);
        $this->assertEquals('com.testapp2.app', $packageId2);
        $this->assertEquals('com.testapp3.app', $packageId3);
    }

    #[Test]
    public function it_validates_correct_package_id_format(): void
    {
        $this->assertTrue($this->traitInstance->validatePackageId('com.example.app'));
        $this->assertTrue($this->traitInstance->validatePackageId('com.mycompany.myapp'));
        $this->assertTrue($this->traitInstance->validatePackageId('org.example.test123'));
        $this->assertTrue($this->traitInstance->validatePackageId('com.test_app.mobile'));
    }

    #[Test]
    public function it_rejects_package_id_with_single_segment(): void
    {
        $this->assertFalse($this->traitInstance->validatePackageId('app'));
        $this->assertFalse($this->traitInstance->validatePackageId('testapp'));
    }

    #[Test]
    public function it_rejects_package_id_starting_with_number(): void
    {
        $this->assertFalse($this->traitInstance->validatePackageId('com.123example.app'));
        $this->assertFalse($this->traitInstance->validatePackageId('2com.example.app'));
    }

    #[Test]
    public function it_rejects_package_id_with_invalid_characters(): void
    {
        $this->assertFalse($this->traitInstance->validatePackageId('com.example-app.mobile'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.example app.mobile'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.example@app.mobile'));
    }

    #[Test]
    public function it_rejects_package_id_with_java_reserved_keywords(): void
    {
        $this->assertFalse($this->traitInstance->validatePackageId('com.class.app'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.public.test'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.interface.mobile'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.example.if'));
    }

    #[Test]
    public function it_rejects_package_id_with_empty_segments(): void
    {
        $this->assertFalse($this->traitInstance->validatePackageId('com..app'));
        $this->assertFalse($this->traitInstance->validatePackageId('.com.app'));
        $this->assertFalse($this->traitInstance->validatePackageId('com.app.'));
    }

    #[Test]
    public function it_transforms_navigation_items_value_to_url(): void
    {
        $items = [
            ['label' => 'Home', 'value' => 'https://example.com', 'icon' => 'home'],
            ['label' => 'About', 'value' => 'https://example.com/about', 'icon' => 'info'],
        ];

        $transformed = $this->callProtectedMethod($this->traitInstance, 'transformNavigationItems', [$items]);

        $this->assertCount(2, $transformed);
        $this->assertEquals('https://example.com', $transformed[0]['url']);
        $this->assertEquals('https://example.com/about', $transformed[1]['url']);
        $this->assertArrayNotHasKey('value', $transformed[0]);
        $this->assertArrayNotHasKey('value', $transformed[1]);
    }

    #[Test]
    public function it_preserves_other_navigation_item_fields(): void
    {
        $items = [
            ['label' => 'Home', 'value' => 'https://example.com', 'icon' => 'home', 'order' => 1],
        ];

        $transformed = $this->callProtectedMethod($this->traitInstance, 'transformNavigationItems', [$items]);

        $this->assertEquals('Home', $transformed[0]['label']);
        $this->assertEquals('home', $transformed[0]['icon']);
        $this->assertEquals(1, $transformed[0]['order']);
    }

    #[Test]
    public function it_handles_empty_navigation_items_array(): void
    {
        $transformed = $this->callProtectedMethod($this->traitInstance, 'transformNavigationItems', [[]]);

        $this->assertIsArray($transformed);
        $this->assertCount(0, $transformed);
    }

    #[Test]
    public function it_returns_empty_string_when_push_config_not_configured(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);

        $content = $this->callProtectedMethod($this->traitInstance, 'getGoogleServicesContent', [$app]);

        $this->assertEquals('', $content);
    }

    #[Test]
    public function it_returns_empty_string_when_push_config_disabled(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);
        PushNotificationConfig::create([
            'app_id' => $app->id,
            'platform' => 'android',
            'enabled' => false,
            'google_services_file' => 'test.json',
        ]);

        $content = $this->callProtectedMethod($this->traitInstance, 'getGoogleServicesContent', [$app]);

        $this->assertEquals('', $content);
    }

    #[Test]
    public function it_returns_google_services_content_when_configured(): void
    {
        Storage::fake('private');
        $googleServicesJson = '{"project_info":{"project_id":"test-project"}}';
        Storage::disk('private')->put('google-services/test.json', $googleServicesJson);

        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);
        PushNotificationConfig::create([
            'app_id' => $app->id,
            'platform' => 'android',
            'enabled' => true,
            'google_services_file' => 'google-services/test.json',
        ]);

        $content = $this->callProtectedMethod($this->traitInstance, 'getGoogleServicesContent', [$app]);

        $this->assertEquals($googleServicesJson, $content);
    }

    /**
     * Helper method to call protected methods for testing
     */
    protected function callProtectedMethod(object $object, string $method, array $args = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
