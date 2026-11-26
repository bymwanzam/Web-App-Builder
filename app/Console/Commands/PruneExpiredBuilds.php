<?php

namespace App\Console\Commands;

use App\Models\AppBuild;
use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneExpiredBuilds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:prune-builds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired build artifacts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionDays = (int) SystemSetting::get('data_retention_days_expired_builds', 90);
        $cutoffDate = now()->subDays($retentionDays);

        // Get builds that are expired or old enough to be pruned
        // Don't prune pending or building builds
        $expiredBuilds = AppBuild::where(function ($query) use ($cutoffDate) {
            $query->where('expires_at', '<', now())
                ->orWhere(function ($q) use ($cutoffDate) {
                    $q->whereNull('expires_at')
                        ->where('created_at', '<', $cutoffDate);
                });
        })
            ->whereIn('status', ['completed', 'failed'])
            ->get();

        $count = $expiredBuilds->count();

        foreach ($expiredBuilds as $build) {
            // Delete the artifact file if it exists
            if ($build->artifact_url && Storage::disk('public')->exists($build->artifact_url)) {
                Storage::disk('public')->delete($build->artifact_url);
            }

            // Delete the build record
            $build->delete();
        }

        $this->info("Pruned {$count} expired build(s).");

        return Command::SUCCESS;
    }
}
