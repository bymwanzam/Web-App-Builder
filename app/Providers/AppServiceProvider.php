<?php

namespace App\Providers;

use App\Models\AppBuild;
use App\Models\SystemSetting;
use App\Observers\AppBuildObserver;
use App\Policies\AppBuildPolicy;
use App\Services\PluginManager;
use App\Services\SocialiteConfigService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PluginManager::class);

        // Register plugin migrations when the migrator is resolved.
        // This ensures plugin migrations are available during `migrate:fresh`.
        $this->callAfterResolving('migrator', function () {
            $this->app->make(PluginManager::class)->registerMigrationsFromFilesystem();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register AppBuildPolicy
        Gate::policy(AppBuild::class, AppBuildPolicy::class);

        // Register AppBuild Observer
        AppBuild::observe(AppBuildObserver::class);

        // Boot plugins (register view namespaces, etc.)
        $pluginManager = $this->app->make(PluginManager::class);
        $pluginManager->boot();

        // Configure Socialite from database settings
        $socialiteConfig = new SocialiteConfigService;
        $socialiteConfig->configure();

        // Configure mail from database settings
        $this->configureMailFromDatabase();

        // Configure API rate limiting - 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Configure AI chat rate limiting - 30 requests per minute
        RateLimiter::for('ai-chat', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        // Configure Scribe API documentation title dynamically
        $this->configureScribeTitle();
    }

    /**
     * Configure Scribe API documentation title from database.
     */
    protected function configureScribeTitle(): void
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            $siteName = SystemSetting::get('site_name', config('app.name'));
            config(['scribe.title' => $siteName.' API Documentation']);
        } catch (\Exception $e) {
            // Silently fail if database is not available
        }
    }

    /**
     * Configure mail settings from database.
     */
    protected function configureMailFromDatabase(): void
    {
        try {
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            $mailMailer = SystemSetting::get('mail_mailer', 'sendmail');

            config([
                'mail.default' => $mailMailer,
                'mail.from.name' => SystemSetting::get('mail_from_name', config('app.name')),
                'mail.from.address' => SystemSetting::get('mail_from_address', 'noreply@example.com'),
            ]);

            if ($mailMailer === 'smtp') {
                $encryption = SystemSetting::get('mail_encryption', 'tls');

                config([
                    'mail.mailers.smtp.host' => SystemSetting::get('mail_host', '127.0.0.1'),
                    'mail.mailers.smtp.port' => (int) SystemSetting::get('mail_port', 587),
                    'mail.mailers.smtp.username' => SystemSetting::get('mail_username'),
                    'mail.mailers.smtp.password' => SystemSetting::get('mail_password'),
                    'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail if database is not available (during migrations, etc.)
        }
    }
}
