<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\AttendanceConstant;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'teacher_id', 'school_id', 'school_year_id',
        'date', 'status', 'check_in', 'check_out',
        'source', 'notes', 'recorded_by',
    ];

    protected $casts = ['date' => 'date'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return AttendanceConstant::getStatusLabel($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return attendance_badge($this->status);
    }
}
