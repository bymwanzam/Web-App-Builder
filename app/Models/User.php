<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPasswordNotification;
use App\Notifications\CustomVerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'role',
        'status',
        'plan_id',
        'build_credits',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function apps()
    {
        return $this->hasMany(App::class);
    }

    /**
     * Get subscriptions for this user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get active subscription for this user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest();
    }

    /**
     * Get keystores for this user.
     */
    public function keystores()
    {
        return $this->hasMany(Keystore::class);
    }

    /**
     * Get social accounts for this user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get consent records for this user.
     */
    public function consents()
    {
        return $this->hasMany(UserConsent::class);
    }

    /**
     * Get transactions for this user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get data export requests for this user.
     */
    public function dataExportRequests()
    {
        return $this->hasMany(DataExportRequest::class);
    }

    /**
     * Get account deletion requests for this user.
     */
    public function accountDeletionRequests()
    {
        return $this->hasMany(AccountDeletionRequest::class);
    }

    /**
     * Check if user has a specific social provider linked.
     */
    public function hasSocialProvider(string $provider): bool
    {
        return $this->socialAccounts()->where('provider', $provider)->exists();
    }

    /**
     * Get a specific social account by provider.
     */
    public function getSocialAccount(string $provider): ?SocialAccount
    {
        return $this->socialAccounts()->where('provider', $provider)->first();
    }

    /**
     * Check if user has enough build credits.
     *
     * @param  string  $platform  Platform (android, ios, etc.) - kept for backward compatibility
     * @param  int  $requiredCredits  Number of credits required (default 1)
     * @return bool
     */
    public function hasBuildCredits($platform, $requiredCredits = 1)
    {
        // Admins and unlimited plans always have credits
        if ($this->isAdmin() || ($this->plan && $this->plan->monthly_build_credits === -1)) {
            return true;
        }

        return $this->build_credits >= $requiredCredits;
    }

    /**
     * Get the credit cost for a platform build.
     *
     * @deprecated Use AppBuilder's credit_cost instead
     */
    public function getBuildCreditCost($platform)
    {
        return match ($platform) {
            'android' => 1,
            default => 1,
        };
    }

    /**
     * Deduct build credits.
     *
     * @param  string  $platform  Platform (android, ios, etc.) - kept for backward compatibility
     * @param  int  $amount  Number of credits to deduct (default 1)
     * @return bool
     */
    public function deductBuildCredits($platform, $amount = 1)
    {
        // Don't deduct for admins or plans with unlimited credits
        if ($this->isAdmin() || ($this->plan && $this->plan->monthly_build_credits === -1)) {
            return true;
        }

        if ($this->build_credits < $amount) {
            return false;
        }

        $this->decrement('build_credits', $amount);

        return true;
    }

    /**
     * Get remaining build credits.
     */
    public function getRemainingCreditsAttribute()
    {
        if ($this->plan && $this->plan->monthly_build_credits === -1) {
            return 'Unlimited';
        }

        return $this->build_credits;
    }

    /**
     * Refill build credits based on plan (monthly reset).
     */
    public function refillBuildCredits()
    {
        if (! $this->plan) {
            return;
        }

        // Don't refill unlimited plans
        if ($this->plan->monthly_build_credits === -1) {
            return;
        }

        $this->update(['build_credits' => $this->plan->monthly_build_credits]);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Scope query to only admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope query to only regular users.
     */
    public function scopeRegularUsers($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new CustomVerifyEmailNotification);
    }

    /**
     * Check if user can use Appetize previews.
     */
    public function canUseAppetize(): bool
    {
        return $this->plan?->canUseAppetize() ?? false;
    }

    /**
     * Check if user can use the API.
     */
    public function canUseApi(): bool
    {
        return $this->isAdmin() || ($this->plan?->canUseApi() ?? false);
    }

    /**
     * Check if user can use hosted pages.
     */
    public function canUseHostedPages(): bool
    {
        return $this->isAdmin() || ($this->plan?->canUseHostedPages() ?? false);
    }

    /**
     * Get remaining Appetize minutes for current month.
     */
    public function getRemainingAppetizeMinutes(): int
    {
        if (! $this->canUseAppetize()) {
            return 0;
        }

        if ($this->plan->hasUnlimitedAppetize()) {
            return PHP_INT_MAX;
        }

        $usedMinutes = \App\Models\AppetizeUsage::where('user_id', $this->id)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('minutes_used');

        $totalMinutes = $this->plan->getAppetizeMinutes();

        return max(0, $totalMinutes - $usedMinutes);
    }

    /**
     * Get user's AI settings.
     */
    public function aiSettings()
    {
        return $this->hasOne(UserAiSettings::class);
    }

    /**
     * Get user's AI usage records.
     */
    public function aiUsage()
    {
        return $this->hasMany(AiUsage::class);
    }

    /**
     * Get user's AI conversations.
     */
    public function aiConversations()
    {
        return $this->hasMany(AiConversation::class);
    }

    /**
     * Check if user can use AI chat.
     */
    public function canUseAiChat(): bool
    {
        return $this->isAdmin() || ($this->plan?->canUseAiChat() ?? false);
    }

    /**
     * Check if user can use their own AI API key.
     */
    public function canUseOwnAiApiKey(): bool
    {
        return $this->isAdmin() || ($this->plan?->allowsUserAiApiKey() ?? false);
    }

    /**
     * Get remaining AI tokens for current month.
     */
    public function getRemainingAiTokens(): int
    {
        if (! $this->canUseAiChat()) {
            return 0;
        }

        if ($this->isAdmin() || $this->plan->hasUnlimitedAiTokens()) {
            return PHP_INT_MAX;
        }

        $usedTokens = AiUsage::getMonthlyUsageForUser($this->id);

        return max(0, $this->plan->getMonthlyAiTokens() - $usedTokens);
    }

    /**
     * Check if user is using their own API key.
     */
    public function isUsingOwnAiApiKey(): bool
    {
        if (! $this->canUseOwnAiApiKey()) {
            return false;
        }

        $settings = $this->aiSettings;
        if (! $settings || $settings->preferred_provider === 'system') {
            return false;
        }

        return $settings->hasApiKeyFor($settings->preferred_provider);
    }
}
