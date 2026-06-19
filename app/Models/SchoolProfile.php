<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'school_id', 'tagline', 'description', 'vision',
        'mission', 'history', 'principal_name', 'principal_photo',
        'founded_year', 'facebook_url', 'instagram_url',
        'youtube_url', 'tiktok_url', 'maps_embed',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
