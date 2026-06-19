<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectKkm extends Model
{
    protected $table = 'subject_kkm';

    protected $fillable = ['subject_id', 'school_year_id', 'kkm_score'];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
