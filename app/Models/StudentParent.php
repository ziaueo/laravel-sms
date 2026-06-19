<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\GenderConstant;

class StudentParent extends Model
{
    protected $fillable = [
        'student_id', 'user_id', 'full_name', 'relation',
        'gender', 'birth_date', 'religion', 'education',
        'job', 'phone', 'email', 'address', 'is_primary',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_primary' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender ? GenderConstant::getLabel($this->gender) : '-';
    }
}
