<?php

namespace App\Console\Commands;

use App\Models\CronLog;
use Illuminate\Console\Command;

class PruneCronLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:prune {--days=30 : Number of days to retain logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete cron logs older than specified days (default: 30 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');

        $this->info("Pruning cron logs older than {$days} days...");

        $deleted = CronLog::pruneOld($days);

        if ($deleted > 0) {
            $this->info("Successfully deleted {$deleted} old cron log(s).");
        } else {
            $this->info('No old cron logs found to delete.');
        }

        return Command::SUCCESS;
    }
}
