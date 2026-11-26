<?php

namespace App\Services;

use App\Contracts\PaymentGatewayPlugin;
use App\Contracts\PlatformPlugin;
use App\Contracts\Plugin as PluginContract;
use App\Models\Plugin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Livewire\Livewire;

class PluginManager
{
    /**
     * Discover all available plugins from the filesystem.
     */
    public function discover(): Collection
    {
        $plugins = collect();

        $pluginTypes = [
            'PaymentGateways' => 'payment_gateway',
            'Platforms' => 'platform',
        ];

        foreach ($pluginTypes as $directory => $type) {
            $namespace = "App\\Plugins\\{$directory}";
            $path = app_path("Plugins/{$directory}");

            if (! File::exists($path)) {
                continue;
            }

            $files = File::files($path);

            foreach ($files as $file) {
                $className = $namespace.'\\'.$file->getFilenameWithoutExtension();

                if (class_exists($className)) {
                    $implements = class_implements($className);

                    if (in_array(PluginContract::class, $implements)) {
                        $instance = new $className;

                        $plugins->push([
                            'name' => $instance->getName(),
                            'description' => $instance->getDescription(),
                            'type' => $instance->getType(),
                            'icon' => $instance->getIcon(),
                            'version' => $instance->getVersion(),
                            'author' => $instance->getAuthor(),
                            'class' => $className,
                            'instance' => $instance,
                        ]);
                    }
                }
            }
        }

        return $plugins;
    }

    /**
     * Register plugin views namespace.
     * This allows plugins to have custom Blade views accessed via "plugins.{slug}::view-name".
     */
    protected function registerPluginViews(Plugin $plugin): void
    {
        $typeDirectory = match ($plugin->type) {
            'payment_gateway' => 'PaymentGateways',
            'platform' => 'Platforms',
            'bridge' => 'Bridges',
            'notification' => 'Notifications',
            default => 'Other',
        };

        // Try to determine the correct plugin directory name
        // Strategy 1: Try namespace extraction (for legacy plugins like AndroidWebView)
        $pluginDirectory = null;
        $metadata = $plugin->metadata;

        if (isset($metadata['namespace'])) {
            $namespaceParts = explode('\\', $metadata['namespace']);
            $pluginDirectory = end($namespaceParts);

            $viewsPath = app_path("Plugins/{$typeDirectory}/{$pluginDirectory}/resources/views");

            // If path doesn't exist with namespace, try slug instead
            if (! File::exists($viewsPath)) {
                $pluginDirectory = null;
            }
        }

        // Strategy 2: Use slug (for plugins installed via installFromZip - line 261)
        if ($pluginDirectory === null) {
            $pluginDirectory = $plugin->slug;
        }

        $viewsPath = app_path("Plugins/{$typeDirectory}/{$pluginDirectory}/resources/views");

        if (File::exists($viewsPath)) {
            View::addNamespace("plugins.{$plugin->slug}", $viewsPath);
        }
    }

    /**
     * Register plugin migrations with Laravel's migrator.
     * This allows plugins to have their own migrations without copying to main folder.
     */
    protected function registerPluginMigrations(Plugin $plugin): void
    {
        $typeDirectory = match ($plugin->type) {
            'payment_gateway' => 'PaymentGateways',
            'platform' => 'Platforms',
            'bridge' => 'Bridges',
            'notification' => 'Notifications',
            default => 'Other',
        };

        // Try to determine the correct plugin directory name
        $pluginDirectory = null;
        $metadata = $plugin->metadata;

        if (isset($metadata['namespace'])) {
            $namespaceParts = explode('\\', $metadata['namespace']);
            $pluginDirectory = end($namespaceParts);

            $migrationsPath = app_path("Plugins/{$typeDirectory}/{$pluginDirectory}/database/migrations");

            // If path doesn't exist with namespace, try slug instead
            if (! File::exists($migrationsPath)) {
                $pluginDirectory = null;
            }
        }

        // Fallback to slug
        if ($pluginDirectory === null) {
            $pluginDirectory = $plugin->slug;
        }

        $migrationsPath = app_path("Plugins/{$typeDirectory}/{$pluginDirectory}/database/migrations");

        if (File::exists($migrationsPath)) {
            app('migrator')->path($migrationsPath);
        }
    }

