<?php

namespace App\Console\Commands;

use App\Models\CronLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetBuildCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:reset {--triggered-by=cron}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset build credits for all users based on their current plan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $cronLog = CronLog::create([
            'job_name' => 'ResetBuildCredits',
            'job_class' => self::class,
            'status' => 'running',
            'started_at' => $startTime,
            'triggered_by' => $this->option('triggered-by'),
        ]);

        try {
            $this->info('Starting monthly build credits reset...');

            $count = 0;
            $skipped = 0;

            // Get all active users with plans
            $users = User::where('status', 'active')
                ->whereNotNull('plan_id')
                ->with('plan')
                ->get();

            foreach ($users as $user) {
                if (! $user->plan) {
                    $skipped++;

                    continue;
                }

                // Get the plan's monthly build credits
                $monthlyCredits = $user->plan->monthly_build_credits;

                // Skip unlimited plans (-1)
                if ($monthlyCredits === -1) {
                    $this->line("  - {$user->email}: Unlimited credits (skipped)");
                    $skipped++;

                    continue;
                }

                // Reset credits to plan's monthly allocation
                $oldCredits = $user->build_credits;
                $user->update(['build_credits' => $monthlyCredits]);

                $this->line("  - {$user->email}: {$oldCredits} → {$monthlyCredits} credits");
                $count++;
            }

            $this->info("Reset credits for {$count} users");
            $this->info("Skipped {$skipped} users (unlimited plans or no plan)");

            $message = sprintf(
                'Reset credits for %d user(s), skipped %d user(s)',
                $count,
                $skipped
            );

            Log::info('Monthly build credits reset completed', [
                'users_reset' => $count,
                'users_skipped' => $skipped,
            ]);

            $cronLog->update([
                'status' => 'success',
                'completed_at' => now(),
                'duration' => now()->diffInSeconds($startTime),
                'message' => $message,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $errorMessage = "Failed to reset build credits: {$e->getMessage()}";
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
}
