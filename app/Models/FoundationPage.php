<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FoundationPage extends Model
{
    protected $fillable = ['title','slug','content','thumbnail','order','meta_title','meta_description','og_image','is_published','created_by'];
    protected $casts = ['is_published' => 'boolean'];
}
