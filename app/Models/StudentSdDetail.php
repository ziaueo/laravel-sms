<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSdDetail extends Model
{
    protected $fillable = [
        'student_id', 'special_needs', 'special_needs_description',
        'health_condition', 'allergy', 'distance_to_school',
        'transportation', 'previous_school',
    ];

    protected $casts = [
        'special_needs' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
