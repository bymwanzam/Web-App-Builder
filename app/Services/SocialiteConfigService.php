<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class SocialiteConfigService
{
    /**
     * Configure Socialite drivers from database settings.
     */
    public function configure(): void
    {
        try {
            // Check if system_settings table exists (important for migrations and tests)
            if (! Schema::hasTable('system_settings')) {
                return;
            }

            $this->configureGoogle();
            $this->configureFacebook();
            $this->configureGithub();
        } catch (\Exception $e) {
            // Database not accessible yet (during installation or migration)
            return;
        }
    }

    /**
     * Configure Google OAuth driver.
     */
    protected function configureGoogle(): void
    {
        $clientId = SystemSetting::get('google_client_id');
        $clientSecret = $this->decryptSecret(SystemSetting::get('google_client_secret'));

        if ($clientId && $clientSecret) {
            config([
                'services.google' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect' => url('/auth/google/callback'),
                ],
            ]);
        }
    }

    /**
     * Configure Facebook OAuth driver.
     */
    protected function configureFacebook(): void
    {
        $clientId = SystemSetting::get('facebook_client_id');
        $clientSecret = $this->decryptSecret(SystemSetting::get('facebook_client_secret'));

        if ($clientId && $clientSecret) {
            config([
                'services.facebook' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect' => url('/auth/facebook/callback'),
                ],
            ]);
        }
    }

    /**
     * Configure GitHub OAuth driver.
     */
    protected function configureGithub(): void
    {
        $clientId = SystemSetting::get('github_client_id');
        $clientSecret = $this->decryptSecret(SystemSetting::get('github_client_secret'));

        if ($clientId && $clientSecret) {
            config([
                'services.github' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect' => url('/auth/github/callback'),
                ],
            ]);
        }
    }

    /**
     * Decrypt a secret value if it's encrypted.
     */
    protected function decryptSecret(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // If decryption fails, assume it's not encrypted (for backward compatibility)
            return $value;
        }
    }

    /**
     * Check if a provider is fully configured.
     */
    public static function isProviderConfigured(string $provider): bool
    {
        if (! Schema::hasTable('system_settings')) {
            return false;
        }

        $clientId = SystemSetting::get("{$provider}_client_id");
        $clientSecret = SystemSetting::get("{$provider}_client_secret");

        return ! empty($clientId) && ! empty($clientSecret);
    }

    /**
     * Get all configured providers.
     */
    public static function getConfiguredProviders(): array
    {
        $providers = ['google', 'facebook', 'github'];
        $configured = [];

        foreach ($providers as $provider) {
            if (self::isProviderConfigured($provider)) {
                $configured[] = $provider;
            }
        }

        return $configured;
    }

    /**
     * Get all enabled providers.
     */
    public static function getEnabledProviders(): array
    {
        if (! Schema::hasTable('system_settings')) {
            return [];
        }

        $providers = ['google', 'facebook', 'github'];
        $enabled = [];

        foreach ($providers as $provider) {
            if (SystemSetting::get("{$provider}_login_enabled", false) && self::isProviderConfigured($provider)) {
                $enabled[] = $provider;
            }
        }

        return $enabled;
    }
}
