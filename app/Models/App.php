<?php

namespace App\Models;

use App\Contracts\PlatformPlugin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    // MINIMAL fillable fields (truly universal across ALL platform types)
    protected $fillable = [
        'user_id',
        'platform_plugin_id',
        'name',
        'version_name',
        'version_code',
    ];

    // No casts needed (simple fields only)

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the platform plugin for this app.
     */
    public function platformPlugin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Plugin::class, 'platform_plugin_id');
    }

    /**
     * Get the builds for this app.
     */
    public function builds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppBuild::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the Android keystore (from platform_config).
     *
     * @deprecated Use getPlatformConfig('keystore_id') instead
     */
    public function androidKeystore()
    {
        $keystoreId = $this->getPlatformConfig('keystore_id');

        if ($keystoreId) {
            return Keystore::find($keystoreId);
        }

        return null;
    }

    /**
     * Get the notification config for this app.
     */
    public function pushNotificationConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PushNotificationConfig::class);
    }

    /**
     * Get the push notifications sent for this app.
     */
    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    /**
     * Get the hosted pages for this app.
     * Uses the shared app_pages table which works with all platform plugins.
     */
    public function hostedPages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Plugins\Shared\Models\AppPage::class);
    }

    /**
     * DEPRECATED: Use getConfig() instead.
     *
     * Get the Android WebView configuration for this app.
     * This method is kept for backward compatibility only.
     *
     * @deprecated Use getConfig() instead
     */
    public function androidWebViewConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AndroidWebViewConfig::class);
    }

    /**
     * DEPRECATED: Use getConfig() instead.
     *
     * Get the Android WordPress configuration for this app.
     * This method is kept for backward compatibility only.
     *
     * @deprecated Use getConfig() instead
     */
    public function androidWordPressConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AndroidWordPressConfig::class);
    }

    /**
     * Get platform-specific configuration model.
     * Dynamically loads configuration based on platform plugin.
     *
     * @return mixed|null
     */
    public function getConfig()
    {
        if (! $this->platformPlugin) {
            return null;
        }

        $platform = $this->getPlatformInstance();
        if (! $platform) {
            return null;
        }

        $configModelClass = $platform->getConfigModelClass();

        return $configModelClass::where('app_id', $this->id)->first();
    }

    /**
     * DEPRECATED: Use getConfig() instead.
     *
     * Get platform configuration value(s).
     *
     * @param  string|null  $key  Configuration key (dot notation supported)
     * @param  mixed  $default  Default value if key not found
     * @return mixed
     *
     * @deprecated v2.0.0 Use getConfig() instead
     */
    public function getPlatformConfig(?string $key = null, $default = null)
    {
        trigger_error('App::getPlatformConfig() is deprecated. Use App::getConfig() instead.', E_USER_DEPRECATED);

        $config = $this->getConfig();
        if ($key === null) {
            return $config ? $config->toArray() : [];
        }

        return data_get($config, $key, $default);
    }

    /**
     * DEPRECATED: Use direct model access instead.
     *
     * Set platform configuration value.
     *
     * @param  string  $key  Configuration key (dot notation supported)
     * @param  mixed  $value  Value to set
     *
     * @deprecated v2.0.0 Use direct model access instead
     */
    public function setPlatformConfig(string $key, $value): void
    {
        trigger_error('App::setPlatformConfig() is deprecated. Use direct model access instead.', E_USER_DEPRECATED);

        $config = $this->getConfig();
        if ($config) {
            $config->$key = $value;
            $config->save();
        }
    }

    /**
     * Get the platform plugin instance.
     */
    public function getPlatformInstance(): ?PlatformPlugin
    {
        if (! $this->platformPlugin) {
            return null;
        }

        return app(\App\Services\PluginManager::class)
            ->getPlatformById($this->platform_plugin_id);
    }

    /**
     * Get the latest build for a specific platform.
     */
    public function latestBuild($platform = null)
    {
        $query = $this->builds();

        if ($platform) {
            $query->where('platform', $platform);
        }

        return $query->first();
    }

    /**
     * Get the latest completed build for a specific platform.
     */
    public function latestCompletedBuild($platform = null)
    {
        $query = $this->builds()->where('status', 'completed');

        if ($platform) {
            $query->where('platform', $platform);
        }

        return $query->first();
    }

    /**
     * Check if app has any active builds.
     */
    public function hasActiveBuilds()
    {
        return $this->builds()->building()->exists();
    }

    /**
     * Increment version code for next build.
     */
    public function incrementVersionCode()
    {
        $this->increment('version_code');

        return $this->version_code;
    }

    /**
     * Get the version name for a specific platform.
     */
    public function getVersionName(string $platform): string
    {
        // Try platform-specific version first, then fallback to universal version_name
        // Note: Currently not used by platform configs, but kept for future platform support
        return $this->version_name ?? '1.0.0';
    }

    /**
     * Increment version name for a specific platform.
     * Increments the patch version (e.g., "1.0.5" -> "1.0.6").
     */
    public function incrementVersionName(string $platform): string
    {
        $currentVersion = $this->getVersionName($platform);

        // Parse version components
        if (preg_match('/^(\d+)\.(\d+)(\.(\d+))?$/', $currentVersion, $matches)) {
            $major = (int) $matches[1];
            $minor = (int) $matches[2];
            $patch = isset($matches[4]) ? (int) $matches[4] : 0;

            // Increment patch version
            $patch++;

            // Return new version string
            return "{$major}.{$minor}.{$patch}";
        }

        // Fallback: if current version doesn't match expected format,
        // default to 1.0.1
        return '1.0.1';
    }

    /**
     * Check if app is ready to build.
     * Apps are ready to build if they don't have active builds in progress.
     */
    public function isReadyToBuild()
    {
        return ! $this->hasActiveBuilds();
    }

    /**
     * Get the build status for display.
     */
    public function getBuildStatusAttribute()
    {
        $latestBuild = $this->latestBuild();

        if (! $latestBuild) {
            return null;
        }

        return $latestBuild->status;
    }
}
