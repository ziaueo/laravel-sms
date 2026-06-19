<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GalleryItem extends Model
{
    protected $fillable = ['gallery_id','type','file_path','video_url','caption','order'];
    public function gallery() { return $this->belongsTo(Gallery::class); }
}
