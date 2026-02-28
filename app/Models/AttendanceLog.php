<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\CompanyScoped;

class AttendanceLog extends Model
{
    use CompanyScoped;

    protected $fillable = [
        'employee_id','work_date','check_in','check_out','worked_minutes','notes','company_id'
    ];

    protected $casts = [
        'work_date' => 'date',
        'check_in'  => 'datetime',
        'check_out' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
