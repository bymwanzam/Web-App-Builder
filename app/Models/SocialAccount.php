<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'provider_token',
        'provider_refresh_token',
        'token_expires_at',
    ];

    protected $hidden = [
        'provider_token',
        'provider_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
            'provider_token' => 'encrypted',
            'provider_refresh_token' => 'encrypted',
        ];
    }

    /**
     * Get the user that owns the social account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find a social account by provider and provider ID.
     */
    public static function findByProvider(string $provider, string $providerId): ?self
    {
        return self::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }
}
