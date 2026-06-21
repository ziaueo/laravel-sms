<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\GenderConstant;
use App\Constants\StudentStatusConstant;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'school_id', 'nisn', 'nis',
        'full_name', 'gender', 'birth_place', 'birth_date',
        'religion', 'address', 'phone', 'photo', 'blood_type',
        'entry_year', 'entry_class_id', 'status',
        'exit_date', 'exit_reason', 'documents',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'exit_date'  => 'date',
        'documents'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function parents()
    {
        return $this->hasMany(StudentParent::class);
    }

    public function primaryParent()
    {
        return $this->hasOne(StudentParent::class)->where('is_primary', true);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'student_classrooms')
                    ->withPivot('is_active', 'school_year_id')
                    ->withTimestamps();
    }

    public function activeClassroom()
    {
        return $this->hasOne(StudentClassroom::class)->where('is_active', true);
    }

    public function paudDetail()
    {
        return $this->hasOne(StudentPaudDetail::class);
    }

    public function sdDetail()
    {
        return $this->hasOne(StudentSdDetail::class);
    }

    public function mutations()
    {
        return $this->hasMany(StudentMutation::class);
    }

    public function achievements()
    {
        return $this->hasMany(StudentAchievement::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function reportCards()
    {
        return $this->hasMany(ReportCard::class);
    }

    public function getGenderLabelAttribute(): string
    {
        return GenderConstant::getLabel($this->gender);
    }

    public function getStatusLabelAttribute(): string
    {
        return StudentStatusConstant::getLabel($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return student_status_badge($this->status);
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

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function smpSmaDetail()
    {
        return $this->hasOne(StudentSmpSmaDetail::class);
    }
}
