<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppetizeUsage extends Model
{
    protected $table = 'appetize_usage';

    protected $fillable = [
        'user_id',
        'app_build_id',
        'minutes_used',
        'session_started_at',
        'session_ended_at',
    ];

    protected $casts = [
        'session_started_at' => 'datetime',
        'session_ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appBuild(): BelongsTo
    {
        return $this->belongsTo(AppBuild::class);
    }
}
