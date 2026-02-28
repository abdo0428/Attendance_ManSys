<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

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
