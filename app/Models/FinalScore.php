<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FinalScore extends Model
{
    protected $fillable = ['student_id','subject_id','classroom_id','school_year_id','final_score','grade','predicate'];
    public function student() { return $this->belongsTo(Student::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolYear() { return $this->belongsTo(SchoolYear::class); }
}
