<?php

namespace App\Console\Commands;

use App\Models\AppBuild;
use App\Models\SystemSetting;
use App\Services\AppetizeService;
use Illuminate\Console\Command;

class CleanupAppetizePreviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appetize:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old Appetize previews to save costs';

    /**
     * Execute the console command.
     */
    public function handle(AppetizeService $appetizeService)
    {
        if (! $appetizeService->isEnabled()) {
            $this->info('Appetize integration is not enabled');

            return Command::SUCCESS;
        }

        $retentionDays = SystemSetting::get('appetize_retention_days', 7);

        $oldBuilds = AppBuild::whereNotNull('appetize_public_key')
            ->where('appetize_uploaded_at', '<', now()->subDays($retentionDays))
            ->get();

        if ($oldBuilds->isEmpty()) {
            $this->info('No old Appetize previews to clean up');

            return Command::SUCCESS;
        }

        $deleted = 0;

        $this->info("Found {$oldBuilds->count()} old previews to delete...");

        foreach ($oldBuilds as $build) {
            try {
                if ($appetizeService->deleteApp($build->appetize_public_key)) {
                    $build->update([
                        'appetize_public_key' => null,
                        'appetize_preview_url' => null,
                        'appetize_uploaded_at' => null,
                    ]);
                    $deleted++;
                    $this->info("Deleted preview for build #{$build->id}");
                }
            } catch (\Exception $e) {
                $this->error("Failed to delete build #{$build->id}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully deleted {$deleted} Appetize previews");

        return Command::SUCCESS;
    }
}
