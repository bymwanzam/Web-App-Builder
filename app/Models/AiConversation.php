<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $fillable = [
        'user_id',
        'app_id',
        'platform_plugin_id',
        'conversation_type',
        'title',
        'messages',
        'context_summary',
        'provider',
        'model',
        'total_tokens',
    ];

    protected $casts = [
        'messages' => 'encrypted:array',
        'context_summary' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function platformPlugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class, 'platform_plugin_id');
    }

    public function usage(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    /**
     * Scope to get only main conversations (shown in /user/ai)
     */
    public function scopeMain($query)
    {
        return $query->where('conversation_type', 'main');
    }

    /**
     * Scope to get only widget conversations (hidden from main AI page)
     */
    public function scopeWidget($query)
    {
        return $query->where('conversation_type', 'widget');
    }
}
