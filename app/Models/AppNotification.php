<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'user_id',
        'title',
        'body',
        'image_url',
        'notification_style',
        'status',
        'scheduled_at',
        'sent_at',
        'fcm_response',
        'error_message',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending notifications ready to send
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for scheduled notifications that are due
     */
    public function scopeDueForSending($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent($fcmResponse = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'fcm_response' => $fcmResponse,
        ]);
    }

    /**
     * Mark notification as failed
     */
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }
}
