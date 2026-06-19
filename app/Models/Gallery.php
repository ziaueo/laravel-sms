<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'school_id', 'title', 'description', 'type',
        'thumbnail', 'is_published', 'published_at', 'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function items()
    {
        return $this->hasMany(GalleryItem::class)->orderBy('order');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
