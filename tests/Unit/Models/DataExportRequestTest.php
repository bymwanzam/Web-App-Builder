<?php

namespace Tests\Unit\Models;

use App\Models\DataExportRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataExportRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_data_export_request_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $request->user);
        $this->assertEquals($user->id, $request->user->id);
    }

    public function test_data_export_request_casts_dates(): void
    {
        $request = DataExportRequest::factory()->create([
            'expires_at' => now()->addDays(7),
            'completed_at' => now(),
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $request->expires_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $request->completed_at);
    }

    public function test_create_for_user_generates_token(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->assertNotNull($request->download_token);
        $this->assertEquals(64, strlen($request->download_token));
    }

    public function test_create_for_user_sets_pending_status(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->assertEquals('pending', $request->status);
    }

    public function test_create_for_user_sets_expiry(): void
    {
        $user = User::factory()->create();
        $request = DataExportRequest::createForUser($user);

        $this->assertTrue($request->expires_at->isAfter(now()));
        $this->assertTrue($request->expires_at->isBefore(now()->addDays(8)));
    }

    public function test_is_expired_returns_true_for_expired_request(): void
    {
        $request = DataExportRequest::factory()->create([
            'expires_at' => now()->subDay(),
        ]);

        $this->assertTrue($request->isExpired());
    }

    public function test_is_expired_returns_false_for_valid_request(): void
    {
        $request = DataExportRequest::factory()->create([
            'expires_at' => now()->addDay(),
        ]);

        $this->assertFalse($request->isExpired());
    }

    public function test_data_export_request_has_valid_statuses(): void
    {
        $validStatuses = ['pending', 'processing', 'completed', 'failed', 'expired'];

        foreach ($validStatuses as $status) {
            $request = DataExportRequest::factory()->create(['status' => $status]);
            $this->assertEquals($status, $request->status);
        }
    }

    public function test_user_export_requests_are_deleted_on_cascade(): void
    {
        $user = User::factory()->create();
        DataExportRequest::factory()->count(2)->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseCount('data_export_requests', 0);
    }
}
