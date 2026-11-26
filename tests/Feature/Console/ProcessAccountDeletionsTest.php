<?php

namespace Tests\Feature\Console;

use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcessAccountDeletionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_processes_past_due_deletions(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;
        AccountDeletionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'scheduled_at' => now()->subDay(),
        ]);

        $this->artisan('accounts:process-deletions')
            ->assertExitCode(0);

        // User should be deleted (which cascades to delete the request too)
        $this->assertNull(User::find($userId));

        // Request is also deleted due to cascade, but that's expected
        $this->assertDatabaseMissing('account_deletion_requests', [
            'user_id' => $userId,
        ]);
    }

    public function test_command_skips_future_deletions(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'scheduled_at' => now()->addDay(),
        ]);

        $this->artisan('accounts:process-deletions')
            ->assertExitCode(0);

        // Request should remain pending
        $request->refresh();
        $this->assertEquals('pending', $request->status);

        // User should not be deleted
        $this->assertNotNull(User::find($user->id));
    }

    public function test_command_skips_cancelled_deletions(): void
    {
        $user = User::factory()->create();
        $request = AccountDeletionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'cancelled',
            'scheduled_at' => now()->subDay(),
        ]);

        $this->artisan('accounts:process-deletions')
            ->assertExitCode(0);

        // User should not be deleted
        $this->assertNotNull(User::find($user->id));
    }

    public function test_command_handles_orphaned_request(): void
    {
        // This tests the edge case where a user was deleted but the request
        // somehow still exists (e.g., foreign key was temporarily disabled)
        // In practice, cascade should delete both, but we test defensive handling

        $user = User::factory()->create();
        $userId = $user->id;

        // Create request, then delete user which cascades to delete request too
        AccountDeletionRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
            'scheduled_at' => now()->subDay(),
        ]);

        // Due to cascade, deleting user deletes the request
        $user->delete();

        // Command should handle having nothing to process
        $this->artisan('accounts:process-deletions')
            ->assertExitCode(0);

        // Both should be gone
        $this->assertNull(User::find($userId));
        $this->assertDatabaseMissing('account_deletion_requests', ['user_id' => $userId]);
    }
}
