<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\NotificationConstant;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message',
        'reference_type', 'reference_id', 'is_read', 'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return NotificationConstant::getLabel($this->type);
    }
}
