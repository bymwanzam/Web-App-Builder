<?php

namespace Tests\Feature;

use App\Models\DataExportRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataExportDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_download_completed_export(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $request = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'file_path' => 'exports/test.zip',
            'expires_at' => now()->addDays(7),
        ]);

        // Create a fake file
        Storage::disk('local')->put('exports/test.zip', 'fake zip content');

        $response = $this->get(route('data-export.download', ['token' => $request->download_token]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/zip');
    }

    public function test_expired_export_returns_410(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $request = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'file_path' => 'exports/test.zip',
            'expires_at' => now()->subDays(1),
        ]);

        Storage::disk('local')->put('exports/test.zip', 'fake zip content');

        $response = $this->get(route('data-export.download', ['token' => $request->download_token]));

        $response->assertStatus(410);
    }

    public function test_invalid_token_returns_404(): void
    {
        $response = $this->get(route('data-export.download', ['token' => 'invalid-token']));

        $response->assertStatus(404);
    }

    public function test_pending_export_returns_404(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->get(route('data-export.download', ['token' => $request->download_token]));

        $response->assertStatus(404);
    }
}
