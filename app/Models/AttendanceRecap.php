<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\RecapTypeConstant;

class AttendanceRecap extends Model
{
    protected $fillable = [
        'recap_type', 'reference_id', 'school_year_id',
        'month', 'year', 'total_days', 'total_hadir',
        'total_sakit', 'total_izin', 'total_alpa',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function getRecapTypeLabelAttribute(): string
    {
        return RecapTypeConstant::getLabel($this->recap_type);
    }
}
