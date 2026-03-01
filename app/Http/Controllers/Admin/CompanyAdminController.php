<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\Scopes\CompanyScope;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyAdminController extends Controller
{
    public function index()
    {
        $companies = Company::with('owner')
            ->withCount(['users', 'employees'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $users = User::with('roles')
            ->where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $employeesQuery = Employee::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id);

        $employees = (clone $employeesQuery)
            ->orderBy('full_name')
            ->limit(10)
            ->get();

        $settings = Setting::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->orderBy('key')
            ->get();

        $auditLogs = AuditLog::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $workedMinutes = AttendanceLog::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->whereBetween('work_date', [$monthStart, $monthEnd])
            ->sum('worked_minutes');

        $stats = [
            'users' => $users->count(),
            'employees' => (clone $employeesQuery)->count(),
            'logs' => AttendanceLog::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', $company->id)
                ->count(),
            'hours_month' => sprintf('%02d:%02d', intdiv($workedMinutes, 60), $workedMinutes % 60),
        ];

        return view('admin.companies.show', compact(
            'company',
            'users',
            'employees',
            'settings',
            'auditLogs',
            'stats'
        ));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company->name = $validated['name'];
        $company->save();

        Setting::setValue('company_name', $company->name, $company->id);

        return redirect()
            ->route('admin.companies.show', $company)
            ->with('toast', ['type' => 'success', 'message' => __('app.toast_saved')]);
    }
}
