<?php

namespace App\Livewire\Admin;

use App\Jobs\CheckBuildTimeouts;
use App\Jobs\TriggerBuilders;
use App\Models\CronLog;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class CronjobsTable extends Component
{
    protected $jobs = [
        [
            'name' => 'Trigger Builders',
            'class' => 'TriggerBuilders',
            'job_class' => TriggerBuilders::class,
            'command' => null,
            'schedule' => 'Every minute',
            'cron' => '* * * * *',
        ],
        [
            'name' => 'Check Build Timeouts',
            'class' => 'CheckBuildTimeouts',
            'job_class' => CheckBuildTimeouts::class,
            'command' => null,
            'schedule' => 'Every 15 minutes',
            'cron' => '*/15 * * * *',
        ],
        [
            'name' => 'Manage Subscriptions',
            'class' => 'ManageSubscriptions',
            'job_class' => null,
            'command' => 'subscriptions:manage',
            'schedule' => 'Daily',
            'cron' => '0 0 * * *',
        ],
        [
            'name' => 'Reset Build Credits',
            'class' => 'ResetBuildCredits',
            'job_class' => null,
            'command' => 'credits:reset',
            'schedule' => 'Monthly',
            'cron' => '0 0 1 * *',
        ],
        [
            'name' => 'Process Scheduled Notifications',
            'class' => 'ProcessScheduledNotifications',
            'job_class' => null,
            'command' => 'notifications:process-scheduled',
            'schedule' => 'Every minute',
            'cron' => '* * * * *',
        ],
    ];

    public function getJobsWithStatsProperty()
    {
        return collect($this->jobs)->map(function ($job) {
            $latest = CronLog::where('job_name', $job['class'])
                ->orderBy('completed_at', 'desc')
                ->first();

            $job['last_run'] = $latest ? $latest->completed_at : null;
            $job['status'] = $latest ? $latest->status : 'pending';
            $job['next_run'] = $this->calculateNextRun($job['schedule']);

            return $job;
        });
    }

    protected function calculateNextRun($schedule)
    {
        return match ($schedule) {
            'Every minute' => now()->addMinute()->startOfMinute(),
            'Every 15 minutes' => now()->addMinutes(15 - (now()->minute % 15))->startOfMinute(),
            'Daily' => now()->addDay()->startOfDay(),
            'Monthly' => now()->day(1)->addMonth()->startOfDay(),
            default => null,
        };
    }

    public function triggerJob($jobClass)
    {
        // Verify admin authorization
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $job = collect($this->jobs)->firstWhere('class', $jobClass);

        if (! $job) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Invalid job selected.'
            );

            return;
        }

        try {
            // Handle job classes (TriggerBuilders, CheckBuildTimeouts)
            if ($job['job_class']) {
                $triggeredBy = 'manual:'.auth()->id();
                $jobInstance = new ($job['job_class'])($triggeredBy);
                dispatch($jobInstance);
            }
            // Handle artisan commands (ManageSubscriptions, ResetBuildCredits, etc.)
            elseif ($job['command']) {
                Artisan::call($job['command'], ['--triggered-by' => 'manual:'.auth()->id()]);
            } else {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: "{$job['name']} cannot be executed manually."
                );

                return;
            }

            $this->dispatch('show-toast',
                type: 'success',
                message: "{$job['name']} executed successfully! Check the Execution Logs tab for details."
            );

            // Dispatch event to refresh logs and switch to logs tab
            $this->dispatch('jobExecuted');
        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: "Failed to execute job: {$e->getMessage()}"
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.cronjobs-table');
    }
}
