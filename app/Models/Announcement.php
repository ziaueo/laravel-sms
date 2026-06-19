<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'school_id', 'title', 'content', 'target_roles',
        'target_classrooms', 'attachment', 'is_published',
        'is_public', 'show_in_feed', 'published_at', 'created_by',
    ];

    protected $casts = [
        'target_roles'      => 'array',
        'target_classrooms' => 'array',
        'is_published'      => 'boolean',
        'is_public'         => 'boolean',
        'show_in_feed'      => 'boolean',
        'published_at'      => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function comments()
    {
        return $this->hasMany(AnnouncementComment::class)
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'asc');
    }
}
