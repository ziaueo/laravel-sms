<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Constants\ActivityConstant;
class ActivityLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','school_id','action','module','reference_id','description','ip_address','user_agent','created_at'];
    protected $casts = ['created_at' => 'datetime'];
    public function user() { return $this->belongsTo(User::class); }
    public function getActionLabelAttribute(): string { return ActivityConstant::getLabel($this->action); }
}
