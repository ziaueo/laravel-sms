<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model
{
    use HasFactory;

    protected $fillable = ['school_id','title','subtitle','image','button_text','button_url','order','is_published'];
    protected $casts = ['is_published' => 'boolean'];
    public function school() { return $this->belongsTo(School::class); }
}
