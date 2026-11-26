<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotificationConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'provider',
        'google_services_file',
        'firebase_credentials_file',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }
}
