<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppBuild extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'app_builder_id',
        'platform',
        'build_type',
        'build_format',
        'version_name',
        'version_code',
        'status',
        'keystore_id',
        'artifact_url',
        'artifact_size',
        'install_qr_code',
        'appetize_public_key',
        'appetize_preview_url',
        'appetize_uploaded_at',
        'build_logs',
        'error_message',
        'build_duration',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'appetize_uploaded_at' => 'datetime',
    ];

    /**
     * Get the app that owns this build.
     */
    public function app()
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Get the builder assigned to this build.
     */
    public function appBuilder()
    {
        return $this->belongsTo(AppBuilder::class);
    }

    /**
     * Get the keystore used for this build (Android only).
     */
    public function keystore()
    {
        return $this->belongsTo(Keystore::class);
    }

    /**
     * Scope to filter builds by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get pending builds.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get completed builds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed builds.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get building status (any in-progress status).
     */
    public function scopeBuilding($query)
    {
        return $query->whereIn('status', ['preparing', 'building_android', 'processing']);
    }

    /**
     * Check if build is in progress.
     */
    public function isBuilding()
    {
        return in_array($this->status, ['preparing', 'building_android', 'processing']);
    }

    /**
     * Check if build is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if build has failed.
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Generate a download URL for the artifact.
     */
    public function getDownloadUrlAttribute()
    {
        if (! $this->artifact_url) {
            return null;
        }

        // Generate signed URL valid for 30 days (matching build expiration)
        $expiresAt = $this->expires_at ?? now()->addDays(30);

        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'builds.download.public',
            $expiresAt,
            ['build' => $this->id]
        );
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute()
    {
        if (! $this->artifact_size) {
            return null;
        }

        $bytes = $this->artifact_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf('%.2f', $bytes / pow(1024, $factor)).' '.$units[$factor];
    }

    /**
     * Get the platform icon name.
     */
    public function getPlatformIconAttribute()
    {
        return match ($this->platform) {
            'android' => 'android',
            default => 'mobile',
        };
    }

    /**
     * Get human-readable build duration.
     */
    public function getFormattedDurationAttribute()
    {
        if (! $this->build_duration) {
            return null;
        }

        $minutes = floor($this->build_duration / 60);
        $seconds = $this->build_duration % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'preparing', 'building_android', 'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }

    /**
     * Get status display text.
     */
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'preparing' => 'Preparing',
            'building_android' => 'Building Android',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the file extension based on platform and format.
     */
    public function getFileExtension(): string
    {
        // Check platform prefix instead of exact match to handle
        // android-webview, android-native, etc.
        if (str_starts_with($this->platform, 'android')) {
            return $this->build_format ?? 'apk';
        }

        if (str_starts_with($this->platform, 'ios')) {
            return 'ipa';
        }

        // Default for unknown platforms
        return 'zip';
    }

    /**
     * Check if build has Appetize preview available.
     */
    public function hasAppetizePreview(): bool
    {
        return ! empty($this->appetize_public_key);
    }

    /**
     * Get Appetize embed URL.
     */
    public function getAppetizeEmbedUrl(): ?string
    {
        if (! $this->appetize_public_key) {
            return null;
        }

        return "https://appetize.io/embed/{$this->appetize_public_key}";
    }

    /**
     * Check if build is eligible for Appetize upload.
     */
    public function isEligibleForAppetize(): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        try {
            // Delegate to platform plugin to check Appetize support
            $platform = $this->app->getPlatformInstance();

            // Check if platform supports Appetize
            if (! $platform->supportsAppetize()) {
                return false;
            }

            // Check if build format is supported by platform
            $supportedFormats = $platform->getSupportedBuildFormats();

            return in_array($this->build_format, $supportedFormats);
        } catch (\Exception $e) {
            // If platform cannot be loaded or doesn't support Appetize, return false
            return false;
        }
    }
}
