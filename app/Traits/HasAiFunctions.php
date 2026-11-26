<?php

namespace App\Traits;

use App\Models\App;

trait HasAiFunctions
{
    /**
     * Execute an AI function call for this platform.
     *
     * @param  string  $name  Function name from ai_functions
     * @param  array  $args  Arguments passed by AI
     * @param  App  $app  The app being configured
     * @return array Result to return to AI
     */
    abstract public function executeAiFunction(string $name, array $args, App $app): array;

    /**
     * Get AI context for this app's current state.
     * Called to build the system prompt with current config values.
     */
    abstract public function getAiAppContext(App $app): string;
}
