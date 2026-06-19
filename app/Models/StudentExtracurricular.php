<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentExtracurricular extends Model
{
    protected $fillable = ['student_id','extracurricular_id','school_year_id','predicate','description'];
    public function student() { return $this->belongsTo(Student::class); }
    public function extracurricular() { return $this->belongsTo(Extracurricular::class); }
    public function schoolYear() { return $this->belongsTo(SchoolYear::class); }
}
