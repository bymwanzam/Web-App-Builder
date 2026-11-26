<?php

namespace Tests\Feature\Console;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneAuditLogsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prunes_audit_logs_older_than_retention_period(): void
    {
        SystemSetting::set('data_retention_days_audit_logs', '30');

        // Create old log (35 days ago)
        $oldLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(35),
        ]);

        // Create recent log (10 days ago)
        $recentLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(10),
        ]);

        $this->artisan('data:prune-audit-logs')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('audit_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('audit_logs', ['id' => $recentLog->id]);
    }

    public function test_uses_default_retention_period_when_setting_not_configured(): void
    {
        // Don't set retention period, should use default of 365 days

        // Create log from 400 days ago
        $oldLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(400),
        ]);

        // Create log from 300 days ago
        $recentLog = AuditLog::factory()->create([
            'created_at' => now()->subDays(300),
        ]);

        $this->artisan('data:prune-audit-logs')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('audit_logs', ['id' => $oldLog->id]);
        $this->assertDatabaseHas('audit_logs', ['id' => $recentLog->id]);
    }

    public function test_outputs_number_of_pruned_records(): void
    {
        SystemSetting::set('data_retention_days_audit_logs', '30');

        AuditLog::factory()->count(5)->create([
            'created_at' => now()->subDays(35),
        ]);

        $this->artisan('data:prune-audit-logs')
            ->expectsOutputToContain('5')
            ->assertExitCode(0);
    }

    public function test_handles_no_logs_to_prune(): void
    {
        SystemSetting::set('data_retention_days_audit_logs', '30');

        // Only create recent logs
        AuditLog::factory()->count(3)->create([
            'created_at' => now()->subDays(10),
        ]);

        $this->artisan('data:prune-audit-logs')
            ->expectsOutputToContain('0')
            ->assertExitCode(0);
    }
}
