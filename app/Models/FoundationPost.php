<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FoundationPost extends Model
{
    protected $fillable = ['title','slug','excerpt','content','thumbnail','meta_title','meta_description','og_image','is_published','published_at','created_by'];
    protected $casts = ['is_published' => 'boolean', 'published_at' => 'datetime'];
}
