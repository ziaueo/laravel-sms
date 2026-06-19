<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolType extends Model
{
    protected $fillable = ['name', 'order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function schools()
    {
        return $this->hasMany(School::class);
    }
}
