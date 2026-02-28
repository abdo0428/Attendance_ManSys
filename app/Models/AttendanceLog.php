<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'employee_id','work_date','check_in','check_out','worked_minutes','notes'
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}