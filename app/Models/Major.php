<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'school_id', 'name', 'code',
        'grade_level_start', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
