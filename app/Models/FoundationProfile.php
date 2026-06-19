<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FoundationProfile extends Model
{
    protected $fillable = ['name','tagline','description','logo','address','phone','email','website','facebook_url','instagram_url','youtube_url','tiktok_url','founded_year'];
}
