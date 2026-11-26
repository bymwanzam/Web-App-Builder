<?php

namespace App\Jobs;

use App\Models\AppBuild;
use App\Models\CronLog;
use App\Notifications\BuildFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckBuildTimeouts implements ShouldQueue
{
    use Queueable;

    protected $triggeredBy;

    /**
     * Create a new job instance.
     */
    public function __construct($triggeredBy = 'cron')
    {
        $this->triggeredBy = $triggeredBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = now();
        $cronLog = CronLog::create([
            'job_name' => 'CheckBuildTimeouts',
            'job_class' => self::class,
            'status' => 'running',
            'started_at' => $startTime,
            'triggered_by' => $this->triggeredBy,
        ]);

        try {
            // Find builds that have been in processing state for more than 30 minutes
            $timeoutBuilds = AppBuild::whereIn('status', ['preparing', 'building_android', 'processing'])
                ->where('started_at', '<', now()->subMinutes(30))
                ->get();

            // Wrap database operations in a transaction
            DB::transaction(function () use ($timeoutBuilds) {
                foreach ($timeoutBuilds as $build) {
                    $build->update([
                        'status' => 'failed',
                        'error_message' => 'Build timeout - Server may be offline or build took too long',
                        'completed_at' => now(),
                    ]);

                    // Decrement builder build count
                    if ($build->appBuilder) {
                        $build->appBuilder->decrementBuildCount();
                    }

                    // Send failure notification
                    try {
                        $build->app->user->notify(new BuildFailedNotification($build));
                    } catch (\Exception $e) {
                        Log::error("Failed to send notification for build {$build->id}: {$e->getMessage()}");
                    }

                    Log::warning("Build {$build->id} marked as failed due to timeout");
                }
            });

            $message = $timeoutBuilds->count() > 0
                ? "Marked {$timeoutBuilds->count()} build(s) as failed due to timeout"
                : 'No timed-out builds found';

            Log::info($message);

            $cronLog->update([
                'status' => 'success',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $message,
                'builds_processed' => $timeoutBuilds->count(),
            ]);
        } catch (\Exception $e) {
            $errorMessage = "Failed to check build timeouts: {$e->getMessage()}";
            Log::error($errorMessage, [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $cronLog->update([
                'status' => 'failed',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $errorMessage,
                'exception' => $e->getTraceAsString(),
            ]);
        }
    }
}
