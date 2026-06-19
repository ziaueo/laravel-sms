<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\CurriculumConstant;

class SchoolYear extends Model
{
    protected $fillable = [
        'school_id', 'curriculum_id', 'name',
        'year', 'semester', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function getCurriculumLabelAttribute(): string
    {
        return $this->curriculum?->name ?? '-';
    }

    public function getSemesterLabelAttribute(): string
    {
        return 'Semester ' . $this->semester;
    }
}
