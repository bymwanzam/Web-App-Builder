<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAiSettings extends Model
{
    protected $fillable = [
        'user_id',
        'preferred_provider',
        'preferred_model',
        'openai_api_key',
        'anthropic_api_key',
        'nvidia_api_key',
    ];

    protected $casts = [
        'openai_api_key' => 'encrypted',
        'anthropic_api_key' => 'encrypted',
        'nvidia_api_key' => 'encrypted',
    ];

    protected $hidden = [
        'openai_api_key',
        'anthropic_api_key',
        'nvidia_api_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has an API key configured for the given provider.
     */
    public function hasApiKeyFor(string $provider): bool
    {
        return match ($provider) {
            'openai' => ! empty($this->openai_api_key),
            'anthropic' => ! empty($this->anthropic_api_key),
            'nvidia' => ! empty($this->nvidia_api_key),
            default => false,
        };
    }

    /**
     * Get the API key for the given provider.
     */
    public function getApiKeyFor(string $provider): ?string
    {
        return match ($provider) {
            'openai' => $this->openai_api_key,
            'anthropic' => $this->anthropic_api_key,
            'nvidia' => $this->nvidia_api_key,
            default => null,
        };
    }
}