    /**
     * Register plugin Livewire components with Livewire.
     * This allows plugins to provide custom Livewire components accessed via "plugins.{slug}.component-name".
     */
    protected function registerLivewireComponents(Plugin $plugin): void
    {
        // Get plugin metadata
        $metadata = $plugin->metadata;

        // Check if plugin has Livewire components
        if (! isset($metadata['livewire_components']) || empty($metadata['livewire_components'])) {
            return;
        }

        // Use the namespace from plugin metadata as base namespace
        // This ensures correct capitalization (e.g., "AndroidWebView" not "AndroidWebview")
        $baseNamespace = $metadata['namespace'] ?? $plugin->class;

        // Register each component
        foreach ($metadata['livewire_components'] as $componentPath) {
            // Extract class name from path (e.g., "Livewire/AppGeneralSettings.php" → "AppGeneralSettings")
            $className = basename($componentPath, '.php');

            // Build full class name
            $fullClassName = "{$baseNamespace}\\Livewire\\{$className}";

            // Verify class exists
            if (! class_exists($fullClassName)) {
                \Log::warning("Plugin '{$plugin->name}' references non-existent Livewire component: {$fullClassName}");

                continue;
            }

            // Generate Livewire alias (e.g., "plugins.android-webview.app-general-settings")
            $alias = 'plugins.'.$plugin->slug.'.'.Str::kebab($className);

            // Register with Livewire
            Livewire::component($alias, $fullClassName);
        }
    }

    /**
     * Register all plugin migrations by scanning the filesystem.
     * This method does NOT require database access and should be called
     * in AppServiceProvider::register() to ensure migrations are available
     * during `php artisan migrate:fresh`.
     */
    public function registerMigrationsFromFilesystem(): void
    {
        $pluginTypes = [
            'PaymentGateways',
            'Platforms',
            'Bridges',
            'Notifications',
        ];

        foreach ($pluginTypes as $typeDirectory) {
            $basePath = app_path("Plugins/{$typeDirectory}");

            if (! File::exists($basePath)) {
                continue;
            }

            // Scan all plugin directories within this type
            $pluginDirs = File::directories($basePath);

            foreach ($pluginDirs as $pluginDir) {
                $migrationsPath = $pluginDir.'/database/migrations';

                if (File::exists($migrationsPath)) {
                    app('migrator')->path($migrationsPath);
                }
            }
        }
    }

    /**
     * Boot all installed plugins (register views, routes, etc.).
     * Should be called from AppServiceProvider.
     */
    public function boot(): void
    {
        try {
            // Check if plugins table exists (important for migrations and tests)
            if (! Schema::hasTable('plugins')) {
                return;
            }

            $plugins = Plugin::all();
        } catch (\Exception $e) {
            // Database not accessible yet (during installation or migration)
            return;
        }

        foreach ($plugins as $plugin) {
            // Register plugin's custom view namespace
            $this->registerPluginViews($plugin);

            // Register plugin's Livewire components
            $this->registerLivewireComponents($plugin);
        }
    }

    /**
     * Install a plugin.
     */
    public function install(string $class): Plugin
    {
        if (! class_exists($class)) {
            throw new \Exception("Plugin class {$class} not found");
        }

        $instance = new $class;

        return Plugin::create([
            'name' => $instance->getName(),
            'slug' => Str::slug($instance->getName()),
            'type' => $instance->getType(),
            'class' => $class,
            'version' => $instance->getVersion(),
            'status' => 'installed',
            'installed_at' => now(),
        ]);
    }

