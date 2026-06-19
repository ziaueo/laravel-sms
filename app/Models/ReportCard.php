<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCard extends Model
{
    protected $fillable = [
        'student_id', 'classroom_id', 'school_year_id',
        'gpa', 'rank_in_class', 'rank_in_grade',
        'total_hadir', 'total_sakit', 'total_izin', 'total_alpa',
        'homeroom_notes', 'principal_notes',
        'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

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
}
