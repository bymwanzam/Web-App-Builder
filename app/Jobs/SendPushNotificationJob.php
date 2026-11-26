<?php

namespace App\Jobs;

use App\Models\AppNotification;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AppNotification $notification
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PushNotificationService $pushService): void
    {
        Log::info("Sending push notification #{$this->notification->id} for app {$this->notification->app_id}");

        $result = $pushService->sendToApp($this->notification);

        if ($result['success']) {
            Log::info("Push notification #{$this->notification->id} sent successfully");
        } else {
            Log::warning("Push notification #{$this->notification->id} failed: {$result['message']}");

            // If we've exhausted all retries, the notification is already marked as failed by the service
            if ($this->attempts() >= $this->tries) {
                Log::error("Push notification #{$this->notification->id} failed after {$this->tries} attempts");
            } else {
                // Re-throw to trigger retry
                throw new \Exception($result['message']);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Push notification job failed for notification #{$this->notification->id}: {$exception->getMessage()}");

        $this->notification->markAsFailed($exception->getMessage());
    }
}
