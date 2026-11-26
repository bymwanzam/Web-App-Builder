<?php

namespace App\Console\Commands;

use App\Jobs\CheckBuildTimeouts;
use App\Jobs\TriggerBuilders;
use Illuminate\Console\Command;

class TriggerCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:trigger {job : The job to trigger (trigger-builds|check-timeouts|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually trigger a specific cron job';

    /**
     * Available jobs mapping.
     */
    protected $jobs = [
        'trigger-builds' => [
            'class' => TriggerBuilders::class,
            'name' => 'Trigger Builders',
        ],
        'check-timeouts' => [
            'class' => CheckBuildTimeouts::class,
            'name' => 'Check Build Timeouts',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $job = $this->argument('job');
        $triggeredBy = 'manual:'.(auth()->id() ?? 'cli');

        if ($job === 'all') {
            $this->info('Triggering all cron jobs...');

            foreach ($this->jobs as $key => $jobConfig) {
                $this->triggerJob($key, $jobConfig, $triggeredBy);
            }

            $this->info('All jobs triggered successfully.');

            return Command::SUCCESS;
        }

        if (! isset($this->jobs[$job])) {
            $this->error("Invalid job: {$job}");
            $this->info('Available jobs: '.implode(', ', array_keys($this->jobs)).', all');

            return Command::FAILURE;
        }

        $this->triggerJob($job, $this->jobs[$job], $triggeredBy);

        return Command::SUCCESS;
    }

    /**
     * Trigger a specific job.
     */
    protected function triggerJob($key, $jobConfig, $triggeredBy)
    {
        $this->info("Triggering: {$jobConfig['name']}...");

        try {
            $jobClass = $jobConfig['class'];
            dispatch(new $jobClass($triggeredBy));

            $this->info("✓ {$jobConfig['name']} triggered successfully.");
        } catch (\Exception $e) {
            $this->error("✗ Failed to trigger {$jobConfig['name']}: {$e->getMessage()}");
        }
    }
}
