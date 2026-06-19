<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceUserPin extends Model
{
    protected $fillable = ['device_id', 'pin', 'user_id', 'role_context'];

    public function device()
    {
        return $this->belongsTo(AttendanceDevice::class, 'device_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
