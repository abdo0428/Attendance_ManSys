<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'full_name','email','phone','job_title','is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }
}