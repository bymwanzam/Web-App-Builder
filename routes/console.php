<?php

use App\Jobs\CheckBuildTimeouts;
use App\Jobs\TriggerBuilders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Builder Scheduled Tasks
Schedule::job(new TriggerBuilders)->everyMinute();
Schedule::job(new CheckBuildTimeouts)->everyFifteenMinutes();

// Subscription Management
Schedule::command('subscriptions:manage')->daily();

// Monthly Build Credits Reset (1st of each month at midnight)
Schedule::command('credits:reset')->monthlyOn(1, '00:00');

// Push Notifications Processing
Schedule::command('notifications:process-scheduled')->everyMinute();

// Cron Log Cleanup
Schedule::command('cron:prune')->daily();

// Appetize Preview Cleanup
Schedule::command('appetize:cleanup')->daily();

// GDPR Data Retention
Schedule::command('data:prune-exports')->daily();
Schedule::command('data:prune-builds')->daily();
Schedule::command('data:prune-audit-logs')->weekly();
Schedule::command('accounts:process-deletions')->daily();
