<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\AttendanceConstant;

class StudentSmpSmaDetail extends Model
{
    protected $table = 'student_smp_sma_details';

    protected $fillable = [
        'student_id', 'height', 'weight', 'health_condition',
        'allergy', 'hobby', 'extracurricular_interest',
        'distance_to_school', 'transportation', 'previous_school',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
