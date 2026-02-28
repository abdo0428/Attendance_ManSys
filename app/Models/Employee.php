<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\CompanyScoped;

class Employee extends Model
{
    use SoftDeletes;
    use CompanyScoped;

    protected $fillable = [
        'full_name','email','phone','job_title','is_active','company_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
