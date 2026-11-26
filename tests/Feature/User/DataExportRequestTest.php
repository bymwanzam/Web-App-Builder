<?php

namespace Tests\Feature\User;

use App\Jobs\ProcessDataExport;
use App\Livewire\User\DataExportCard;
use App\Models\DataExportRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class DataExportRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_data_export(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DataExportCard::class)
            ->call('requestExport')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('data_export_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessDataExport::class);
    }

    public function test_user_cannot_request_export_within_rate_limit(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        // Create a recent request
        DataExportRequest::createForUser($user);

        Livewire::actingAs($user)
            ->test(DataExportCard::class)
            ->call('requestExport')
            ->assertDispatched('toast', type: 'error');

        // Should only have one request
        $this->assertEquals(1, DataExportRequest::where('user_id', $user->id)->count());
    }

    public function test_data_export_card_shows_recent_exports(): void
    {
        $user = User::factory()->create();

        // Create some export requests
        DataExportRequest::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        Livewire::actingAs($user)
            ->test(DataExportCard::class)
            ->assertViewHas('exportRequests', function ($requests) {
                return $requests->count() === 3;
            });
    }

    public function test_data_export_card_shows_active_export_indicator(): void
    {
        $user = User::factory()->create();

        // Create an active request
        DataExportRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'processing',
        ]);

        Livewire::actingAs($user)
            ->test(DataExportCard::class)
            ->assertViewHas('hasActiveExport', true)
            ->assertViewHas('canRequestExport', false);
    }
}
