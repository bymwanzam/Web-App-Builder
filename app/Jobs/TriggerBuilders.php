<?php

namespace App\Jobs;

use App\Models\AppBuilder;
use App\Models\CronLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TriggerBuilders implements ShouldQueue
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
            'job_name' => 'TriggerBuilders',
            'job_class' => self::class,
            'status' => 'running',
            'started_at' => $startTime,
            'triggered_by' => $this->triggeredBy,
        ]);

        try {
            $builders = AppBuilder::active()->get();
            $buildersTriggered = [];
            $failedBuilders = [];
            $totalPendingBuilds = \App\Models\AppBuild::whereNull('app_builder_id')
                ->where('status', 'pending')
                ->count();

            if ($builders->isEmpty()) {
                $message = 'No active builders found';
                Log::warning($message);
                $cronLog->update([
                    'status' => 'success',
                    'completed_at' => now(),
                    'duration' => now()->diffInSeconds($startTime),
                    'message' => $message,
                    'builds_processed' => 0,
                ]);

                return;
            }

            if ($totalPendingBuilds === 0) {
                $message = 'No pending builds to trigger';
                Log::info($message);
                $cronLog->update([
                    'status' => 'success',
                    'completed_at' => now(),
                    'duration' => now()->diffInSeconds($startTime),
                    'message' => $message,
                    'builds_processed' => 0,
                    'servers_triggered' => [],
                ]);

                return;
            }

            // Trigger each active builder
            foreach ($builders as $builder) {
                try {
                    $triggered = $builder->triggerBuildFetch();
                    if ($triggered) {
                        $buildersTriggered[] = $builder->id;
                        Log::info("Successfully triggered builder: {$builder->name}");
                    } else {
                        $failedBuilders[] = $builder->name;
                        Log::warning("Failed to trigger builder: {$builder->name}");
                    }
                } catch (\Exception $e) {
                    $failedBuilders[] = $builder->name;
                    Log::error("Exception triggering builder {$builder->name}: {$e->getMessage()}");
                }
            }

            $message = sprintf(
                'Triggered %d builder(s) for %d pending build(s). Failed: %d',
                count($buildersTriggered),
                $totalPendingBuilds,
                count($failedBuilders)
            );

            Log::info($message, [
                'builders_triggered' => $buildersTriggered,
                'failed_builders' => $failedBuilders,
                'pending_builds' => $totalPendingBuilds,
            ]);

            $cronLog->update([
                'status' => 'success',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $message,
                'builds_processed' => $totalPendingBuilds,
                'servers_triggered' => $buildersTriggered,
            ]);
        } catch (\Exception $e) {
            $errorMessage = "Failed to trigger builders: {$e->getMessage()}";
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
