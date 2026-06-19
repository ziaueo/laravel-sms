<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentLearningObjective extends Model
{
    protected $fillable = ['student_id','learning_objective_id','school_year_id','predicate','description','recorded_by'];
    public function student() { return $this->belongsTo(Student::class); }
    public function learningObjective() { return $this->belongsTo(LearningObjective::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
