<?php

namespace Tests\Feature\App;

use App\Models\App;
use App\Models\PushNotificationConfig;
use App\Models\User;
use App\Plugins\Platforms\AndroidWebView\Livewire\AppNotificationsSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AppNotificationsSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithApp()
    {
        $user = User::factory()->create();
        $app = App::factory()->forUser($user)->create();
        $this->actingAs($user);

        return [$user, $app];
    }

    public function test_component_renders_for_authenticated_user()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->assertOk()
            ->assertSet('enabled', false);
    }

    public function test_component_loads_existing_config()
    {
        [$user, $app] = $this->createUserWithApp();

        $config = PushNotificationConfig::factory()->forApp($app)->create([
            'firebase_credentials_file' => 'fcm/test-app/firebase.json',
            'google_services_file' => 'fcm/test-app/google-services.json',
            'enabled' => true,
        ]);

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->assertSet('firebase_credentials_file', 'fcm/test-app/firebase.json')
            ->assertSet('google_services_file', 'fcm/test-app/google-services.json')
            ->assertSet('enabled', true);
    }

    public function test_google_services_file_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors('google_services_file_upload');
    }

    public function test_google_services_file_must_be_json()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->create('services.txt', 100);

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('google_services_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasErrors(['google_services_file_upload' => 'mimes']);
    }

    public function test_google_services_file_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->create('services.json', 2000); // 2MB

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('google_services_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasErrors(['google_services_file_upload' => 'max']);
    }

    public function test_firebase_credentials_file_is_optional()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors('firebase_credentials_file_upload');
    }

    public function test_firebase_credentials_file_must_be_json()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->create('credentials.txt', 100);

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('firebase_credentials_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasErrors(['firebase_credentials_file_upload' => 'mimes']);
    }

    public function test_firebase_credentials_file_cannot_exceed_max_size()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->create('credentials.json', 3000); // 3MB

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('firebase_credentials_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasErrors(['firebase_credentials_file_upload' => 'max']);
    }

    public function test_enabled_is_required()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', '')
            ->call('save')
            ->assertHasErrors(['enabled' => 'required']);
    }

    public function test_enabled_must_be_boolean()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', 'not-a-boolean')
            ->call('save')
            ->assertHasErrors(['enabled' => 'boolean']);
    }

    public function test_can_create_new_config()
    {
        [$user, $app] = $this->createUserWithApp();

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('show-toast');

        $this->assertDatabaseHas('push_notification_configs', [
            'app_id' => $app->id,
            'enabled' => true,
        ]);
    }

    public function test_can_update_existing_config()
    {
        [$user, $app] = $this->createUserWithApp();

        $config = PushNotificationConfig::factory()->forApp($app)->create([
            'enabled' => true,
        ]);

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $config->refresh();
        $this->assertEquals(false, $config->enabled);
    }

    public function test_google_services_file_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->createWithContent(
            'google-services.json',
            json_encode(['project_info' => ['project_id' => 'test']])
        );

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('google_services_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        $config = PushNotificationConfig::where('app_id', $app->id)->first();
        $this->assertNotNull($config->google_services_file);
        Storage::disk('private')->assertExists($config->google_services_file);
    }

    public function test_firebase_credentials_file_is_stored_when_uploaded()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $file = UploadedFile::fake()->createWithContent(
            'firebase-credentials.json',
            json_encode(['type' => 'service_account', 'project_id' => 'test'])
        );

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('firebase_credentials_file_upload', $file)
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        $config = PushNotificationConfig::where('app_id', $app->id)->first();
        $this->assertNotNull($config->firebase_credentials_file);
        Storage::disk('private')->assertExists($config->firebase_credentials_file);
    }

    public function test_both_files_can_be_uploaded_together()
    {
        [$user, $app] = $this->createUserWithApp();
        Storage::fake('private');

        $googleServicesFile = UploadedFile::fake()->createWithContent(
            'google-services.json',
            json_encode(['project_info' => ['project_id' => 'test']])
        );
        $firebaseCredentialsFile = UploadedFile::fake()->createWithContent(
            'firebase-credentials.json',
            json_encode(['type' => 'service_account', 'project_id' => 'test'])
        );

        Livewire::test(AppNotificationsSettings::class, ['app' => $app])
            ->set('google_services_file_upload', $googleServicesFile)
            ->set('firebase_credentials_file_upload', $firebaseCredentialsFile)
            ->set('enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        $config = PushNotificationConfig::where('app_id', $app->id)->first();
        $this->assertNotNull($config->google_services_file);
        $this->assertNotNull($config->firebase_credentials_file);
        Storage::disk('private')->assertExists($config->google_services_file);
        Storage::disk('private')->assertExists($config->firebase_credentials_file);
    }
}
