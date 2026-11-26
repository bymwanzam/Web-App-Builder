<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Installed
{
    /**
     * Handle an incoming request.
     *
     * Redirect to installer if installation is not complete
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isInstalled()) {
            return redirect()->route('install');
        }

        return $next($request);
    }

    /**
     * Check if application is installed
     */
    private function isInstalled(): bool
    {
        try {
            return SystemSetting::get('installation_completed', false) === true;
        } catch (\Exception $e) {
            // Try to reload config from .env file and check again
            try {
                $this->reloadDatabaseConfig();

                return SystemSetting::get('installation_completed', false) === true;
            } catch (\Exception $e2) {
                // If we still can't check (database not set up yet), not installed
                return false;
            }
        }
    }

    /**
     * Reload database configuration from .env file
     */
    private function reloadDatabaseConfig(): void
    {
        $envPath = base_path('.env');
        if (! file_exists($envPath)) {
            return;
        }

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
                \DB::purge('sqlite');
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
            \DB::purge('mysql');
        }
    }
}
