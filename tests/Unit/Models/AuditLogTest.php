<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_audit_log_belongs_to_actor(): void
    {
        $user = User::factory()->create();
        $actor = User::factory()->create();
        $log = AuditLog::factory()->create([
            'user_id' => $user->id,
            'actor_id' => $actor->id,
        ]);

        $this->assertInstanceOf(User::class, $log->actor);
        $this->assertEquals($actor->id, $log->actor->id);
    }

    public function test_audit_log_casts_json_fields(): void
    {
        $log = AuditLog::factory()->create([
            'old_values' => ['name' => 'Old Name'],
            'new_values' => ['name' => 'New Name'],
            'metadata' => ['source' => 'api'],
        ]);

        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);
        $this->assertIsArray($log->metadata);
    }

    public function test_audit_log_user_can_be_null(): void
    {
        $log = AuditLog::factory()->create(['user_id' => null]);

        $this->assertNull($log->user);
    }

    public function test_prune_old_deletes_logs_older_than_days(): void
    {
        // Create old logs
        AuditLog::factory()->count(3)->create([
            'created_at' => now()->subDays(400),
        ]);

        // Create recent logs
        AuditLog::factory()->count(2)->create([
            'created_at' => now()->subDays(30),
        ]);

        $deleted = AuditLog::pruneOld(365);

        $this->assertEquals(3, $deleted);
        $this->assertDatabaseCount('audit_logs', 2);
    }

    public function test_audit_log_captures_action(): void
    {
        $log = AuditLog::factory()->create(['action' => 'data_export']);

        $this->assertEquals('data_export', $log->action);
    }

    public function test_audit_log_stores_entity_type_and_id(): void
    {
        $user = User::factory()->create();
        $log = AuditLog::factory()->create([
            'entity_type' => User::class,
            'entity_id' => $user->id,
        ]);

        $this->assertEquals(User::class, $log->entity_type);
        $this->assertEquals($user->id, $log->entity_id);
    }
}
