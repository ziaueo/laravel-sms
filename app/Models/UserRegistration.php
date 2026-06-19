<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Constants\RegistrationConstant;
class UserRegistration extends Model
{
    protected $fillable = ['name','email','password','role','school_id','data','status','email_verified_at','reviewed_by','reviewed_at','notes'];
    protected $hidden = ['password'];
    protected $casts = ['data' => 'array', 'email_verified_at' => 'datetime', 'reviewed_at' => 'datetime'];
    public function school() { return $this->belongsTo(School::class); }
    public function reviewedBy() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function getStatusLabelAttribute(): string { return RegistrationConstant::getLabel($this->status); }
}
