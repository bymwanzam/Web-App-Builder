<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'currency',
        'interval_unit',
        'interval_count',
        'status',
        'is_default',
        'features',
        'monthly_build_credits',
        'can_use_custom_code',
        'can_use_appetize',
        'monthly_appetize_minutes',
        'can_use_api',
        'can_use_hosted_pages',
        'max_hosted_pages_per_app',
        'allowed_builders',
        'can_use_ai_chat',
        'monthly_ai_tokens',
        'allow_user_ai_api_key',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_default' => 'boolean',
        'can_use_custom_code' => 'boolean',
        'can_use_appetize' => 'boolean',
        'can_use_api' => 'boolean',
        'can_use_hosted_pages' => 'boolean',
        'max_hosted_pages_per_app' => 'integer',
        'allowed_builders' => 'array',
        'can_use_ai_chat' => 'boolean',
        'monthly_ai_tokens' => 'integer',
        'allow_user_ai_api_key' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getSubscriberCountAttribute()
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }

    /**
     * Get all features including dynamically generated ones for build credits and custom code.
     */
    public function getAllFeaturesAttribute()
    {
        $staticFeatures = $this->features ?? [];
        $dynamicFeatures = [];

        // Generate build credits feature dynamically
        if ($this->monthly_build_credits === -1) {
            $dynamicFeatures[] = ['name' => 'Unlimited build credits', 'included' => true];
        } else {
            $dynamicFeatures[] = ['name' => "{$this->monthly_build_credits} build credits", 'included' => true];
        }

        // Generate builder access feature dynamically
        if ($this->allowed_builders === null) {
            $dynamicFeatures[] = ['name' => 'Access to all active builders', 'included' => true];
        } elseif (is_array($this->allowed_builders)) {
            $builderCount = count($this->allowed_builders);
            if ($builderCount === 0) {
                $dynamicFeatures[] = ['name' => 'Builder access', 'included' => false];
            } elseif ($builderCount === 1) {
                $dynamicFeatures[] = ['name' => 'Access to single builder', 'included' => true];
            } else {
                $dynamicFeatures[] = ['name' => "Access to {$builderCount} builders", 'included' => true];
            }
        }

        // Generate custom CSS/JS feature dynamically
        $dynamicFeatures[] = ['name' => 'Custom CSS/JS injection', 'included' => $this->can_use_custom_code];

        // Generate API access feature dynamically
        $dynamicFeatures[] = ['name' => 'API Access', 'included' => $this->can_use_api];

        // Generate hosted pages feature dynamically
        if ($this->can_use_hosted_pages) {
            if ($this->max_hosted_pages_per_app === null) {
                $dynamicFeatures[] = ['name' => 'Unlimited hosted pages', 'included' => true];
            } else {
                $dynamicFeatures[] = ['name' => "{$this->max_hosted_pages_per_app} hosted pages per app", 'included' => true];
            }
        } else {
            $dynamicFeatures[] = ['name' => 'Hosted pages', 'included' => false];
        }

        // Generate AI chat feature dynamically (only if AI is enabled globally)
        if (SystemSetting::get('ai_chat_enabled', false)) {
            if ($this->can_use_ai_chat) {
                if ($this->monthly_ai_tokens === -1) {
                    $dynamicFeatures[] = ['name' => 'Unlimited AI Chat', 'included' => true];
                } else {
                    $formatted = number_format($this->monthly_ai_tokens);
                    $dynamicFeatures[] = ['name' => "{$formatted} AI tokens/month", 'included' => true];
                }

                if ($this->allow_user_ai_api_key) {
                    $dynamicFeatures[] = ['name' => 'Use your own AI API key', 'included' => true];
                }
            } else {
                $dynamicFeatures[] = ['name' => 'AI Chat Assistant', 'included' => false];
            }
        }

        // Combine dynamic features first, then static features
        return array_merge($dynamicFeatures, $staticFeatures);
    }

    /**
     * Check if a builder is allowed for this plan.
     *
     * @param  int  $builderId
     */
    public function allowsBuilder($builderId): bool
    {
        // Null means all active builders allowed
        if ($this->allowed_builders === null) {
            return true;
        }

        // Empty array means no builders allowed (blocks builds)
        if (empty($this->allowed_builders)) {
            return false;
        }

        return in_array($builderId, $this->allowed_builders);
    }

    /**
     * Get active builders available for this plan.
     */
    public function getAvailableBuildersAttribute()
    {
        $query = \App\Models\AppBuilder::where('status', 'active');

        // If plan restricts builders, filter by allowed IDs
        if ($this->allowed_builders !== null) {
            // Empty array means no builders allowed
            if (empty($this->allowed_builders)) {
                return collect();
            }

            $query->whereIn('id', $this->allowed_builders);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Check if plan allows Appetize previews.
     */
    public function canUseAppetize(): bool
    {
        return $this->can_use_appetize ?? false;
    }

    /**
     * Get monthly Appetize minutes allocation.
     * Returns -1 for unlimited, 0 for disabled.
     */
    public function getAppetizeMinutes(): int
    {
        if (! $this->can_use_appetize) {
            return 0;
        }

        return $this->monthly_appetize_minutes ?? 0;
    }

    /**
     * Check if plan has unlimited Appetize minutes.
     */
    public function hasUnlimitedAppetize(): bool
    {
        return $this->can_use_appetize && $this->monthly_appetize_minutes === -1;
    }

    /**
     * Check if plan allows API access.
     */
    public function canUseApi(): bool
    {
        return $this->can_use_api ?? false;
    }

    /**
     * Check if plan allows hosted pages.
     */
    public function canUseHostedPages(): bool
    {
        return $this->can_use_hosted_pages ?? false;
    }

    /**
     * Get the maximum hosted pages per app.
     * Returns null for unlimited.
     */
    public function getMaxHostedPagesPerApp(): ?int
    {
        if (! $this->can_use_hosted_pages) {
            return 0;
        }

        return $this->max_hosted_pages_per_app;
    }

    /**
     * Check if plan has unlimited hosted pages.
     */
    public function hasUnlimitedHostedPages(): bool
    {
        return $this->can_use_hosted_pages && $this->max_hosted_pages_per_app === null;
    }

    /**
     * Check if plan allows AI chat.
     */
    public function canUseAiChat(): bool
    {
        return $this->can_use_ai_chat ?? false;
    }

    /**
     * Get monthly AI token allocation.
     * Returns -1 for unlimited, 0 for disabled.
     */
    public function getMonthlyAiTokens(): int
    {
        if (! $this->can_use_ai_chat) {
            return 0;
        }

        return $this->monthly_ai_tokens ?? 0;
    }

    /**
     * Check if plan has unlimited AI tokens.
     */
    public function hasUnlimitedAiTokens(): bool
    {
        return $this->can_use_ai_chat && $this->monthly_ai_tokens === -1;
    }

    /**
     * Check if plan allows user to use their own AI API key.
     */
    public function allowsUserAiApiKey(): bool
    {
        return $this->allow_user_ai_api_key ?? false;
    }
}
