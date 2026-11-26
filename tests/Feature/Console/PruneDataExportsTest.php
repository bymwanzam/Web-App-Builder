<?php

namespace Tests\Feature\Console;

use App\Models\DataExportRequest;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PruneDataExportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_prunes_expired_data_exports(): void
    {
        $user = User::factory()->create();
        SystemSetting::set('data_retention_days_data_exports', '7');

        // Create expired export (10 days ago)
        $expiredExport = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(3),
            'file_path' => 'exports/old-export.zip',
        ]);
        Storage::disk('local')->put('exports/old-export.zip', 'old content');

        // Create valid export (expires in 5 days)
        $validExport = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'expires_at' => now()->addDays(5),
            'file_path' => 'exports/valid-export.zip',
        ]);
        Storage::disk('local')->put('exports/valid-export.zip', 'valid content');

        $this->artisan('data:prune-exports')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('data_export_requests', ['id' => $expiredExport->id]);
        $this->assertDatabaseHas('data_export_requests', ['id' => $validExport->id]);
        Storage::disk('local')->assertMissing('exports/old-export.zip');
        Storage::disk('local')->assertExists('exports/valid-export.zip');
    }

    public function test_deletes_associated_files(): void
    {
        $user = User::factory()->create();

        $export = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(1),
            'file_path' => 'exports/test-export.zip',
        ]);
        Storage::disk('local')->put('exports/test-export.zip', 'test content');

        $this->artisan('data:prune-exports')
            ->assertExitCode(0);

        Storage::disk('local')->assertMissing('exports/test-export.zip');
    }

    public function test_handles_missing_files_gracefully(): void
    {
        $user = User::factory()->create();

        // Create export with non-existent file
        DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(1),
            'file_path' => 'exports/missing-file.zip',
        ]);

        // Should not throw an exception
        $this->artisan('data:prune-exports')
            ->assertExitCode(0);
    }

    public function test_outputs_number_of_pruned_exports(): void
    {
        $user = User::factory()->create();

        DataExportRequest::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'expires_at' => now()->subDays(1),
        ]);

        $this->artisan('data:prune-exports')
            ->expectsOutputToContain('3')
            ->assertExitCode(0);
    }
}
