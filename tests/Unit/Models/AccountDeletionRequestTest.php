<?php

namespace Tests\Unit\Models;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeletionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_deletion_request_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $request->user);
        $this->assertEquals($user->id, $request->user->id);
    }

    public function test_account_deletion_request_casts_dates(): void
    {
        $request = AccountDeletionRequest::factory()->create([
            'scheduled_at' => now()->addDays(7),
            'confirmed_at' => now(),
            'cancelled_at' => null,
            'completed_at' => null,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $request->scheduled_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $request->confirmed_at);
    }

    public function test_create_for_user_generates_tokens(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::createForUser($user);

        $this->assertNotNull($request->confirmation_token);
        $this->assertNotNull($request->cancellation_token);
        $this->assertEquals(64, strlen($request->confirmation_token));
        $this->assertEquals(64, strlen($request->cancellation_token));
    }

    public function test_create_for_user_sets_pending_status(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::createForUser($user);

        $this->assertEquals('pending', $request->status);
    }

    public function test_create_for_user_schedules_deletion_with_grace_period(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::createForUser($user);

        // Default grace period is 7 days
        $this->assertTrue($request->scheduled_at->isAfter(now()->addDays(6)));
        $this->assertTrue($request->scheduled_at->isBefore(now()->addDays(8)));
    }

    public function test_account_deletion_request_has_valid_statuses(): void
    {
        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];

        foreach ($validStatuses as $status) {
            $request = AccountDeletionRequest::factory()->create(['status' => $status]);
            $this->assertEquals($status, $request->status);
        }
    }

    public function test_is_past_grace_period_returns_true_when_scheduled_at_is_past(): void
    {
        $request = AccountDeletionRequest::factory()->create([
            'scheduled_at' => now()->subHour(),
            'status' => 'pending',
        ]);

        $this->assertTrue($request->isPastGracePeriod());
    }

    public function test_is_past_grace_period_returns_false_when_scheduled_at_is_future(): void
    {
        $request = AccountDeletionRequest::factory()->create([
            'scheduled_at' => now()->addDays(5),
            'status' => 'pending',
        ]);

        $this->assertFalse($request->isPastGracePeriod());
    }

    public function test_can_be_cancelled_returns_true_for_pending_request(): void
    {
        $request = AccountDeletionRequest::factory()->create([
            'status' => 'pending',
            'scheduled_at' => now()->addDays(5),
        ]);

        $this->assertTrue($request->canBeCancelled());
    }

    public function test_can_be_cancelled_returns_false_for_completed_request(): void
    {
        $request = AccountDeletionRequest::factory()->create([
            'status' => 'completed',
        ]);

        $this->assertFalse($request->canBeCancelled());
    }

    public function test_deletion_requests_are_deleted_on_user_cascade(): void
    {
        $user = User::factory()->create();
        AccountDeletionRequest::factory()->count(2)->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseCount('account_deletion_requests', 0);
    }
}
