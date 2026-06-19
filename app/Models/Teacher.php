<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\GenderConstant;
use App\Constants\EmploymentConstant;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'school_id', 'position_id', 'nip',
        'full_name', 'gender', 'birth_place', 'birth_date',
        'religion', 'address', 'phone', 'email', 'photo',
        'join_date', 'employment_status', 'last_education',
        'major', 'documents', 'is_active',
    ];

    protected $casts = [
        'birth_date'  => 'date',
        'join_date'   => 'date',
        'documents'   => 'array',
        'is_active'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return GenderConstant::getLabel($this->gender);
    }

    public function getEmploymentLabelAttribute(): string
    {
        return $this->employment_status
            ? EmploymentConstant::getLabel($this->employment_status)
            : '-';
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset($this->photo)
            : asset('public/assets/images/default/avatar.png');
    }

    public function getInitialsAttribute(): string
    {
        return initials($this->full_name);
    }
}
