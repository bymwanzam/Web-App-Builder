<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class PluginIconController extends Controller
{
    /**
     * Serve plugin icon file.
     *
     * @param  string  $slug  Plugin slug (e.g., 'stripe', 'paypal', 'android')
     * @param  string  $filename  Icon filename (e.g., 'icon.jpg')
     */
    public function show(string $slug, string $filename): Response
    {
        // Security: Only allow icon files
        if (! in_array($filename, ['icon.jpg', 'icon.jpeg', 'icon.png', 'icon.svg'])) {
            abort(404);
        }

        // Find platform plugin directory with case-insensitive match
        $platformDir = $this->findPluginDirectory(app_path('Plugins/Platforms'), $slug);

        // Build paths to check
        $pathsToCheck = [];

        if ($platformDir) {
            $pathsToCheck[] = app_path("Plugins/Platforms/{$platformDir}/resources/images/{$filename}");
            $pathsToCheck[] = app_path("Plugins/Platforms/{$platformDir}/resources/{$filename}");
        }

        // Payment gateway plugins (use slug directly as they're lowercase)
        $pathsToCheck[] = app_path("Plugins/PaymentGateways/{$slug}/resources/images/{$filename}");
        $pathsToCheck[] = app_path("Plugins/PaymentGateways/{$slug}/resources/{$filename}");

        // Find first existing path
        $pluginPath = null;
        foreach ($pathsToCheck as $path) {
            if (File::exists($path)) {
                $pluginPath = $path;
                break;
            }
        }

        // If no path found, return 404
        if ($pluginPath === null) {
            abort(404);
        }

        // Get file contents and mime type
        $contents = File::get($pluginPath);
        $mimeType = File::mimeType($pluginPath);

        // Return file with proper headers
        return response($contents, 200)
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'public, max-age=31536000'); // Cache for 1 year
    }

    /**
     * Find plugin directory with case-insensitive slug matching.
     *
     * @param  string  $basePath  Base path to search (e.g., app/Plugins/Platforms)
     * @param  string  $slug  Plugin slug (e.g., 'android-webview')
     * @return string|null Actual directory name or null if not found
     */
    private function findPluginDirectory(string $basePath, string $slug): ?string
    {
        if (! is_dir($basePath)) {
            return null;
        }

        // Normalize slug: remove hyphens/underscores and lowercase
        $normalizedSlug = strtolower(str_replace(['-', '_'], '', $slug));

        // Scan directory for matching folder
        $directories = glob($basePath.'/*', GLOB_ONLYDIR);

        foreach ($directories as $dir) {
            $dirName = basename($dir);
            // Compare normalized versions
            if (strtolower($dirName) === $normalizedSlug) {
                return $dirName;
            }
        }

        return null;
    }
}
