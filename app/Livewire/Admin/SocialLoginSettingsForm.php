<?php

namespace App\Livewire\Admin;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SocialLoginSettingsForm extends Component
{
    // General auth settings
    public $enable_registration = true;

    // Email verification settings
    public $require_email_verification = true;

    // reCAPTCHA settings
    public $recaptcha_enabled = false;

    public $recaptcha_site_key = '';

    public $recaptcha_secret_key = '';

    // Google settings
    public $google_login_enabled = false;

    public $google_client_id = '';

    public $google_client_secret = '';

    // Facebook settings
    public $facebook_login_enabled = false;

    public $facebook_client_id = '';

    public $facebook_client_secret = '';

    // GitHub settings
    public $github_login_enabled = false;

    public $github_client_id = '';

    public $github_client_secret = '';

    public function mount()
    {
        // Load general auth settings
        $this->enable_registration = (bool) SystemSetting::get('enable_registration', true);

        // Load email verification settings
        $this->require_email_verification = (bool) SystemSetting::get('require_email_verification', true);

        // Load reCAPTCHA settings
        $this->recaptcha_enabled = (bool) SystemSetting::get('recaptcha_enabled', false);
        $this->recaptcha_site_key = SystemSetting::get('recaptcha_site_key', '');
        // Don't show decrypted secret, just indicate if it's set
        $this->recaptcha_secret_key = '';

        // Load Google settings
        $this->google_login_enabled = (bool) SystemSetting::get('google_login_enabled', false);
        $this->google_client_id = SystemSetting::get('google_client_id', '');
        // Don't show decrypted secret, just indicate if it's set
        $this->google_client_secret = '';

        // Load Facebook settings
        $this->facebook_login_enabled = (bool) SystemSetting::get('facebook_login_enabled', false);
        $this->facebook_client_id = SystemSetting::get('facebook_client_id', '');
        $this->facebook_client_secret = '';

        // Load GitHub settings
        $this->github_login_enabled = (bool) SystemSetting::get('github_login_enabled', false);
        $this->github_client_id = SystemSetting::get('github_client_id', '');
        $this->github_client_secret = '';

        // Clear sensitive fields in demo mode
        if (config('app.demo')) {
            $this->recaptcha_site_key = '';
            $this->google_client_id = '';
            $this->facebook_client_id = '';
            $this->github_client_id = '';
        }
    }

    public function save()
    {
        if (config('app.demo')) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Settings cannot be modified in demo mode.'
            );

            return;
        }

        $this->validate([
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'google_client_id' => 'nullable|string|max:255',
            'google_client_secret' => 'nullable|string|max:255',
            'facebook_client_id' => 'nullable|string|max:255',
            'facebook_client_secret' => 'nullable|string|max:255',
            'github_client_id' => 'nullable|string|max:255',
            'github_client_secret' => 'nullable|string|max:255',
        ]);

        // Save general auth settings
        SystemSetting::set('enable_registration', $this->enable_registration ? '1' : '0', 'boolean', 'auth');

        // Save email verification settings
        SystemSetting::set('require_email_verification', $this->require_email_verification ? '1' : '0', 'boolean', 'auth');

        // Save reCAPTCHA settings
        SystemSetting::set('recaptcha_enabled', $this->recaptcha_enabled ? '1' : '0', 'boolean', 'auth');
        SystemSetting::set('recaptcha_site_key', $this->recaptcha_site_key, 'string', 'auth');
        if ($this->recaptcha_secret_key) {
            SystemSetting::set('recaptcha_secret_key', Crypt::encryptString($this->recaptcha_secret_key), 'string', 'auth');
        }

        // Save Google settings
        SystemSetting::set('google_login_enabled', $this->google_login_enabled ? '1' : '0', 'boolean', 'social');
        SystemSetting::set('google_client_id', $this->google_client_id, 'string', 'social');
        if ($this->google_client_secret) {
            SystemSetting::set('google_client_secret', Crypt::encryptString($this->google_client_secret), 'string', 'social');
        }

        // Save Facebook settings
        SystemSetting::set('facebook_login_enabled', $this->facebook_login_enabled ? '1' : '0', 'boolean', 'social');
        SystemSetting::set('facebook_client_id', $this->facebook_client_id, 'string', 'social');
        if ($this->facebook_client_secret) {
            SystemSetting::set('facebook_client_secret', Crypt::encryptString($this->facebook_client_secret), 'string', 'social');
        }

        // Save GitHub settings
        SystemSetting::set('github_login_enabled', $this->github_login_enabled ? '1' : '0', 'boolean', 'social');
        SystemSetting::set('github_client_id', $this->github_client_id, 'string', 'social');
        if ($this->github_client_secret) {
            SystemSetting::set('github_client_secret', Crypt::encryptString($this->github_client_secret), 'string', 'social');
        }

        // Clear secrets after save (don't keep in memory)
        $this->recaptcha_secret_key = '';
        $this->google_client_secret = '';
        $this->facebook_client_secret = '';
        $this->github_client_secret = '';

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Authentication settings updated successfully!'
        );
    }

    /**
     * Check if a provider has a secret configured.
     */
    public function hasSecret(string $provider): bool
    {
        $secret = SystemSetting::get("{$provider}_client_secret");

        return ! empty($secret);
    }

    /**
     * Get the callback URL for a provider.
     */
    public function getCallbackUrl(string $provider): string
    {
        return url("/auth/{$provider}/callback");
    }

    /**
     * Check if reCAPTCHA has a secret configured.
     */
    public function recaptchaHasSecret(): bool
    {
        $secret = SystemSetting::get('recaptcha_secret_key');

        return ! empty($secret);
    }

    public function render()
    {
        return view('livewire.admin.social-login-settings-form', [
            'recaptchaHasSecret' => $this->recaptchaHasSecret(),
            'googleHasSecret' => $this->hasSecret('google'),
            'facebookHasSecret' => $this->hasSecret('facebook'),
            'githubHasSecret' => $this->hasSecret('github'),
            'googleCallbackUrl' => $this->getCallbackUrl('google'),
            'facebookCallbackUrl' => $this->getCallbackUrl('facebook'),
            'githubCallbackUrl' => $this->getCallbackUrl('github'),
        ]);
    }
}
