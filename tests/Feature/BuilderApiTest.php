<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\AppBuild;
use App\Models\AppBuilder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BuilderApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected App $testApp;

    protected AppBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the AndroidWebView platform plugin
        $this->seed(\Database\Seeders\PlatformPluginsSeeder::class);

        // Create a user
        $this->user = User::factory()->create([
            'build_credits' => 10,
        ]);

        // Create an app
        $this->testApp = App::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create a builder
        $this->builder = AppBuilder::factory()->create([
            'name' => 'Test Builder',
            'server_key' => 'test-server-key-123',
            'platforms' => ['android-webview'],
            'status' => 'active',
            'max_queue' => 5,
            'current_builds_count' => 0,
        ]);
    }

    #[Test]
    public function it_returns_pending_builds_for_authenticated_server()
    {
        // Mock HTTP call to builder's queue endpoint
        Http::fake([
            '*' => Http::response(['data' => ['processing' => 0]], 200),
        ]);

        // Create a pending build without builder assignment
        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        $response = $this->getJson("/api/v1/builds/pending/{$this->builder->server_key}");

        $response->assertOk();
        $response->assertJsonStructure([
            'builds' => [
                '*' => ['id', 'platform', 'build_type', 'config'],
            ],
        ]);
        $response->assertJsonCount(1, 'builds');
    }

    #[Test]
    public function it_assigns_builds_to_requesting_server_in_transaction()
    {
        // Mock HTTP call to builder's queue endpoint
        Http::fake([
            '*' => Http::response(['data' => ['processing' => 0]], 200),
        ]);

        // Create a pending build
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        $this->getJson("/api/v1/builds/pending/{$this->builder->server_key}");

        // Verify build was assigned to the builder
        $build->refresh();
        $this->assertEquals($this->builder->id, $build->app_builder_id);

        // Note: We no longer increment a database counter as the queue status is checked in real-time
    }

    #[Test]
    public function it_respects_server_max_queue_limit()
    {
        // Mock HTTP call to builder's queue endpoint showing it's at max capacity
        Http::fake([
            '*' => Http::response(['data' => ['processing' => 5]], 200),
        ]);

        // Create pending builds
        AppBuild::factory()->count(3)->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        $response = $this->getJson("/api/v1/builds/pending/{$this->builder->server_key}");

        $response->assertOk();
        $response->assertJsonCount(0, 'builds');
    }

    #[Test]
    public function it_only_returns_builds_matching_server_platform()
    {
        // Mock HTTP call to builder's queue endpoint
        Http::fake([
            '*' => Http::response(['data' => ['processing' => 0]], 200),
        ]);

        // Create AndroidWebView build
        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        // Create iOS build
        AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'ios',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        $response = $this->getJson("/api/v1/builds/pending/{$this->builder->server_key}");

        $response->assertOk();
        $response->assertJsonCount(1, 'builds'); // Only Android build
    }

    #[Test]
    public function it_accepts_status_updates_from_builder()
    {
        // Create a build assigned to this builder
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => $this->builder->id,
        ]);

        $response = $this->postJson("/api/v1/builds/{$build->id}/status/{$this->builder->server_key}", [
            'status' => 'failed',
            'error' => 'Build failed due to compilation error',
            'build_logs' => 'Error: Compilation failed...',
            'build_duration' => 120,
        ]);

        $response->assertOk();

        // Verify build status was updated
        $build->refresh();
        $this->assertEquals('failed', $build->status);
        $this->assertEquals('Build failed due to compilation error', $build->error_message);
    }

    #[Test]
    public function it_accepts_intermediate_status_updates()
    {
        // Create a build assigned to this builder
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => $this->builder->id,
        ]);

        // Test each intermediate status (only statuses allowed by the API validation)
        $intermediateStatuses = ['pending', 'preparing', 'building_android', 'processing'];

        foreach ($intermediateStatuses as $status) {
            $response = $this->postJson("/api/v1/builds/{$build->id}/status/{$this->builder->server_key}", [
                'status' => $status,
                'message' => "Build is {$status}",
            ]);

            $response->assertOk();

            // Verify status was updated
            $build->refresh();
            $this->assertEquals($status, $build->status);
        }
    }

    #[Test]
    public function it_validates_server_key_authentication()
    {
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => null,
        ]);

        // Request with invalid server key
        $response = $this->getJson('/api/v1/builds/pending/invalid-key');

        $response->assertUnauthorized();
    }

    #[Test]
    public function it_prevents_builder_from_updating_other_builders_builds()
    {
        // Create another builder
        $otherBuilder = AppBuilder::factory()->create([
            'server_key' => 'other-server-key',
            'platforms' => ['android-webview'],
        ]);

        // Create a build assigned to the other builder
        $build = AppBuild::factory()->create([
            'app_id' => $this->testApp->id,
            'platform' => 'android-webview',
            'status' => 'pending',
            'app_builder_id' => $otherBuilder->id,
        ]);

        // Try to update with our builder's key
        $response = $this->postJson("/api/v1/builds/{$build->id}/status/{$this->builder->server_key}", [
            'status' => 'completed',
            'artifact_path' => '/path/to/artifact.apk',
        ]);

        $response->assertForbidden();
    }
}
