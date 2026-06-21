<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'school_id', 'school_year_id', 'grade_level_id',
        'homeroom_teacher_id', 'name', 'capacity', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_classrooms')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
