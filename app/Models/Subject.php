<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'school_id', 'grade_level_id', 'name', 'code', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function kkm()
    {
        return $this->hasMany(SubjectKkm::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }
}
