<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'school_id', 'category_id', 'title', 'slug',
        'excerpt', 'content', 'thumbnail', 'is_published',
        'show_in_feed', 'meta_title', 'meta_description',
        'og_image', 'published_at', 'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_feed' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail
            ? asset($this->thumbnail)
            : asset('public/assets/images/default/no-image.png');
    }
}
