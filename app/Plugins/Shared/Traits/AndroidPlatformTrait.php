<?php

namespace App\Plugins\Shared\Traits;

use App\Models\App;

/**
 * AndroidPlatformTrait
 *
 * Shared trait for Android platform plugins containing common Android-specific logic:
 * - Package name validation and generation
 * - Android permissions management
 * - Build format support
 * - Default configuration
 * - Navigation transformation
 * - Firebase/Google Services integration
 *
 * This trait can be used by any Android-based platform plugin (WebView, WordPress, etc.)
 */
trait AndroidPlatformTrait
{
    /**
     * Get supported build formats for Android
     *
     * @return array<string> Array of supported build formats
     */
    public function getSupportedBuildFormats(): array
    {
        return ['apk', 'aab'];
    }

    /**
     * Get the builder type for this platform
     *
     * @return string The builder type identifier
     */
    public function getBuilderType(): string
    {
        return 'android';
    }

    /**
     * Get default Android platform configuration
     *
     * @return array<string, mixed> Default configuration values
     */
    public function getDefaultConfig(): array
    {
        return [
            'version_name' => '1.0.0',
            'min_sdk' => 24,
            'target_sdk' => 34,
            'build_tools_version' => '34.0.0',
            'permissions' => [
                'internet' => true, // Always enabled by default
                'location' => false,
                'camera' => false,
                'storage' => false,
                'record_audio' => false,
                'read_contacts' => false,
                'vibrate' => false,
            ],
        ];
    }

    /**
     * Get available Android permissions with metadata
     *
     * @return array<string, array<string, mixed>> Permission definitions
     */
    public function getAvailablePermissions(): array
    {
        return [
            'internet' => [
                'label' => 'Internet Access',
                'description' => 'Required for loading web content (always enabled)',
                'required' => true,
            ],
            'location' => [
                'label' => 'Location Access',
                'description' => 'Access device GPS location',
                'required' => false,
            ],
            'camera' => [
                'label' => 'Camera Access',
                'description' => 'Access device camera for photos and videos',
                'required' => false,
            ],
            'storage' => [
                'label' => 'Storage Access',
                'description' => 'Read and write to device storage',
                'required' => false,
            ],
            'record_audio' => [
                'label' => 'Record Audio',
                'description' => 'Record audio using device microphone',
                'required' => false,
            ],
            'read_contacts' => [
                'label' => 'Read Contacts',
                'description' => 'Access device contacts',
                'required' => false,
            ],
            'vibrate' => [
                'label' => 'Vibrate',
                'description' => 'Vibrate the device',
                'required' => false,
            ],
        ];
    }

    /**
     * Generate a valid Android package ID from an app name
     *
     * Converts the app name to a valid package identifier following Android conventions:
     * - Lowercase only
     * - Removes non-alphanumeric characters
     * - Ensures it starts with a letter
     * - Supports retry attempts with numeric suffix
     *
     * @param  string  $appName  The application name
     * @param  int  $attempt  Attempt number (for generating unique package IDs)
     * @return string Generated package ID in format "com.{slug}.app"
     */
    public function generatePackageId(string $appName, int $attempt = 1): string
    {
        // Convert to lowercase and remove all non-alphanumeric
        $slug = strtolower($appName);
        $slug = preg_replace('/[^a-z0-9]/', '', $slug);

        // Ensure it starts with a letter
        if (! preg_match('/^[a-z]/', $slug)) {
            $slug = 'app'.$slug;
        }

        // Generate package name
        $packageId = "com.{$slug}.app";

        // Add attempt number if > 1
        if ($attempt > 1) {
            $packageId = "com.{$slug}{$attempt}.app";
        }

        return $packageId;
    }

    /**
     * Validate an Android package ID
     *
     * Android package naming rules:
     * 1. Must have at least 2 segments separated by dots
     * 2. Each segment must start with a letter
     * 3. Each segment can only contain letters, numbers, and underscores
     * 4. Cannot use Java reserved keywords
     *
     * @param  string  $packageId  The package identifier to validate
     * @return bool True if valid, false otherwise
     */
    public function validatePackageId(string $packageId): bool
    {
        $segments = explode('.', $packageId);

        // Must have at least 2 segments
        if (count($segments) < 2) {
            return false;
        }

        // Java reserved keywords (simplified list of common ones)
        $reservedKeywords = [
            'abstract', 'assert', 'boolean', 'break', 'byte', 'case', 'catch', 'char',
            'class', 'const', 'continue', 'default', 'do', 'double', 'else', 'enum',
            'extends', 'final', 'finally', 'float', 'for', 'goto', 'if', 'implements',
            'import', 'instanceof', 'int', 'interface', 'long', 'native', 'new', 'package',
            'private', 'protected', 'public', 'return', 'short', 'static', 'strictfp',
            'super', 'switch', 'synchronized', 'this', 'throw', 'throws', 'transient',
            'try', 'void', 'volatile', 'while',
        ];

        foreach ($segments as $segment) {
            // Must not be empty
            if (empty($segment)) {
                return false;
            }

            // Must start with a letter
            if (! preg_match('/^[a-z]/i', $segment)) {
                return false;
            }

            // Can only contain letters, numbers, and underscores
            if (! preg_match('/^[a-z][a-z0-9_]*$/i', $segment)) {
                return false;
            }

            // Must not be a reserved keyword
            if (in_array(strtolower($segment), $reservedKeywords)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Transform navigation items for builder compatibility
     *
     * Renames 'value' field to 'url' as expected by Android builder
     *
     * @param  array<int, array<string, mixed>>  $items  Navigation items to transform
     * @return array<int, array<string, mixed>> Transformed navigation items
     */
    protected function transformNavigationItems(array $items): array
    {
        return array_map(function ($item) {
            // Rename 'value' to 'url' for builder compatibility
            if (isset($item['value'])) {
                $item['url'] = $item['value'];
                unset($item['value']);
            }

            return $item;
        }, $items);
    }

    /**
     * Get the content of google-services.json for an app
     *
     * Retrieves Firebase configuration from the app's push notification config
     *
     * @param  App  $app  The application model
     * @return string JSON content of google-services.json or empty string if not configured
     */
    protected function getGoogleServicesContent(App $app): string
    {
        $pushConfig = $app->pushNotificationConfig;

        if (! $pushConfig || ! $pushConfig->enabled || ! $pushConfig->google_services_file) {
            return '';
        }

        $path = \Illuminate\Support\Facades\Storage::disk('private')->path($pushConfig->google_services_file);

        if (! file_exists($path)) {
            return '';
        }

        return file_get_contents($path);
    }
}
