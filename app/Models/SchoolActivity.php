<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolActivity extends Model
{
    protected $fillable = [
        'school_id', 'category_id', 'title', 'content',
        'media', 'activity_date', 'is_published', 'show_in_feed',
        'meta_title', 'meta_description', 'og_image',
        'published_at', 'created_by',
    ];

    protected $casts = [
        'media'         => 'array',
        'activity_date' => 'date',
        'is_published'  => 'boolean',
        'show_in_feed'  => 'boolean',
        'published_at'  => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(ActivityCategory::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
