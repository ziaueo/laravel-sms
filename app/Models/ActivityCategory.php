<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityCategory extends Model
{
    protected $fillable = ['school_id','name','icon','color','order'];
    public function school() { return $this->belongsTo(School::class); }
    public function activities() { return $this->hasMany(SchoolActivity::class, 'category_id'); }
}
