<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotificationJob;
use App\Models\AppNotification;
use App\Models\CronLog;
use Illuminate\Console\Command;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process-scheduled {--triggered-by=cron}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled push notifications that are due for sending';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = now();
        $cronLog = CronLog::create([
            'job_name' => 'ProcessScheduledNotifications',
            'job_class' => self::class,
            'status' => 'running',
            'started_at' => $startTime,
            'triggered_by' => $this->option('triggered-by'),
        ]);

        try {
            $notifications = AppNotification::dueForSending()
                ->with(['app', 'app.pushNotificationConfig'])
                ->get();

            if ($notifications->isEmpty()) {
                $this->info('No scheduled notifications to process.');

                $cronLog->update([
                    'status' => 'success',
                    'completed_at' => now(),
                    'duration' => now()->diffInSeconds($startTime),
                    'message' => 'No scheduled notifications to process',
                ]);

                return Command::SUCCESS;
            }

            $this->info("Processing {$notifications->count()} scheduled notification(s)...");

            foreach ($notifications as $notification) {
                // Update status to pending so it won't be picked up again
                $notification->update(['status' => 'pending']);

                // Dispatch the job
                SendPushNotificationJob::dispatch($notification);

                $this->line("  - Dispatched notification #{$notification->id} for app: {$notification->app->name}");
            }

            $this->info('All scheduled notifications have been dispatched.');

            $message = sprintf(
                'Dispatched %d scheduled notification(s)',
                $notifications->count()
            );

            $cronLog->update([
                'status' => 'success',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $message,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $errorMessage = "Failed to process scheduled notifications: {$e->getMessage()}";

            $cronLog->update([
                'status' => 'failed',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $errorMessage,
                'exception' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
