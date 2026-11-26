<?php

namespace App\Policies;

use App\Models\AppBuild;
use App\Models\User;

class AppBuildPolicy
{
    /**
     * Determine if the user can view the build.
     */
    public function view(User $user, AppBuild $build): bool
    {
        // User can view if they own the app
        return $user->id === $build->app->user_id;
    }

    /**
     * Determine if the user can download the build.
     */
    public function download(User $user, AppBuild $build): bool
    {
        // User can download if they own the app and build is completed
        return $user->id === $build->app->user_id && $build->status === 'completed';
    }

    /**
     * Determine if the user can delete the build.
     */
    public function delete(User $user, AppBuild $build): bool
    {
        // User can delete if they own the app
        return $user->id === $build->app->user_id;
    }

    /**
     * Determine if the user can cancel the build.
     */
    public function cancel(User $user, AppBuild $build): bool
    {
        // User can cancel if they own the app and build is still in progress
        return $user->id === $build->app->user_id && $build->isBuilding();
    }
}
