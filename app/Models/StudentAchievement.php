<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    protected $fillable = [
        'student_id', 'school_year_id', 'title',
        'level', 'date', 'description', 'certificate',
    ];

    protected $casts = ['date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
