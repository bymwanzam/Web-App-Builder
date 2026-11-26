<?php

namespace App\Services;

use App\Models\AppBuild;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate QR code for app installation.
     *
     * @return string|null Path to QR code image
     */
    public function generateInstallQR(AppBuild $build): ?string
    {
        try {
            // Check if imagick extension is available
            if (! $this->isImagickAvailable()) {
                \Log::warning('QR Code generation skipped - imagick extension not installed', [
                    'build_id' => $build->id,
                    'install_command' => 'pecl install imagick',
                ]);

                return null;
            }

            // Determine URL based on platform
            $url = $this->getInstallUrl($build);

            if (! $url) {
                return null;
            }

            // Generate QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate($url);

            // Define storage path
            $directory = "builds/{$build->app_id}/{$build->id}";
            $filename = "qr-{$build->platform}.png";
            $path = "{$directory}/{$filename}";

            // Ensure directory exists
            Storage::disk('builds')->makeDirectory($directory);

            // Save QR code
            Storage::disk('builds')->put($path, $qrCode);

            return $path;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed', [
                'build_id' => $build->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Check if imagick PHP extension is available.
     */
    protected function isImagickAvailable(): bool
    {
        return extension_loaded('imagick');
    }

    /**
     * Get installation URL for the build.
     */
    protected function getInstallUrl(AppBuild $build): ?string
    {
        // Generate signed URL valid for 30 days (matching build expiration)
        $expiresAt = $build->expires_at ?? now()->addDays(30);

        return URL::temporarySignedRoute(
            'builds.download.public',
            $expiresAt,
            ['build' => $build->id]
        );
    }

    /**
     * Delete QR code for a build.
     */
    public function deleteQR(AppBuild $build): bool
    {
        if (! $build->install_qr_code) {
            return true;
        }

        try {
            Storage::disk('builds')->delete($build->install_qr_code);

            return true;
        } catch (\Exception $e) {
            \Log::error('QR Code deletion failed', [
                'build_id' => $build->id,
                'path' => $build->install_qr_code,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
