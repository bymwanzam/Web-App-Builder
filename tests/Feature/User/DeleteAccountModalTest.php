<?php

namespace Tests\Feature\User;

use App\Livewire\User\DeleteAccountModal;
use App\Models\AccountDeletionRequest;
use App\Models\User;
use App\Notifications\AccountDeletionScheduledNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteAccountModalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_account_deletion(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(DeleteAccountModal::class)
            ->set('password', 'password')
            ->call('requestDeletion')
            ->assertDispatched('toast')
            ->assertDispatched('closeDeleteAccountModal');

        $this->assertDatabaseHas('account_deletion_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Notification::assertSentTo($user, AccountDeletionScheduledNotification::class);
    }

    public function test_user_cannot_request_deletion_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(DeleteAccountModal::class)
            ->set('password', 'wrongpassword')
            ->call('requestDeletion')
            ->assertHasErrors(['password']);

        $this->assertDatabaseMissing('account_deletion_requests', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_request_deletion_when_pending_request_exists(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Create a pending request
        AccountDeletionRequest::createForUser($user);

        Livewire::actingAs($user)
            ->test(DeleteAccountModal::class)
            ->set('password', 'password')
            ->call('requestDeletion')
            ->assertDispatched('toast', type: 'error');

        // Should still only have one request
        $this->assertEquals(1, AccountDeletionRequest::where('user_id', $user->id)->count());
    }

    public function test_user_can_cancel_deletion_request(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $request = AccountDeletionRequest::createForUser($user);

        Livewire::actingAs($user)
            ->test(DeleteAccountModal::class)
            ->call('cancelDeletion')
            ->assertDispatched('toast');

        $request->refresh();
        $this->assertEquals('cancelled', $request->status);
        $this->assertNotNull($request->cancelled_at);
    }

    public function test_modal_shows_pending_deletion_status(): void
    {
        $user = User::factory()->create();
        AccountDeletionRequest::createForUser($user);

        Livewire::actingAs($user)
            ->test(DeleteAccountModal::class)
            ->assertViewHas('pendingDeletion', true);
    }
}
