<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppBuilder extends Model
{
    use HasFactory;

    protected $table = 'app_builders';

    protected $fillable = [
        'name',
        'url',
        'port',
        'server_key',
        'max_queue',
        'platforms',
        'status',
        'credit_cost',
        'current_builds_count',
        'total_builds_processed',
        'last_triggered_at',
    ];

    protected $casts = [
        'last_triggered_at' => 'datetime',
        'platforms' => 'array',
    ];

    /**
     * Get all builds assigned to this server.
     */
    public function builds()
    {
        return $this->hasMany(AppBuild::class);
    }

    /**
     * Scope to get only active servers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get servers that support a specific platform.
     */
    public function scopeForPlatform($query, $platform)
    {
        return $query->whereJsonContains('platforms', $platform);
    }

    /**
     * Scope to get servers that support any Android platform.
     * Dynamically discovers all platforms starting with "android-".
     * Used for keystore generation which works for all Android platforms.
     */
    public function scopeForAndroidPlatforms($query)
    {
        // Get all active Android platform IDs from PluginManager
        $pluginManager = app(\App\Services\PluginManager::class);
        $androidPlatformIds = $pluginManager->getActivePlatforms()
            ->map(fn ($p) => $p->getPlatformId())
            ->filter(fn ($id) => str_starts_with($id, 'android-'))
            ->values()
            ->toArray();

        if (empty($androidPlatformIds)) {
            // No Android platforms active, return empty result
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($q) use ($androidPlatformIds) {
            foreach ($androidPlatformIds as $platformId) {
                $q->orWhereJsonContains('platforms', $platformId);
            }
        });
    }

    /**
     * Check if this builder supports a given platform.
     */
    public function supportsPlatform(string $platform): bool
    {
        return in_array($platform, $this->platforms ?? []);
    }

    /**
     * Select the optimal build server for a given platform.
     * Queries each active server's queue and picks the least busy one.
     *
     * @param  string  $platform  Platform to build for (android, ios, both)
     * @param  array|null  $allowedServerIds  Optional array of allowed server IDs to filter by
     * @return AppBuilder|null
     */
    public static function selectOptimalServer($platform, $allowedServerIds = null)
    {
        $query = static::active()->forPlatform($platform);

        // Filter by allowed servers if specified
        if ($allowedServerIds !== null) {
            $query->whereIn('id', $allowedServerIds);
        }

        $servers = $query->get();

        if ($servers->isEmpty()) {
            return null;
        }

        $optimalServer = null;
        $lowestQueueCount = PHP_INT_MAX;

        foreach ($servers as $server) {
            // Skip if server is at max capacity based on DB count
            if ($server->current_builds_count >= $server->max_queue) {
                continue;
            }

            // Get real-time queue count from server
            $queueCount = $server->getQueueCount();

            if ($queueCount !== null && $queueCount < $lowestQueueCount) {
                $lowestQueueCount = $queueCount;
                $optimalServer = $server;
            }
        }

        return $optimalServer;
    }

    /**
     * Get the current queue count from the build server.
     * Returns the total of pending + processing builds.
     */
    public function getQueueCount()
    {
        try {
            $response = Http::timeout(5)->get("{$this->url}:{$this->port}/queue/{$this->server_key}");

            if ($response->successful()) {
                $data = $response->json('data', []);
                $pending = $data['count'] ?? 0;
                $processing = $data['processing'] ?? 0;

                return $pending + $processing;
            }
        } catch (\Exception $e) {
            Log::error("Failed to get queue count from build server {$this->name}: {$e->getMessage()}");
        }

        // Fallback to DB count if API fails
        return $this->current_builds_count;
    }

    /**
     * Trigger the build server to fetch pending builds.
     */
    public function triggerBuildFetch()
    {
        try {
            $response = Http::timeout(10)->post("{$this->url}:{$this->port}/trigger/{$this->server_key}");

            if ($response->successful()) {
                $this->update(['last_triggered_at' => now()]);
                Log::info("Triggered build fetch for server {$this->name}");

                return true;
            }

            Log::warning("Failed to trigger build server {$this->name}: HTTP {$response->status()}");

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to trigger build server {$this->name}: {$e->getMessage()}");

            return false;
        }
    }

    /**
     * Increment the current builds count.
     */
    public function incrementBuildCount()
    {
        $this->increment('current_builds_count');
    }

    /**
     * Decrement the current builds count.
     */
    public function decrementBuildCount()
    {
        $this->decrement('current_builds_count');
        $this->increment('total_builds_processed');
    }

    /**
     * Get the full server URL.
     */
    public function getFullUrlAttribute()
    {
        return "{$this->url}:{$this->port}";
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the display name for a specific platform.
     */
    public function getPlatformDisplayName(string $platformId): string
    {
        try {
            $pluginManager = app(\App\Services\PluginManager::class);
            $platform = $pluginManager->getPlatformBySlug($platformId);

            return $platform ? $platform->getName() : ucfirst($platformId);
        } catch (\Exception $e) {
            return ucfirst($platformId);
        }
    }

    /**
     * Get list of platform display names.
     */
    public function getPlatformsListAttribute(): array
    {
        $platforms = $this->platforms ?? [];

        return array_map(fn ($p) => $this->getPlatformDisplayName($p), $platforms);
    }

    /**
     * Get comma-separated platform names for display.
     */
    public function getPlatformNameAttribute(): string
    {
        return implode(', ', $this->platforms_list);
    }
}
