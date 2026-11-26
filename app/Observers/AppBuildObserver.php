<?php

namespace App\Observers;

use App\Jobs\UploadToAppetizeJob;
use App\Models\AppBuild;
use App\Models\SystemSetting;
use App\Services\AppetizeService;

class AppBuildObserver
{
    /**
     * Handle the AppBuild "updated" event.
     */
    public function updated(AppBuild $appBuild): void
    {
        // Check if status changed to completed
        if ($appBuild->wasChanged('status') && $appBuild->status === 'completed') {
            // Check if Appetize is enabled globally
            if (! SystemSetting::get('appetize_enabled', false)) {
                return;
            }

            // Check if auto-upload is enabled
            if (! SystemSetting::get('appetize_auto_upload', true)) {
                return;
            }

            // Eager load relationships to avoid N+1 queries
            $appBuild->load('app.user.plan');

            // Check if user's plan allows Appetize
            if (! $appBuild->app->user->plan || ! $appBuild->app->user->plan->can_use_appetize) {
                return;
            }

            // Dispatch upload job after transaction commits
            // This ensures the build status is saved before Appetize upload runs
            dispatch(new UploadToAppetizeJob($appBuild))->afterCommit();
        }
    }

    /**
     * Handle the AppBuild "deleted" event.
     */
    public function deleted(AppBuild $appBuild): void
    {
        // Delete from Appetize if uploaded
        if ($appBuild->appetize_public_key) {
            try {
                $appetizeService = app(AppetizeService::class);
                $appetizeService->deleteApp($appBuild->appetize_public_key);
            } catch (\Exception $e) {
                // Log error for manual cleanup - don't block build deletion
                \Illuminate\Support\Facades\Log::error('Failed to delete Appetize app on build deletion', [
                    'build_id' => $appBuild->id,
                    'appetize_key' => $appBuild->appetize_public_key,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
