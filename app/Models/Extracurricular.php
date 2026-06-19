<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Extracurricular extends Model
{
    protected $fillable = ['school_id','name','description','is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function school() { return $this->belongsTo(School::class); }
}
