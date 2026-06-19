<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LearningObjective extends Model
{
    protected $fillable = ['subject_id','school_year_id','description','order'];
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolYear() { return $this->belongsTo(SchoolYear::class); }
}
