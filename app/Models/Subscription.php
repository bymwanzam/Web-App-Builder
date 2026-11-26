<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'amount',
        'payment_method',
        'subscription_id',
        'billing_info',
        'renewal_at',
        'ends_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'renewal_at' => 'datetime',
        'ends_at' => 'datetime',
        'billing_info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope query to only active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
