<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Constants\SubstitutionConstant;
class ScheduleSubstitution extends Model
{
    protected $fillable = ['schedule_id','date','type','original_teacher_id','substitute_teacher_id','original_subject_id','substitute_subject_id','reason','notes','created_by'];
    protected $casts = ['date' => 'date'];
    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function originalTeacher() { return $this->belongsTo(Teacher::class, 'original_teacher_id'); }
    public function substituteTeacher() { return $this->belongsTo(Teacher::class, 'substitute_teacher_id'); }
    public function getTypeLabelAttribute(): string { return SubstitutionConstant::getLabel($this->type); }
}
