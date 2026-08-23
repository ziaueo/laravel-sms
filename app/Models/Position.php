<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\PositionConstant;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'order'];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return PositionConstant::getLabel($this->type);
    }
}
