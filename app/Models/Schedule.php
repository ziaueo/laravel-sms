<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\ScheduleConstant;

class Schedule extends Model
{
    protected $fillable = [
        'classroom_id', 'school_year_id', 'day_of_week',
        'start_time', 'end_time', 'type', 'subject_id', 'teacher_id',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function substitutions()
    {
        return $this->hasMany(ScheduleSubstitution::class);
    }

    public function getDayLabelAttribute(): string
    {
        return ScheduleConstant::getDayLabel($this->day_of_week);
    }

    public function getTypeLabelAttribute(): string
    {
        return ScheduleConstant::getTypeLabel($this->type);
    }
}
