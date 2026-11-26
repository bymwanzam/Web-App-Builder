<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AccountDeletionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'confirmation_token',
        'cancellation_token',
        'scheduled_at',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createForUser(User $user): self
    {
        $graceDays = SystemSetting::get('account_deletion_grace_days', 7);

        return self::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'confirmation_token' => Str::random(64),
            'cancellation_token' => Str::random(64),
            'scheduled_at' => now()->addDays($graceDays),
        ]);
    }

    public function isPastGracePeriod(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast();
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pending';
    }
}
