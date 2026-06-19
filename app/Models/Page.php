<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Page extends Model
{
    protected $fillable = ['school_id','title','slug','content','thumbnail','order','meta_title','meta_description','og_image','is_published','created_by'];
    protected $casts = ['is_published' => 'boolean'];
    public function school() { return $this->belongsTo(School::class); }
}
