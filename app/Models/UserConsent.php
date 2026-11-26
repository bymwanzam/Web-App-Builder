<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'consented',
        'version',
        'ip_address',
        'user_agent',
        'consented_at',
        'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'consented' => 'boolean',
            'consented_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function recordConsent(
        User $user,
        string $type,
        bool $consented,
        ?string $version = null
    ): self {
        return self::updateOrCreate(
            ['user_id' => $user->id, 'consent_type' => $type],
            [
                'consented' => $consented,
                'version' => $version,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'consented_at' => $consented ? now() : null,
                'withdrawn_at' => $consented ? null : now(),
            ]
        );
    }
}
