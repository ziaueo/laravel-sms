<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\MutationConstant;

class StudentMutation extends Model
{
    protected $fillable = [
        'student_id', 'school_id', 'type', 'mutation_date',
        'reason', 'origin_school_name', 'origin_school_address',
        'origin_class', 'destination_school_name',
        'destination_school_address', 'documents', 'status',
        'notes', 'processed_by', 'processed_at',
    ];

    protected $casts = [
        'mutation_date' => 'date',
        'processed_at'  => 'datetime',
        'documents'     => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return MutationConstant::getTypeLabel($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return MutationConstant::getStatusLabel($this->status);
    }
}
