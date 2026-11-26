<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'external_transaction_id',
        'user_id',
        'subscription_id',
        'amount',
        'currency',
        'status',
        'type',
        'payment_method',
        'transaction_date',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = 'TRX-'.strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get formatted invoice number.
     */
    public function getInvoiceNumberAttribute()
    {
        return '#INV-'.date('Y', strtotime($this->created_at)).'-'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
