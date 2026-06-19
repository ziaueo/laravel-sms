<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\DeviceLogConstant;

class AttendanceDeviceLog extends Model
{
    protected $fillable = [
        'device_id', 'pin', 'punch_time', 'type',
        'role_context', 'is_processed', 'processed_at', 'reference_id',
    ];

    protected $casts = [
        'punch_time'   => 'datetime',
        'processed_at' => 'datetime',
        'is_processed' => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(AttendanceDevice::class, 'device_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return DeviceLogConstant::getLabel($this->type);
    }
}
