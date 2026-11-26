<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Console\Command;

class PruneAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:prune-audit-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune audit logs older than the configured retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionDays = (int) SystemSetting::get('data_retention_days_audit_logs', 365);
        $cutoffDate = now()->subDays($retentionDays);

        $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

        AuditLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Pruned {$count} audit log(s) older than {$retentionDays} days.");

        return Command::SUCCESS;
    }
}
