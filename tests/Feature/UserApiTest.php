<?php

namespace Tests\Feature;

use App\Jobs\SendPushNotificationJob;
use App\Models\App;
use App\Models\AppBuild;
use App\Models\AppBuilder;
use App\Models\AppNotification;
use App\Models\Plan;
use App\Models\PushNotificationConfig;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Plan $plan;

    protected App $testApp;

    protected function setUp(): void
    {
        parent::setUp();

        // Note: PlatformPluginsSeeder is already run in base TestCase

        // Create a plan with API access
        $this->plan = Plan::factory()->create([
            'name' => 'Pro',
            'can_use_api' => true,
            'monthly_build_credits' => 100,
        ]);

        // Create a user with the plan
        $this->user = User::factory()->create([
            'plan_id' => $this->plan->id,
            'build_credits' => 50,
        ]);

        // Create an app for the user
        $this->testApp = App::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    // ==================== Authentication Tests ====================

    #[Test]
    public function it_requires_authentication_for_api_endpoints()
    {
        $response = $this->getJson('/api/v1/account');
        $response->assertUnauthorized();
    }

    #[Test]
    public function it_authenticates_with_valid_sanctum_token()
    {
        Sanctum::actingAs($this->user, ['*']);

        $response = $this->getJson('/api/v1/account');
        $response->assertOk();
    }

    #[Test]
    public function it_denies_access_when_plan_does_not_allow_api()
    {
        $planWithoutApi = Plan::factory()->create([
            'can_use_api' => false,
        ]);

        $userWithoutApi = User::factory()->create([
            'plan_id' => $planWithoutApi->id,
        ]);

        Sanctum::actingAs($userWithoutApi, ['*']);

        $response = $this->getJson('/api/v1/account');
        $response->assertForbidden();
        $response->assertJson([
            'error' => 'api_access_denied',
        ]);
    }

    // ==================== Account Endpoint Tests ====================

    #[Test]
    public function it_returns_account_information()
    {
        Sanctum::actingAs($this->user, ['account:read']);

        $response = $this->getJson('/api/v1/account');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified',
                'role',
                'status',
                'plan' => ['id', 'name', 'features'],
                'build_credits' => ['available', 'monthly_allocation', 'unlimited'],
                'created_at',
            ],
        ]);
        $response->assertJsonPath('data.id', $this->user->id);
        $response->assertJsonPath('data.email', $this->user->email);
    }

    #[Test]
    public function it_returns_subscription_information()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
        ]);

        Sanctum::actingAs($this->user, ['account:read']);

        $response = $this->getJson('/api/v1/account/subscription');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'status',
                'amount',
                'payment_method',
                'plan' => ['id', 'name', 'price', 'currency', 'billing_period', 'features'],
                'renewal_at',
                'ends_at',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function it_returns_no_subscription_message_when_none_exists()
    {
        Sanctum::actingAs($this->user, ['account:read']);

        $response = $this->getJson('/api/v1/account/subscription');

        $response->assertOk();
        $response->assertJsonPath('message', 'No active subscription found.');
    }

    #[Test]
    public function it_returns_usage_statistics()
    {
        // Create some builds
        AppBuild::factory()->count(3)->create([
            'app_id' => $this->testApp->id,
            'status' => 'completed',
        ]);

        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'failed',
        ]);

        Sanctum::actingAs($this->user, ['account:read']);

        $response = $this->getJson('/api/v1/account/usage');

        $response->assertOk();
        $response->assertJsonStructure([
            'build_credits' => ['available', 'monthly_allocation', 'unlimited'],
            'apps' => ['total'],
            'builds' => ['total', 'completed', 'failed'],
            'plan' => ['id', 'name', 'features'],
        ]);
        $response->assertJsonPath('apps.total', 1);
        $response->assertJsonPath('builds.total', 4);
        $response->assertJsonPath('builds.completed', 3);
        $response->assertJsonPath('builds.failed', 1);
    }

    #[Test]
    public function it_denies_account_access_without_proper_scope()
    {
        Sanctum::actingAs($this->user, ['apps:read']); // Wrong scope

        $response = $this->getJson('/api/v1/account');
        $response->assertForbidden();
    }

    // ==================== Apps Endpoint Tests ====================

    #[Test]
    public function it_lists_user_apps()
    {
        App::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user, ['apps:read']);

        $response = $this->getJson('/api/v1/apps');

        $response->assertOk();
        $response->assertJsonCount(4, 'data'); // 3 + 1 from setUp
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'platform',
                    'version_name',
                    'version_code',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    #[Test]
    public function it_does_not_list_other_users_apps()
    {
        $otherUser = User::factory()->create();
        App::factory()->count(2)->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($this->user, ['apps:read']);

        $response = $this->getJson('/api/v1/apps');

        $response->assertOk();
        $response->assertJsonCount(1, 'data'); // Only the one from setUp
    }

    #[Test]
    public function it_shows_single_app_details()
    {
        Sanctum::actingAs($this->user, ['apps:read']);

        $response = $this->getJson("/api/v1/apps/{$this->testApp->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $this->testApp->id);
        $response->assertJsonPath('data.name', $this->testApp->name);
    }

    #[Test]
    public function it_returns_404_for_other_users_app()
    {
        $otherUser = User::factory()->create();
        $otherApp = App::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($this->user, ['apps:read']);

        $response = $this->getJson("/api/v1/apps/{$otherApp->id}");

        $response->assertNotFound();
    }

    #[Test]
    public function it_creates_a_new_app()
    {
        Sanctum::actingAs($this->user, ['apps:write']);

        $response = $this->postJson('/api/v1/apps', [
            'name' => 'My New App',
            'platform' => 'android-webview',
            'url' => 'https://example.com',
            'package_name' => 'com.example.newapp',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'My New App');

        $this->assertDatabaseHas('apps', [
            'user_id' => $this->user->id,
            'name' => 'My New App',
        ]);
    }

    #[Test]
    public function it_validates_app_creation_request()
    {
        Sanctum::actingAs($this->user, ['apps:write']);

        $response = $this->postJson('/api/v1/apps', [
            'name' => '', // Required
            'platform' => 'invalid-platform',
            'url' => 'not-a-url',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['name', 'platform', 'url']);
    }

    #[Test]
    public function it_updates_an_existing_app()
    {
        Sanctum::actingAs($this->user, ['apps:write']);

        $response = $this->putJson("/api/v1/apps/{$this->testApp->id}", [
            'name' => 'Updated App Name',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Updated App Name');

        $this->assertDatabaseHas('apps', [
            'id' => $this->testApp->id,
            'name' => 'Updated App Name',
        ]);
    }

    #[Test]
    public function it_deletes_an_app()
    {
        Sanctum::actingAs($this->user, ['apps:write']);

        $response = $this->deleteJson("/api/v1/apps/{$this->testApp->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'App deleted successfully.');

        $this->assertDatabaseMissing('apps', [
            'id' => $this->testApp->id,
        ]);
    }

    #[Test]
    public function it_cannot_delete_app_with_active_builds()
    {
        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'building_android',
        ]);

        Sanctum::actingAs($this->user, ['apps:write']);

        $response = $this->deleteJson("/api/v1/apps/{$this->testApp->id}");

        $response->assertConflict();
        $response->assertJsonPath('error', 'active_builds');
    }

    #[Test]
    public function it_denies_app_write_without_proper_scope()
    {
        Sanctum::actingAs($this->user, ['apps:read']); // Read-only scope

        $response = $this->postJson('/api/v1/apps', [
            'name' => 'Test App',
            'platform' => 'android-webview',
            'url' => 'https://example.com',
        ]);

        $response->assertForbidden();
    }

    // ==================== Builds Endpoint Tests ====================

    #[Test]
    public function it_lists_builds_for_an_app()
    {
        AppBuild::factory()->count(5)->create([
            'app_id' => $this->testApp->id,
        ]);

        Sanctum::actingAs($this->user, ['builds:read']);

        $response = $this->getJson("/api/v1/apps/{$this->testApp->id}/builds");

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'app_id',
                    'platform',
                    'build_type',
                    'build_format',
                    'status',
                    'created_at',
                ],
            ],
        ]);
    }

    #[Test]
    public function it_shows_single_build_details()
    {
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'completed',
        ]);

        Sanctum::actingAs($this->user, ['builds:read']);

        $response = $this->getJson("/api/v1/builds/{$build->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $build->id);
        $response->assertJsonPath('data.status', 'completed');
    }

    #[Test]
    public function it_triggers_a_new_build()
    {
        // Create an available builder
        AppBuilder::factory()->create([
            'status' => 'active',
            'platforms' => ['android-webview'],
            'max_queue' => 10,
            'current_builds_count' => 0,
            'credit_cost' => 1,
        ]);

        Sanctum::actingAs($this->user, ['builds:trigger']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/builds", [
            'build_format' => 'apk',
            'build_type' => 'debug',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'pending');
        $response->assertJsonPath('data.build_format', 'apk');
        $response->assertJsonPath('data.build_type', 'debug');

        // Verify build was created
        $this->assertDatabaseHas('app_builds', [
            'app_id' => $this->testApp->id,
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function it_returns_error_when_no_builders_available()
    {
        // No builders exist
        Sanctum::actingAs($this->user, ['builds:trigger']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/builds", [
            'build_format' => 'apk',
        ]);

        $response->assertServiceUnavailable();
        $response->assertJsonPath('error', 'no_builders');
    }

    #[Test]
    public function it_returns_error_when_insufficient_credits()
    {
        AppBuilder::factory()->create([
            'status' => 'active',
            'platforms' => ['android-webview'],
            'max_queue' => 10,
            'current_builds_count' => 0,
            'credit_cost' => 100, // More than user has
        ]);

        $this->user->update(['build_credits' => 5]);

        Sanctum::actingAs($this->user, ['builds:trigger']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/builds", [
            'build_format' => 'apk',
        ]);

        $response->assertPaymentRequired();
        $response->assertJsonPath('error', 'insufficient_credits');
    }

    #[Test]
    public function it_returns_error_when_app_has_active_build()
    {
        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'building_android',
        ]);

        AppBuilder::factory()->create([
            'status' => 'active',
            'platforms' => ['android-webview'],
            'max_queue' => 10,
            'current_builds_count' => 0,
        ]);

        Sanctum::actingAs($this->user, ['builds:trigger']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/builds");

        $response->assertConflict();
        $response->assertJsonPath('error', 'active_build');
    }

    #[Test]
    public function it_returns_download_url_for_completed_build()
    {
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'completed',
            'artifact_url' => 'builds/test-artifact.apk',
            'artifact_size' => 1024000,
            'expires_at' => now()->addDays(30),
        ]);

        Sanctum::actingAs($this->user, ['builds:download']);

        $response = $this->getJson("/api/v1/builds/{$build->id}/download");

        $response->assertOk();
        $response->assertJsonStructure([
            'download_url',
            'file_name',
            'file_size',
            'expires_at',
        ]);
    }

    #[Test]
    public function it_returns_error_for_incomplete_build_download()
    {
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($this->user, ['builds:download']);

        $response = $this->getJson("/api/v1/builds/{$build->id}/download");

        $response->assertBadRequest();
        $response->assertJsonPath('error', 'build_not_completed');
    }

    #[Test]
    public function it_returns_build_logs()
    {
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'status' => 'completed',
            'build_logs' => 'Build started...\nCompiling...\nDone!',
        ]);

        Sanctum::actingAs($this->user, ['builds:read']);

        $response = $this->getJson("/api/v1/builds/{$build->id}/logs");

        $response->assertOk();
        $response->assertJsonStructure([
            'build_id',
            'status',
            'logs',
        ]);
        $response->assertJsonPath('build_id', $build->id);
    }

    // ==================== Notifications Endpoint Tests ====================

    #[Test]
    public function it_lists_notifications_for_an_app()
    {
        AppNotification::factory()->count(3)->create([
            'app_id' => $this->testApp->id,
            'user_id' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user, ['notifications:read']);

        $response = $this->getJson("/api/v1/apps/{$this->testApp->id}/notifications");

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'app_id',
                    'title',
                    'body',
                    'status',
                    'created_at',
                ],
            ],
        ]);
    }

    #[Test]
    public function it_sends_a_push_notification()
    {
        Queue::fake();

        // Configure push notifications for the app
        PushNotificationConfig::factory()->create([
            'app_id' => $this->testApp->id,
            'enabled' => true,
        ]);

        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/notifications", [
            'title' => 'Test Notification',
            'body' => 'This is a test notification body.',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.title', 'Test Notification');
        $response->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('app_notifications', [
            'app_id' => $this->testApp->id,
            'title' => 'Test Notification',
        ]);

        // Assert job was dispatched
        Queue::assertPushed(SendPushNotificationJob::class);
    }

    #[Test]
    public function it_schedules_a_notification()
    {
        Queue::fake();

        PushNotificationConfig::factory()->create([
            'app_id' => $this->testApp->id,
            'enabled' => true,
        ]);

        $scheduledTime = now()->addHour()->toIso8601String();

        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/notifications", [
            'title' => 'Scheduled Notification',
            'body' => 'This will be sent later.',
            'scheduled_at' => $scheduledTime,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.status', 'scheduled');

        // Scheduled notifications should NOT dispatch the job immediately
        Queue::assertNotPushed(SendPushNotificationJob::class);
    }

    #[Test]
    public function it_returns_error_when_push_not_configured()
    {
        // No push config for this app
        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/notifications", [
            'title' => 'Test',
            'body' => 'Test body',
        ]);

        $response->assertBadRequest();
        $response->assertJsonPath('error', 'notifications_not_configured');
    }

    #[Test]
    public function it_cancels_a_pending_notification()
    {
        $notification = AppNotification::factory()->create([
            'app_id' => $this->testApp->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->deleteJson("/api/v1/notifications/{$notification->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Notification cancelled successfully.');

        $this->assertDatabaseMissing('app_notifications', [
            'id' => $notification->id,
        ]);
    }

    #[Test]
    public function it_cannot_cancel_sent_notification()
    {
        $notification = AppNotification::factory()->create([
            'app_id' => $this->testApp->id,
            'user_id' => $this->user->id,
            'status' => 'sent',
        ]);

        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->deleteJson("/api/v1/notifications/{$notification->id}");

        $response->assertBadRequest();
        $response->assertJsonPath('error', 'already_sent');
    }

    #[Test]
    public function it_validates_notification_request()
    {
        PushNotificationConfig::factory()->create([
            'app_id' => $this->testApp->id,
            'enabled' => true,
        ]);

        Sanctum::actingAs($this->user, ['notifications:send']);

        $response = $this->postJson("/api/v1/apps/{$this->testApp->id}/notifications", [
            'title' => '', // Required
            'body' => '', // Required
            'scheduled_at' => 'not-a-date',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title', 'body', 'scheduled_at']);
    }

    // ==================== Scope Tests ====================

    #[Test]
    public function it_allows_all_access_with_wildcard_scope()
    {
        Sanctum::actingAs($this->user, ['*']);

        // Should be able to access all endpoints
        $this->getJson('/api/v1/account')->assertOk();
        $this->getJson('/api/v1/apps')->assertOk();
        $this->getJson("/api/v1/apps/{$this->testApp->id}/builds")->assertOk();
    }

    #[Test]
    public function it_restricts_access_based_on_scopes()
    {
        Sanctum::actingAs($this->user, ['apps:read']);

        // Should work
        $this->getJson('/api/v1/apps')->assertOk();

        // Should fail - different endpoint without proper scope
        $this->getJson('/api/v1/account')->assertForbidden();

        // POST with apps:read should fail (needs apps:write)
        // Note: Scope check happens before validation, but with valid data
        $response = $this->postJson('/api/v1/apps', [
            'name' => 'Test App',
            'platform' => 'android-webview',
            'url' => 'https://example.com',
            'package_name' => 'com.test.scopetest',
        ]);
        $response->assertForbidden();
    }

    // ==================== Rate Limiting Tests ====================

    #[Test]
    public function it_applies_rate_limiting()
    {
        Sanctum::actingAs($this->user, ['*']);

        // Make many requests (rate limit is 60/minute)
        for ($i = 0; $i < 65; $i++) {
            $response = $this->getJson('/api/v1/account');

            if ($i >= 60) {
                $response->assertTooManyRequests();
                break;
            }
        }
    }
}
