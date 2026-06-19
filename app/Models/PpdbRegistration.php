<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\PpdbConstant;
use App\Constants\GenderConstant;

class PpdbRegistration extends Model
{
    protected $fillable = [
        'school_id', 'school_year_id', 'ppdb_period_id',
        'registration_number', 'full_name', 'gender',
        'birth_place', 'birth_date', 'religion', 'address',
        'previous_school', 'documents', 'parent_name',
        'parent_relation', 'parent_phone', 'parent_email',
        'parent_job', 'parent_address', 'status',
        'notes', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'birth_date'  => 'date',
        'reviewed_at' => 'datetime',
        'documents'   => 'array',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function ppdbPeriod()
    {
        return $this->belongsTo(PpdbPeriod::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return PpdbConstant::getLabel($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return ppdb_status_badge($this->status);
    }

    public function getGenderLabelAttribute(): string
    {
        return GenderConstant::getLabel($this->gender);
    }
}
