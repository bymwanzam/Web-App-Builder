<?php

namespace App\DataTransferObjects;

use App\Models\App;
use App\Models\AppBuild;

/**
 * Build Configuration Context
 *
 * Data transfer object that encapsulates all context needed by platform plugins
 * to generate build server configuration.
 */
class BuildConfigContext
{
    /**
     * Create a new build config context instance.
     *
     * @param  App  $app  The application being built
     * @param  AppBuild  $build  The specific build being created
     */
    public function __construct(
        public readonly App $app,
        public readonly AppBuild $build,
    ) {}
}
