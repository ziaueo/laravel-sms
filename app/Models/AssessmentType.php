<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentType extends Model
{
    protected $fillable = ['school_id', 'name', 'weight', 'order'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}
