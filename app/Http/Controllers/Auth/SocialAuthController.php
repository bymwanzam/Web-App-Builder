<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SocialAccount;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\AdminPaymentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Supported OAuth providers.
     */
    protected array $providers = ['google', 'facebook', 'github'];

    /**
     * Redirect to the OAuth provider.
     */
    public function redirect(string $provider): RedirectResponse
    {
        // Validate provider
        if (! in_array($provider, $this->providers)) {
            return redirect()->route('login')
                ->with('error', 'Invalid authentication provider.');
        }

        // Check if provider is enabled
        if (! $this->isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', ucfirst($provider).' login is not currently available.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the OAuth provider callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        // Validate provider
        if (! in_array($provider, $this->providers)) {
            return redirect()->route('login')
                ->with('error', 'Invalid authentication provider.');
        }

        // Check if provider is enabled
        if (! $this->isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', ucfirst($provider).' login is not currently available.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            return DB::transaction(function () use ($provider, $socialUser) {
                // Check if this social account already exists
                $socialAccount = SocialAccount::findByProvider($provider, $socialUser->getId());

                if ($socialAccount) {
                    // User already has this social account linked - log them in
                    $this->updateSocialAccountTokens($socialAccount, $socialUser);
                    Auth::login($socialAccount->user);

                    return redirect()->intended(route('dashboard'));
                }

                // Check if a user with this email already exists
                $existingUser = User::where('email', $socialUser->getEmail())->first();

                if ($existingUser) {
                    // Auto-link the social account to existing user
                    $this->createSocialAccount($existingUser, $provider, $socialUser);

                    // Update avatar if not set
                    if (! $existingUser->avatar && $socialUser->getAvatar()) {
                        $existingUser->update(['avatar' => $socialUser->getAvatar()]);
                    }

                    Auth::login($existingUser);

                    return redirect()->intended(route('dashboard'));
                }

                // New user - check if registration is enabled
                if (! SystemSetting::get('enable_registration', true)) {
                    return redirect()->route('login')
                        ->with('error', 'Registration is currently disabled. Please contact support.');
                }

                // Create new user
                $user = $this->createUser($socialUser);

                // Create social account for new user
                $this->createSocialAccount($user, $provider, $socialUser);

                // Verify email automatically for social logins
                if (! $user->email_verified_at) {
                    $user->update(['email_verified_at' => now()]);
                }

                // Send admin notification for new registration
                AdminPaymentNotification::sendIfEnabled('user_registered', $user);

                Auth::login($user);

                return redirect()->intended(route('dashboard'));
            });

        } catch (\Exception $e) {
            Log::error('Social authentication failed', [
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Social authentication failed. Please try again.');
        }
    }

    /**
     * Check if a social provider is enabled in settings.
     */
    protected function isProviderEnabled(string $provider): bool
    {
        return (bool) SystemSetting::get("{$provider}_login_enabled", false);
    }

    /**
     * Create a new user from social login data.
     */
    protected function createUser($socialUser): User
    {
        // Get the default plan
        $defaultPlanId = SystemSetting::get('default_plan_id', null);
        $defaultPlan = $defaultPlanId ? Plan::find($defaultPlanId) : null;

        return User::create([
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'password' => null, // Social-only users don't have passwords
            'role' => 'user',
            'status' => 'active',
            'email_verified_at' => now(),
            'plan_id' => $defaultPlan ? $defaultPlan->id : null,
            'build_credits' => $defaultPlan ? $defaultPlan->monthly_build_credits : 0,
        ]);
    }

    /**
     * Create a social account record for a user.
     */
    protected function createSocialAccount(User $user, string $provider, $socialUser): SocialAccount
    {
        return SocialAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? null,
            'token_expires_at' => $socialUser->expiresIn
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }

    /**
     * Update social account tokens after login.
     */
    protected function updateSocialAccountTokens(SocialAccount $socialAccount, $socialUser): void
    {
        $socialAccount->update([
            'provider_token' => $socialUser->token,
            'provider_refresh_token' => $socialUser->refreshToken ?? $socialAccount->provider_refresh_token,
            'token_expires_at' => $socialUser->expiresIn
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }
}
