<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'school_type_id', 'name', 'slug', 'npsn',
        'address', 'phone', 'email', 'logo',
        'accreditation', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────
    public function schoolType()
    {
        return $this->belongsTo(SchoolType::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_schools')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function schoolYears()
    {
        return $this->hasMany(SchoolYear::class);
    }

    public function activeSchoolYear()
    {
        return $this->hasOne(SchoolYear::class)->where('is_active', true);
    }

    public function gradeLevels()
    {
        return $this->hasMany(GradeLevel::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function profile()
    {
        return $this->hasOne(SchoolProfile::class);
    }

    public function banners()
    {
        return $this->hasMany(Banner::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function activities()
    {
        return $this->hasMany(SchoolActivity::class);
    }

    public function ppdbPeriods()
    {
        return $this->hasMany(PpdbPeriod::class);
    }

    // ── Accessors ──────────────────────────────────────
    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset($this->logo)
            : asset('public/assets/images/default/school-default.png');
    }
}
