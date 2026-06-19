<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Faq extends Model
{
    protected $fillable = ['school_id','question','answer','order','is_published'];
    protected $casts = ['is_published' => 'boolean'];
    public function school() { return $this->belongsTo(School::class); }
}
