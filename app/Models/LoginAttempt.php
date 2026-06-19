<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoginAttempt extends Model
{
    public $timestamps = false;
    protected $fillable = ['email','ip_address','is_success','attempted_at'];
    protected $casts = ['is_success' => 'boolean', 'attempted_at' => 'datetime'];
}
