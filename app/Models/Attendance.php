<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\AttendanceConstant;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 'classroom_id', 'school_year_id',
        'date', 'status', 'check_in', 'check_out',
        'source', 'notes', 'recorded_by',
    ];

    protected $casts = ['date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
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

    public function getSourceLabelAttribute(): string
    {
        return AttendanceConstant::getSourceLabel($this->source);
    }
}
