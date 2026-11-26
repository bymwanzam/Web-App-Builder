<?php

namespace App\Services;

use App\Models\AppBuild;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppetizeService
{
    private const API_BASE = 'https://api.appetize.io/v1';

    /**
     * Get Appetize API token from system settings
     */
    private function getApiToken(): ?string
    {
        return SystemSetting::get('appetize_api_token');
    }

    /**
     * Check if Appetize integration is enabled
     */
    public function isEnabled(): bool
    {
        return SystemSetting::get('appetize_enabled', false)
            && ! empty($this->getApiToken());
    }

    /**
     * Upload build to Appetize.io using direct file upload
     *
     * @throws \Exception
     */
    public function uploadBuild(AppBuild $build): array
    {
        if (! $this->isEnabled()) {
            throw new \Exception('Appetize integration is not enabled');
        }

        // Get absolute path to build artifact on local filesystem
        $filePath = Storage::disk('builds')->path($build->artifact_url);

        if (! file_exists($filePath)) {
            throw new \Exception("Build artifact not found at: {$build->artifact_url}");
        }

        Log::info('Uploading build to Appetize via direct file upload', [
            'build_id' => $build->id,
            'file_path' => $build->artifact_url,
            'file_size' => filesize($filePath),
        ]);

        // Upload file directly using multipart/form-data
        // This works with local storage - no public URL needed!
        $response = Http::withHeaders([
            'X-API-KEY' => $this->getApiToken(),
        ])
            ->timeout(120) // Increased timeout for file upload
            ->attach(
                'file',                          // Field name required by Appetize
                file_get_contents($filePath),    // File contents
                basename($filePath)              // Filename
            )
            ->post(self::API_BASE.'/apps', [
                'platform' => $this->getPlatformName($build),
                'fileType' => $this->getFileType($build),
                'note' => "Build #{$build->id} - {$build->app->name} v{$build->version_name}",

                // Session configuration
                'timeout' => SystemSetting::get('appetize_session_timeout', 300),
                'timeLimit' => 600, // 10 minutes max session
                'maxConcurrent' => 10,

                // Security: restrict embedding to your domain
                'referrerHostnamesRestricted' => implode(',', array_filter([
                    parse_url(config('app.url'), PHP_URL_HOST),
                ])),

                // App permissions (flattened as per Appetize API docs)
                'appPermissions.run' => 'public',
                'appPermissions.networkProxy' => 'authenticated',
                'appPermissions.networkIntercept' => 'authenticated',
                'appPermissions.debugLog' => 'authenticated',
                'appPermissions.adbConnect' => 'authenticated',
                'appPermissions.androidPackageManager' => 'authenticated',

                // UI customization
                'buttonText' => 'Tap to preview your app',
                'postSessionButtonText' => 'Tap to restart',
                'useLastFrame' => true,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            // Update build record
            $build->update([
                'appetize_public_key' => $data['publicKey'],
                'appetize_preview_url' => $this->buildPreviewUrl($build, $data['publicKey']),
                'appetize_uploaded_at' => now(),
            ]);

            Log::info('Build uploaded to Appetize successfully', [
                'build_id' => $build->id,
                'public_key' => $data['publicKey'],
            ]);

            return $data;
        }

        throw new \Exception('Failed to upload to Appetize: '.$response->body());
    }

    /**
     * Delete app from Appetize.io
     */
    public function deleteApp(string $publicKey): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $response = Http::withHeaders([
            'X-API-KEY' => $this->getApiToken(),
        ])->delete(self::API_BASE."/apps/{$publicKey}");

        if ($response->successful()) {
            Log::info('Deleted Appetize app', ['public_key' => $publicKey]);

            return true;
        }

        Log::error('Failed to delete Appetize app', [
            'public_key' => $publicKey,
            'response' => $response->body(),
        ]);

        return false;
    }

    /**
     * Generate preview URL with platform-specific configuration
     */
    public function buildPreviewUrl(AppBuild $build, ?string $publicKey = null): string
    {
        $publicKey = $publicKey ?? $build->appetize_public_key;

        if (! $publicKey) {
            throw new \Exception('No Appetize public key available');
        }

        // Get platform-specific configuration
        $platform = $build->app->getPlatformInstance();
        $config = $platform->getAppetizeConfig($build);

        $params = array_merge([
            'buildId' => $publicKey,
            'scale' => 'auto',
            'autoPlay' => 'false',
            'screenOnly' => 'false',
            'grantPermissions' => 'true',
            'orientation' => 'portrait',
        ], $config);

        return 'https://appetize.io/embed/'.$publicKey.'?'.http_build_query($params);
    }

    /**
     * Get session configuration for JavaScript SDK
     */
    public function getSessionConfig(AppBuild $build): array
    {
        $platform = $build->app->getPlatformInstance();

        return array_merge([
            'device' => $this->getDefaultDevice($build),
            'scale' => 'auto',
            'orientation' => 'portrait',
            'grantPermissions' => true,
            'hidePasswords' => true,
            'codec' => 'h264',
            'audio' => false,
            'record' => true,
        ], $platform->getAppetizeConfig($build));
    }

    /**
     * Get Appetize platform identifier from build
     */
    private function getPlatformName(AppBuild $build): string
    {
        $platform = $build->app->getPlatformInstance();

        if (! $platform) {
            throw new \Exception("Platform plugin not found for build #{$build->id}");
        }

        if (! $platform->supportsAppetize()) {
            throw new \Exception("Platform {$platform->getName()} does not support Appetize");
        }

        return $platform->getAppetizePlatformId();
    }

    /**
     * Get Appetize file type from build
     */
    private function getFileType(AppBuild $build): string
    {
        $platform = $build->app->getPlatformInstance();

        if (! $platform) {
            throw new \Exception("Platform plugin not found for build #{$build->id}");
        }

        if (! $platform->supportsAppetize()) {
            throw new \Exception("Platform {$platform->getName()} does not support Appetize");
        }

        return $platform->getAppetizeFileType($build);
    }

    /**
     * Get default Appetize device from build
     */
    private function getDefaultDevice(AppBuild $build): string
    {
        $platform = $build->app->getPlatformInstance();

        if (! $platform) {
            throw new \Exception("Platform plugin not found for build #{$build->id}");
        }

        if (! $platform->supportsAppetize()) {
            throw new \Exception("Platform {$platform->getName()} does not support Appetize");
        }

        return $platform->getAppetizeDefaultDevice();
    }
}