    /**
     * Install a plugin from a ZIP file upload.
     */
    public function installFromZip($zipFile): Plugin
    {
        $zip = new \ZipArchive;
        $tempDir = storage_path('app/temp_plugin_'.uniqid());

        try {
            // 1. Extract ZIP
            if ($zip->open($zipFile->getRealPath()) !== true) {
                throw new \Exception('Failed to open ZIP file');
            }

            File::makeDirectory($tempDir, 0755, true);
            $zip->extractTo($tempDir);
            $zip->close();

            // 2. Find and validate plugin.json
            $pluginJsonPath = $tempDir.'/plugin.json';
            if (! File::exists($pluginJsonPath)) {
                throw new \Exception('plugin.json not found in ZIP file');
            }

            $metadata = json_decode(File::get($pluginJsonPath), true);
            if (! $metadata) {
                throw new \Exception('Invalid plugin.json format');
            }

            // Validate required metadata fields
            $requiredFields = ['name', 'slug', 'type', 'main_class', 'namespace', 'version'];
            foreach ($requiredFields as $field) {
                if (empty($metadata[$field])) {
                    throw new \Exception("Required field '{$field}' missing in plugin.json");
                }
            }

            // 3. Validate requirements
            if (isset($metadata['requirements'])) {
                $this->validateRequirements($metadata['requirements']);
            }

            // 4. Validate main class exists
            $mainClassPath = $tempDir.'/'.$metadata['main_class'];
            if (! File::exists($mainClassPath)) {
                throw new \Exception("Main class file not found: {$metadata['main_class']}");
            }

            // 5. Load and validate plugin class
            require_once $mainClassPath;
            $className = $metadata['namespace'];

            if (! class_exists($className)) {
                throw new \Exception("Plugin class {$className} not found");
            }

            $instance = new $className;

            // Validate it implements the correct interface
            if (! $instance instanceof PluginContract) {
                throw new \Exception('Plugin must implement Plugin interface');
            }

            // 6. Check for duplicates
            if ($this->isInstalled($metadata['slug'])) {
                throw new \Exception("Plugin '{$metadata['name']}' is already installed");
            }

            // 7. Determine destination directory based on plugin type
            $typeDirectory = match ($metadata['type']) {
                'payment_gateway' => 'PaymentGateways',
                'platform' => 'Platforms',
                'bridge' => 'Bridges',
                'notification' => 'Notifications',
                default => 'Other',
            };

            $destinationDir = app_path("Plugins/{$typeDirectory}/{$metadata['slug']}");

            // 8. Copy entire plugin directory to destination
            if (File::exists($destinationDir)) {
                File::deleteDirectory($destinationDir);
            }

            File::makeDirectory($destinationDir, 0755, true);
            File::copyDirectory($tempDir, $destinationDir);

            // 9. Process migrations
            $migratedFiles = [];
            if (! empty($metadata['migrations'])) {
                $migratedFiles = $this->runPluginMigrations($destinationDir, $metadata['migrations']);
            }

            // 10. Create database record
            $plugin = Plugin::create([
                'name' => $metadata['name'],
                'slug' => $metadata['slug'],
                'type' => $metadata['type'],
                'class' => $className,
                'version' => $metadata['version'],
                'status' => 'inactive',
                'migrations' => $migratedFiles,
                'metadata' => $metadata,
                'installed_at' => now(),
            ]);

            return $plugin;
        } catch (\Exception $e) {
            // Cleanup on error
            if (isset($destinationDir) && File::exists($destinationDir)) {
                File::deleteDirectory($destinationDir);
            }
            throw $e;
        } finally {
            // Clean up temporary directory
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
        }
    }

    /**
     * Configure a plugin with the provided configuration.
     */
    public function configure(Plugin $plugin, array $config): void
    {
        $instance = $plugin->getInstance();

        // Validate configuration
        $instance->validateConfig($config);

        // Encrypt sensitive fields
        $encryptedConfig = $this->encryptSensitiveFields($config, $instance);

        $plugin->update([
            'config' => $encryptedConfig,
        ]);
    }

    /**
     * Uninstall a plugin with migration rollback.
     */
    public function uninstall(Plugin $plugin): void
    {
        // 1. Check if safe to uninstall
        if ($plugin->type === 'payment_gateway') {
            $activeSubscriptions = \App\Models\Subscription::where('payment_method', $plugin->name)
                ->where('status', 'active')
                ->count();

            if ($activeSubscriptions > 0) {
                throw new \Exception(
                    "Cannot uninstall {$plugin->name}. There are {$activeSubscriptions} active subscriptions using this payment method."
                );
            }
        }

        // 2. Rollback migrations (reverse order)
        if ($plugin->hasMigrations()) {
            foreach (array_reverse($plugin->getMigrations()) as $migrationFile) {
                $migrationPath = database_path("migrations/{$migrationFile}");

                if (File::exists($migrationPath)) {
                    try {
                        // Rollback migration
                        \Artisan::call('migrate:rollback', [
                            '--path' => "database/migrations/{$migrationFile}",
                            '--force' => true,
                        ]);

                        // Delete migration file
                        File::delete($migrationPath);
                    } catch (\Exception $e) {
                        // Log error but continue
                        \Log::warning("Failed to rollback migration {$migrationFile}: ".$e->getMessage());
                    }
                }
            }
        }

        // 3. Delete plugin directory
        $typeDirectory = match ($plugin->type) {
            'payment_gateway' => 'PaymentGateways',
            'platform' => 'Platforms',
            'bridge' => 'Bridges',
            'notification' => 'Notifications',
            default => 'Other',
        };

        $pluginDir = app_path("Plugins/{$typeDirectory}/{$plugin->slug}");
        if (File::exists($pluginDir)) {
            File::deleteDirectory($pluginDir);
        }

        // 4. Delete database record
        $plugin->delete();
    }

    /**
     * Get all active payment gateway plugins.
     */
    public function getActiveGateways(): Collection
    {
        return Plugin::active()
            ->byType('payment_gateway')
            ->get()
            ->map(fn ($plugin) => $plugin->getInstance());
    }

