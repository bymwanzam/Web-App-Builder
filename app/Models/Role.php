<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'badge_type',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function getUserCountAttribute()
    {
        // This will calculate user count dynamically when the users.role_id relationship is implemented
        return User::where('role', $this->name)->count();
    }

    public function isSystem()
    {
        return $this->type === 'system';
    }
}
