<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPaudDetail extends Model
{
    protected $fillable = [
        'student_id', 'height', 'weight', 'special_needs',
        'special_needs_description', 'health_condition', 'allergy',
        'pediatrician_name', 'vaccine_status', 'is_toilet_trained',
        'language_at_home',
    ];

    protected $casts = [
        'special_needs'     => 'boolean',
        'is_toilet_trained' => 'boolean',
        'vaccine_status'    => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
