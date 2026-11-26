<?php

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AuditLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuditLogService;
    }

    public function test_logs_action_for_user(): void
    {
        $user = User::factory()->create();

        $this->service->log(
            action: 'data_export',
            user: $user,
            metadata: ['reason' => 'user_request']
        );

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'data_export',
        ]);
    }

    public function test_logs_action_with_actor(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->service->log(
            action: 'admin_action',
            user: $user,
            actor: $admin,
            metadata: ['action' => 'changed_subscription']
        );

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'actor_id' => $admin->id,
            'action' => 'admin_action',
        ]);
    }

    public function test_logs_entity_changes(): void
    {
        $user = User::factory()->create();

        $this->service->log(
            action: 'consent_changed',
            user: $user,
            entityType: 'UserConsent',
            entityId: 123,
            oldValues: ['consented' => false],
            newValues: ['consented' => true]
        );

        $log = AuditLog::where('action', 'consent_changed')->first();

        $this->assertEquals('UserConsent', $log->entity_type);
        $this->assertEquals(123, $log->entity_id);
        $this->assertEquals(['consented' => false], $log->old_values);
        $this->assertEquals(['consented' => true], $log->new_values);
    }

    public function test_captures_ip_and_user_agent(): void
    {
        $user = User::factory()->create();

        // Simulate request context
        $this->app['request']->server->set('REMOTE_ADDR', '192.168.1.1');
        $this->app['request']->headers->set('User-Agent', 'Test Browser');

        $this->service->log(
            action: 'account_deletion_requested',
            user: $user
        );

        $log = AuditLog::where('action', 'account_deletion_requested')->first();

        $this->assertEquals('192.168.1.1', $log->ip_address);
        $this->assertEquals('Test Browser', $log->user_agent);
    }

    public function test_logs_without_user(): void
    {
        $this->service->log(
            action: 'system_action',
            metadata: ['type' => 'automated_cleanup']
        );

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'system_action',
            'user_id' => null,
        ]);
    }
}
