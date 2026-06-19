<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'is_active',
        'must_change_password',
        'registered_by',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'last_login_at'        => 'datetime',
            'password'             => 'hashed',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────
    public function userSchools()
    {
        return $this->hasMany(UserSchool::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'user_schools')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function studentParents()
    {
        return $this->hasMany(StudentParent::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Accessors ──────────────────────────────────────
    public function getInitialsAttribute(): string
    {
        return initials($this->name);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset($this->avatar)
            : asset('public/assets/images/default/avatar.png');
    }
}
