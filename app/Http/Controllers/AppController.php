<?php

namespace App\Http\Controllers;

use App\Models\App;

class AppController extends Controller
{
    /**
     * Display the app editor/settings page.
     *
     * Automatically loads the platform-specific editor view
     * based on the app's platform plugin.
     */
    public function show(App $app)
    {
        // Ensure user owns this app
        if ($app->user_id !== auth()->id()) {
            abort(403);
        }

        // Get platform plugin instance
        $platform = $app->getPlatformInstance();

        if (! $platform) {
            abort(500, 'Platform plugin not found or not configured for this app.');
        }

        // Load platform-specific editor view
        return view($platform->getEditorView(), [
            'app' => $app,
            'platform' => $platform,
        ]);
    }
}
