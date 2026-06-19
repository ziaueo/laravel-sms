<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\RoleConstant;

class UserSchool extends Model
{
    protected $fillable = ['user_id', 'school_id', 'role'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return RoleConstant::getLabel($this->role);
    }
}
