<?php

namespace App\Jobs;

use App\Models\AppBuild;
use App\Services\AppetizeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UploadToAppetizeJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AppBuild $build
    ) {
        $this->onQueue('appetize');
    }

    /**
     * Execute the job.
     */
    public function handle(AppetizeService $appetizeService): void
    {
        try {
            // Only upload if build is completed and eligible
            if ($this->build->status !== 'completed') {
                Log::warning('Attempted to upload non-completed build to Appetize', [
                    'build_id' => $this->build->id,
                    'status' => $this->build->status,
                ]);

                return;
            }

            // Check if already uploaded
            if ($this->build->appetize_public_key) {
                Log::info('Build already uploaded to Appetize', [
                    'build_id' => $this->build->id,
                    'public_key' => $this->build->appetize_public_key,
                ]);

                return;
            }

            // Upload to Appetize
            $result = $appetizeService->uploadBuild($this->build);

            Log::info('Successfully uploaded build to Appetize', [
                'build_id' => $this->build->id,
                'public_key' => $result['publicKey'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to upload build to Appetize', [
                'build_id' => $this->build->id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Appetize upload job failed after all retries', [
            'build_id' => $this->build->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
