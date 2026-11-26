<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    /**
     * Google reCAPTCHA verify URL.
     */
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Minimum score threshold for reCAPTCHA v3 (0.0 - 1.0).
     */
    protected const SCORE_THRESHOLD = 0.5;

    /**
     * Check if reCAPTCHA is enabled.
     */
    public static function isEnabled(): bool
    {
        return (bool) SystemSetting::get('recaptcha_enabled', false);
    }

    /**
     * Get the site key for frontend.
     */
    public static function getSiteKey(): ?string
    {
        return SystemSetting::get('recaptcha_site_key');
    }

    /**
     * Get the secret key for verification.
     */
    protected static function getSecretKey(): ?string
    {
        $encrypted = SystemSetting::get('recaptcha_secret_key');

        if (! $encrypted) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt reCAPTCHA secret key', ['error' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Verify a reCAPTCHA token.
     *
     * @param  string  $token  The reCAPTCHA response token from frontend
     * @param  string|null  $action  Expected action name (optional)
     * @return bool True if verification passes, false otherwise
     */
    public static function verify(string $token, ?string $action = null): bool
    {
        // If reCAPTCHA is disabled, always pass
        if (! self::isEnabled()) {
            return true;
        }

        $secretKey = self::getSecretKey();

        if (! $secretKey) {
            Log::warning('reCAPTCHA is enabled but secret key is not configured');

            return false;
        }

        try {
            $response = Http::asForm()->post(self::VERIFY_URL, [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            if (! $response->successful()) {
                Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status(),
                ]);

                return false;
            }

            $result = $response->json();

            // Check if verification was successful
            if (! ($result['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'error-codes' => $result['error-codes'] ?? [],
                ]);

                return false;
            }

            // Check score for v3
            $score = $result['score'] ?? 1.0;
            if ($score < self::SCORE_THRESHOLD) {
                Log::warning('reCAPTCHA score below threshold', [
                    'score' => $score,
                    'threshold' => self::SCORE_THRESHOLD,
                ]);

                return false;
            }

            // Optionally check action
            if ($action && isset($result['action']) && $result['action'] !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $action,
                    'actual' => $result['action'],
                ]);

                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
