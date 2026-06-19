<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Testimonial extends Model
{
    protected $fillable = ['school_id','name','role','content','photo','is_published'];
    protected $casts = ['is_published' => 'boolean'];
    public function school() { return $this->belongsTo(School::class); }
}
