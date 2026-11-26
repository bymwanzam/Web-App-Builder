<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\SystemSetting;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstallerService
{
    public array $dependencies;

    public array $permissions;

    public function __construct()
    {
        $this->dependencies = [
            'PHP 8.2+' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'BCMath' => extension_loaded('bcmath'),
            'Ctype' => extension_loaded('ctype'),
            'cURL' => extension_loaded('curl'),
            'DOM' => extension_loaded('dom'),
            'Fileinfo' => extension_loaded('fileinfo'),
            'Filter' => extension_loaded('filter'),
            'GD' => extension_loaded('gd'),
            'Hash' => extension_loaded('hash'),
            'JSON' => extension_loaded('json'),
            'Mbstring' => extension_loaded('mbstring'),
            'OpenSSL' => extension_loaded('openssl'),
            'PCRE' => extension_loaded('pcre'),
            'PDO' => extension_loaded('pdo'),
            'PDO MySQL (Optional)' => extension_loaded('pdo_mysql'),
            'PDO SQLite (Optional)' => extension_loaded('pdo_sqlite'),
            'Session' => extension_loaded('session'),
            'Tokenizer' => extension_loaded('tokenizer'),
            'XML' => extension_loaded('xml'),
            'Zip' => extension_loaded('zip'),
        ];

        $files = [
            '.env',
        ];

        $directories = [
            'bootstrap/cache',
            'public',
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/cache/data',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
        ];

        foreach ($files as $file) {
            $path = base_path($file);
            $this->permissions[$file] = is_writable($path) && is_file($path);
        }

        foreach ($directories as $directory) {
            $path = base_path($directory);
            $this->permissions[$directory] = is_writable($path) && is_dir($path);
        }
    }

    /**
     * Create or update .env configuration file
     *
     * @param  array<string, string>  $data
     *
     * @throws Exception
     */
    public function createConfig(array $data): void
    {
        $dbConnection = $data['db_type'] ?? 'mysql';

        $env = [
            'APP_NAME' => '"Appy"',
            'APP_ENV' => 'production',
            'APP_KEY' => 'base64:'.base64_encode(Str::random(32)),
            'APP_DEBUG' => 'false',
            'APP_DEMO' => 'false',
            'APP_TIMEZONE' => 'UTC',
            'APP_URL' => request()->getSchemeAndHttpHost(),

            'DB_CONNECTION' => $dbConnection,

            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => '120',
            'QUEUE_CONNECTION' => 'sync',

            'CACHE_STORE' => 'file',
            'FILESYSTEM_DISK' => 'local',
        ];

        // Add database-specific configuration
        if ($dbConnection === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            $env['DB_DATABASE'] = $dbPath;

            // Create SQLite database file if it doesn't exist
            if (! file_exists($dbPath)) {
                if (! touch($dbPath)) {
                    throw new Exception('Failed to create SQLite database file. Please ensure the database directory is writable.');
                }
                chmod($dbPath, 0664);
            }
        } else {
            // MySQL configuration
            $env['DB_HOST'] = $data['host'];
            $env['DB_PORT'] = $data['port'];
            $env['DB_DATABASE'] = $data['database'];
            $env['DB_USERNAME'] = $data['username'];
            $env['DB_PASSWORD'] = $data['password'] ?? '';
        }

        $envPath = base_path('.env');
        $result = file_put_contents($envPath, $this->toEnv($env));

        if ($result === false) {
            throw new Exception('Failed to write to .env file. Please ensure the file is writable.');
        }

        // Reload configuration to pick up new .env values
        Artisan::call('config:clear');

        // Purge database connection to force reconnection with new credentials
        DB::purge($dbConnection);

        // Create storage symlink
        Artisan::call('storage:link');
    }

    /**
     * Run migrations and seeders, create admin user
     *
     * @param  array<string, string>  $data
     */
    public function createAdmin(array $data): void
    {
        // Force reload configuration from .env file
        $this->reloadDatabaseConfig();

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);

        // Update session and cache drivers to database now that tables exist
        $this->updateEnvValue('SESSION_DRIVER', 'database');
        $this->updateEnvValue('CACHE_STORE', 'database');

        // Run required seeders
        Artisan::call('db:seed', [
            '--class' => 'PlanSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'SystemSettingSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'PlatformPluginsSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'LanguageSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'EmailTemplateSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'AppBuilderSeeder',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'PaymentGatewayPluginsSeeder',
            '--force' => true,
        ]);

        // Get the first available plan (should be Free plan)
        $plan = Plan::orderBy('price')->first();

        // Create admin user
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'status' => 'active',
            'plan_id' => $plan?->id,
            'build_credits' => $plan?->monthly_build_credits ?? 0,
            'email_verified_at' => now(),
        ]);

        // Save site settings
        SystemSetting::set('site_name', $data['site_name'] ?? 'Appy', 'string', 'general');

        // Save purchase code if provided
        if (! empty($data['purchase_code'])) {
            SystemSetting::set('purchase_code', $data['purchase_code'], 'string', 'general');
        }

        // Mark installation as completed
        SystemSetting::set('installation_completed', true, 'boolean', 'app');
    }

    /**
     * Get current database configuration
     */
    public function getDbConfig(): array
    {
        return [
            'db_type' => config('database.default', 'mysql'),
            'host' => config('database.connections.mysql.host', 'localhost'),
            'port' => config('database.connections.mysql.port', '3306'),
            'database' => config('database.connections.mysql.database', ''),
            'username' => config('database.connections.mysql.username', ''),
            'password' => '',
        ];
    }

    /**
     * Test database connection
     */
    public function testDatabaseConnection(array $config): bool
    {
        $dbType = $config['db_type'] ?? 'mysql';

        try {
            // Clear any existing connection
            DB::purge($dbType);

            if ($dbType === 'sqlite') {
                // For SQLite, test if we can create/access the database file
                $dbPath = database_path('database.sqlite');
                $dbDir = dirname($dbPath);

                // Check if database directory exists and is writable
                if (! is_dir($dbDir)) {
                    \Log::error('Database directory does not exist: '.$dbDir);

                    return false;
                }

                if (! is_writable($dbDir)) {
                    \Log::error('Database directory is not writable: '.$dbDir);

                    return false;
                }

                // Try to create a test file
                if (! file_exists($dbPath)) {
                    if (! touch($dbPath)) {
                        \Log::error('Cannot create SQLite database file: '.$dbPath);

                        return false;
                    }
                    chmod($dbPath, 0664);
                }

                // Test SQLite connection
                config([
                    'database.connections.sqlite.database' => $dbPath,
                ]);

                DB::connection('sqlite')->getPdo();

                return true;
            } else {
                // MySQL connection test
                config([
                    'database.connections.mysql.host' => $config['host'],
                    'database.connections.mysql.port' => $config['port'],
                    'database.connections.mysql.database' => $config['database'],
                    'database.connections.mysql.username' => $config['username'],
                    'database.connections.mysql.password' => $config['password'] ?? '',
                ]);

                DB::connection('mysql')->getPdo();

                return true;
            }
        } catch (\Exception $e) {
            // Log the actual error for debugging
            \Log::error('Database connection test failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Convert array to .env file format
     */
    private function toEnv(array $data): string
    {
        $lines = [];

        // Keys that need quoting for special characters
        $checkForSpecialChars = ['DB_PASSWORD'];

        foreach ($data as $key => $value) {
            // Convert booleans to strings
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            // Convert nulls to empty strings
            if (is_null($value)) {
                $value = '';
            } elseif (! is_string($value)) {
                $value = (string) $value;
            }

            // Quote values with spaces or special characters
            $hasSpecialChars = preg_match('/[!@$%^&*()+=\[\]{}|;:,.<>?#]/', $value);
            $needsQuotes = str_contains($value, ' ')
                || (in_array($key, $checkForSpecialChars) && $hasSpecialChars)
                || ($key !== 'DB_PASSWORD' && $hasSpecialChars);

            if ($needsQuotes && $value !== '') {
                $value = '"'.$value.'"';
            }

            $lines[] = "{$key}={$value}";
        }

        return implode("\n", $lines);
    }

    /**
     * Update a single value in .env file
     */
    private function updateEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if ($envContent === false) {
            return;
        }

        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, $replacement, $envContent);
        } else {
            $envContent .= "\n{$replacement}";
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Reload database configuration from .env file
     */
    private function reloadDatabaseConfig(): void
    {
        // Read .env file directly
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        if ($envContent === false) {
            return;
        }

        // Parse database connection type
        $dbConnection = 'mysql';
        if (preg_match('/^DB_CONNECTION=(.*)$/m', $envContent, $matches)) {
            $dbConnection = trim(trim($matches[1]), '"\'');
        }

        if ($dbConnection === 'sqlite') {
            // Parse SQLite database path
            if (preg_match('/^DB_DATABASE=(.*)$/m', $envContent, $matches)) {
                $dbPath = trim(trim($matches[1]), '"\'');

                // Update Laravel's config for SQLite
                config([
                    'database.default' => 'sqlite',
                    'database.connections.sqlite.database' => $dbPath,
                ]);

                // Purge existing connection to force reconnect
                DB::purge('sqlite');
            }
        } else {
            // Parse MySQL credentials from .env
            $dbConfig = [];
            $keys = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];

            foreach ($keys as $key) {
                if (preg_match("/^{$key}=(.*)$/m", $envContent, $matches)) {
                    $value = trim($matches[1]);
                    // Remove quotes if present
                    $value = trim($value, '"\'');
                    $dbConfig[$key] = $value;
                }
            }

            // Update Laravel's config for MySQL
            config([
                'database.default' => 'mysql',
                'database.connections.mysql.host' => $dbConfig['DB_HOST'] ?? 'localhost',
                'database.connections.mysql.port' => $dbConfig['DB_PORT'] ?? '3306',
                'database.connections.mysql.database' => $dbConfig['DB_DATABASE'] ?? '',
                'database.connections.mysql.username' => $dbConfig['DB_USERNAME'] ?? '',
                'database.connections.mysql.password' => $dbConfig['DB_PASSWORD'] ?? '',
            ]);

            // Purge existing connection to force reconnect
            DB::purge('mysql');
        }
    }
}