    /**
     * Get a specific payment gateway plugin instance.
     */
    public function getGateway(string $name): ?PaymentGatewayPlugin
    {
        $plugin = Plugin::active()
            ->byType('payment_gateway')
            ->where('name', $name)
            ->first();

        return $plugin ? $plugin->getInstance() : null;
    }

    /**
     * Get all active platform plugins.
     */
    public function getActivePlatforms(): Collection
    {
        return Plugin::active()
            ->byType('platform')
            ->get()
            ->map(fn ($plugin) => $plugin->getInstance());
    }

    /**
     * Get all active platform plugins that support Appetize.io.
     */
    public function getPlatformsWithAppetizeSupport(): Collection
    {
        return $this->getActivePlatforms()
            ->filter(fn ($platform) => $platform->supportsAppetize());
    }

    /**
     * Get a specific platform plugin instance by slug.
     */
    public function getPlatformBySlug(string $slug): ?PlatformPlugin
    {
        $plugin = Plugin::active()
            ->byType('platform')
            ->where('slug', $slug)
            ->first();

        return $plugin ? $plugin->getInstance() : null;
    }

    /**
     * Get the default platform plugin.
     * Returns Android WebView platform by default.
     */
    public function getDefaultPlatform(): ?PlatformPlugin
    {
        return $this->getPlatformBySlug('android-webview');
    }

    /**
     * Get platform plugin by ID.
     */
    public function getPlatformById(int $id): ?PlatformPlugin
    {
        $plugin = Plugin::active()
            ->byType('platform')
            ->where('id', $id)
            ->first();

        return $plugin ? $plugin->getInstance() : null;
    }

    /**
     * Validate plugin requirements (PHP, Laravel, Appy versions).
     */
    protected function validateRequirements(array $requirements): void
    {
        // Validate PHP version
        if (isset($requirements['php'])) {
            $phpVersion = PHP_VERSION;
            $required = str_replace('>=', '', $requirements['php']);

            if (version_compare($phpVersion, $required, '<')) {
                throw new \Exception("PHP {$required}+ required, but found {$phpVersion}");
            }
        }

        // Validate Laravel version
        if (isset($requirements['laravel'])) {
            $laravelVersion = app()->version();
            $required = str_replace('>=', '', $requirements['laravel']);

            if (version_compare($laravelVersion, $required, '<')) {
                throw new \Exception("Laravel {$required}+ required, but found {$laravelVersion}");
            }
        }

        // Validate Appy version (can be implemented later when versioning is added)
        if (isset($requirements['appy'])) {
            // Future: Check Appy version
        }
    }

    /**
     * Run plugin migrations and return list of installed migration files.
     */
    protected function runPluginMigrations(string $pluginDir, array $migrations): array
    {
        $migratedFiles = [];

        foreach ($migrations as $migration) {
            $sourcePath = $pluginDir.'/'.$migration;

            if (! File::exists($sourcePath)) {
                throw new \Exception("Migration not found: {$migration}");
            }

            // Generate timestamped filename
            $timestamp = now()->format('Y_m_d_His');
            $basename = basename($migration);

            // Remove numeric prefix if exists (001_, 002_, etc.)
            $basename = preg_replace('/^\d+_/', '', $basename);

            $destinationFilename = "{$timestamp}_{$basename}";
            $destinationPath = database_path("migrations/{$destinationFilename}");

            // Copy migration to app migrations folder
            File::copy($sourcePath, $destinationPath);

            try {
                // Run migration
                \Artisan::call('migrate', [
                    '--path' => "database/migrations/{$destinationFilename}",
                    '--force' => true,
                ]);

                $migratedFiles[] = $destinationFilename;
            } catch (\Exception $e) {
                // Rollback on error: delete the migration file
                File::delete($destinationPath);
                throw new \Exception("Migration failed: {$basename}. Error: ".$e->getMessage());
            }

            // Small delay to ensure unique timestamps
            usleep(100000); // 0.1 second
        }

        return $migratedFiles;
    }

    /**
     * Process configuration before saving.
     * Currently just returns the config as-is since encryption is not needed.
     * Sensitive values are protected by database access controls and authentication.
     */
    protected function encryptSensitiveFields(array $config, PluginContract $instance): array
    {
        // No encryption needed - just return config as-is
        return $config;
    }

    /**
     * Check if a plugin is installed by slug.
     */
    public function isInstalled(string $slug): bool
    {
        return Plugin::where('slug', $slug)->exists();
    }

    /**
     * Get plugin by class name.
     */
    public function getByClass(string $class): ?Plugin
    {
        return Plugin::where('class', $class)->first();
    }
}
