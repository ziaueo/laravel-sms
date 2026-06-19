<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AttitudeScore extends Model
{
    protected $fillable = ['student_id','classroom_id','school_year_id','type','score','predicate','description','recorded_by'];
    public function student() { return $this->belongsTo(Student::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
