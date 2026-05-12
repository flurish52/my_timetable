<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'token',
        'device_name',
        'platform',
        'device_name',
        'scope',
        'last_active_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
