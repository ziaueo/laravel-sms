<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDevice extends Model
{
    protected $fillable = [
        'school_id', 'device_id', 'device_name',
        'location', 'secret_key', 'is_active',
    ];

    protected $hidden = ['secret_key'];

    protected $casts = ['is_active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function userPins()
    {
        return $this->hasMany(DeviceUserPin::class, 'device_id');
    }

    public function logs()
    {
        return $this->hasMany(AttendanceDeviceLog::class, 'device_id');
    }
}
