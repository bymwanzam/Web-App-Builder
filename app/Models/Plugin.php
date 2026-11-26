<?php

namespace App\Models;

use App\Contracts\Plugin as PluginContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'class',
        'version',
        'status',
        'config',
        'migrations',
        'metadata',
        'installed_at',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'migrations' => 'array',
            'metadata' => 'array',
            'installed_at' => 'datetime',
        ];
    }

    /**
     * Scope query to only active plugins.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope query to plugins of a specific type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get an instance of the plugin with its configuration.
     */
    public function getInstance(): PluginContract
    {
        $class = $this->class;

        if (! class_exists($class)) {
            throw new \Exception("Plugin class {$class} not found");
        }

        return new $class($this->config);
    }

    /**
     * Check if plugin is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Activate the plugin.
     */
    public function activate(): void
    {
        // Check if plugin is configured before activating
        $instance = $this->getInstance();

        if (! $instance->isConfigured()) {
            throw new \Exception('Plugin must be configured before activation.');
        }

        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the plugin.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Get plugin metadata.
     */
    public function getMetadata(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->metadata ?? [];
        }

        return data_get($this->metadata, $key, $default);
    }

    /**
     * Get list of migration files installed by this plugin.
     */
    public function getMigrations(): array
    {
        return $this->migrations ?? [];
    }

    /**
     * Check if plugin has any installed migrations.
     */
    public function hasMigrations(): bool
    {
        return ! empty($this->migrations);
    }
}
