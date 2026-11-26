<?php

namespace App\Console\Commands;

use App\Models\CronLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionRenewalReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:manage {--triggered-by=cron}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage subscriptions: check expirations, send reminders, downgrade expired users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $cronLog = CronLog::create([
            'job_name' => 'ManageSubscriptions',
            'job_class' => self::class,
            'status' => 'running',
            'started_at' => $startTime,
            'triggered_by' => $this->option('triggered-by'),
        ]);

        try {
            $this->info('Starting subscription management...');

            // Process expired subscriptions
            $expiredCount = $this->processExpiredSubscriptions();

            // Send renewal reminders
            $reminderCount = $this->sendRenewalReminders();

            $this->info("Processed {$expiredCount} expired subscriptions");
            $this->info("Sent {$reminderCount} renewal reminders");

            $message = sprintf(
                'Processed %d expired subscription(s) and sent %d reminder(s)',
                $expiredCount,
                $reminderCount
            );

            Log::info('Subscription management completed', [
                'expired_processed' => $expiredCount,
                'reminders_sent' => $reminderCount,
            ]);

            $cronLog->update([
                'status' => 'success',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $message,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $errorMessage = "Failed to manage subscriptions: {$e->getMessage()}";
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

            return Command::FAILURE;
        }
    }

    /**
     * Process expired subscriptions - downgrade users to free plan.
     */
    protected function processExpiredSubscriptions(): int
    {
        $count = 0;

        // Find active subscriptions that have ended
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->with(['user', 'plan'])
            ->get();

        // Get the default (free) plan
        $defaultPlanId = SystemSetting::get('default_plan_id', 1);
        $defaultPlan = Plan::find($defaultPlanId);

        foreach ($expiredSubscriptions as $subscription) {
            $user = $subscription->user;

            if (! $user) {
                continue;
            }

            // Mark subscription as expired
            $subscription->update(['status' => 'expired']);

            // Downgrade user to free plan
            $user->update([
                'plan_id' => $defaultPlanId,
                'build_credits' => $defaultPlan ? $defaultPlan->monthly_build_credits : 0,
            ]);

            // Send expiration notification
            try {
                $user->notify(new SubscriptionExpiredNotification($subscription));
            } catch (\Exception $e) {
                \Log::error('Failed to send SubscriptionExpiredNotification', [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $count++;

            $this->line("  - Expired subscription for: {$user->email}");
        }

        return $count;
    }

    /**
     * Send renewal reminders for subscriptions ending soon.
     */
    protected function sendRenewalReminders(): int
    {
        $count = 0;

        // Get subscriptions ending in 3 days (send first reminder)
        $threeDaysOut = now()->addDays(3)->startOfDay();
        $threeDaysOutEnd = now()->addDays(3)->endOfDay();

        $threeDaySubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [$threeDaysOut, $threeDaysOutEnd])
            ->with(['user', 'plan'])
            ->get();

        foreach ($threeDaySubscriptions as $subscription) {
            if ($subscription->user) {
                try {
                    $subscription->user->notify(new SubscriptionRenewalReminderNotification($subscription));
                    $count++;
                    $this->line("  - 3-day reminder sent to: {$subscription->user->email}");
                } catch (\Exception $e) {
                    \Log::error('Failed to send SubscriptionRenewalReminderNotification', [
                        'user_id' => $subscription->user->id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("  - Failed to send 3-day reminder to: {$subscription->user->email}");
                }
            }
        }

        // Get subscriptions ending tomorrow (send final reminder)
        $oneDayOut = now()->addDay()->startOfDay();
        $oneDayOutEnd = now()->addDay()->endOfDay();

        $oneDaySubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [$oneDayOut, $oneDayOutEnd])
            ->with(['user', 'plan'])
            ->get();

        foreach ($oneDaySubscriptions as $subscription) {
            if ($subscription->user) {
                try {
                    $subscription->user->notify(new SubscriptionRenewalReminderNotification($subscription));
                    $count++;
                    $this->line("  - 1-day reminder sent to: {$subscription->user->email}");
                } catch (\Exception $e) {
                    \Log::error('Failed to send SubscriptionRenewalReminderNotification', [
                        'user_id' => $subscription->user->id,
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("  - Failed to send 1-day reminder to: {$subscription->user->email}");
                }
            }
        }

        return $count;
    }
}
