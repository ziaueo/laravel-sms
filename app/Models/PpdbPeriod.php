<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpdbPeriod extends Model
{
    protected $fillable = [
        'school_id', 'school_year_id', 'name',
        'open_date', 'close_date', 'quota',
        'requirements', 'description', 'is_active',
    ];

    protected $casts = [
        'open_date'    => 'date',
        'close_date'   => 'date',
        'requirements' => 'array',
        'is_active'    => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function registrations()
    {
        return $this->hasMany(PpdbRegistration::class);
    }

    public function getIsOpenAttribute(): bool
    {
        $today = now()->toDateString();
        return $this->is_active
            && $today >= $this->open_date->toDateString()
            && $today <= $this->close_date->toDateString();
    }
}
