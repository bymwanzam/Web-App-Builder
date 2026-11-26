<?php

namespace Tests\Feature\Console;

use App\Models\App;
use App\Models\AppBuild;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PruneExpiredBuildsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_prunes_expired_builds(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('data_retention_days_expired_builds', '90');

        // Create expired build
        $expiredBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(10),
            'artifact_url' => 'builds/old-build.apk',
        ]);
        Storage::disk('public')->put('builds/old-build.apk', 'old build content');

        // Create valid build
        $validBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => now()->addDays(30),
            'artifact_url' => 'builds/valid-build.apk',
        ]);
        Storage::disk('public')->put('builds/valid-build.apk', 'valid build content');

        $this->artisan('data:prune-builds')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('app_builds', ['id' => $expiredBuild->id]);
        $this->assertDatabaseHas('app_builds', ['id' => $validBuild->id]);
        Storage::disk('public')->assertMissing('builds/old-build.apk');
        Storage::disk('public')->assertExists('builds/valid-build.apk');
    }

    public function test_prunes_builds_without_expires_at_using_retention_period(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('data_retention_days_expired_builds', '90');

        // Create old build without expires_at (100 days old)
        $oldBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => null,
            'created_at' => now()->subDays(100),
        ]);

        // Create recent build without expires_at (50 days old)
        $recentBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => null,
            'created_at' => now()->subDays(50),
        ]);

        $this->artisan('data:prune-builds')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('app_builds', ['id' => $oldBuild->id]);
        $this->assertDatabaseHas('app_builds', ['id' => $recentBuild->id]);
    }

    public function test_does_not_prune_pending_or_building_builds(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);
        SystemSetting::set('data_retention_days_expired_builds', '7');

        // Create old pending build
        $pendingBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'pending',
            'expires_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
        ]);

        // Create old building build
        $buildingBuild = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'building',
            'expires_at' => now()->subDays(10),
            'created_at' => now()->subDays(10),
        ]);

        $this->artisan('data:prune-builds')
            ->assertExitCode(0);

        // Pending and building builds should not be deleted
        $this->assertDatabaseHas('app_builds', ['id' => $pendingBuild->id]);
        $this->assertDatabaseHas('app_builds', ['id' => $buildingBuild->id]);
    }

    public function test_deletes_associated_artifacts(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);

        $build = AppBuild::factory()->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(1),
            'artifact_url' => 'builds/test-build.apk',
        ]);
        Storage::disk('public')->put('builds/test-build.apk', 'test content');

        $this->artisan('data:prune-builds')
            ->assertExitCode(0);

        Storage::disk('public')->assertMissing('builds/test-build.apk');
    }

    public function test_outputs_number_of_pruned_builds(): void
    {
        $user = User::factory()->create();
        $app = App::factory()->create(['user_id' => $user->id]);

        AppBuild::factory()->count(4)->create([
            'app_id' => $app->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(1),
        ]);

        $this->artisan('data:prune-builds')
            ->expectsOutputToContain('4')
            ->assertExitCode(0);
    }
}
