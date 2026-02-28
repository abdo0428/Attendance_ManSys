<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index');
    }

    public function data(Request $request)
    {
        $q = Employee::query()
            ->select(['id','full_name','email','phone','job_title','is_active']);

        $status = $request->get('status');
        if ($status === 'active') {
            $q->where('is_active', true);
        }
        if ($status === 'inactive') {
            $q->where('is_active', false);
        }
        $recordsTotal = (clone $q)->count();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $q->where(function ($sub) use ($search) {
                $sub->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $q)->count();

        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $columns = $request->input('columns', []);
        $orderColumn = $columns[$orderIdx]['data'] ?? 'id';

        $columnMap = [
            'id' => 'id',
            'full_name' => 'full_name',
            'email' => 'email',
            'phone' => 'phone',
            'job_title' => 'job_title',
            'status' => 'is_active',
        ];

        $q->orderBy($columnMap[$orderColumn] ?? 'id', $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $q->skip($start)->take($length);
        }

        $rows = $q->get();

        $data = $rows->map(function ($e) {
            return [
                'id' => $e->id,
                'full_name' => $e->full_name,
                'email' => $e->email,
                'phone' => $e->phone,
                'job_title' => $e->job_title,
                'status' => $e->is_active ? __('app.status_active') : __('app.status_inactive'),
                'actions' => view('employees.partials.actions', compact('e'))->render(),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255', Rule::unique('employees','email')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'phone' => ['nullable','string','max:50'],
            'job_title' => ['nullable','string','max:100'],
            'is_active' => ['required','boolean'],
        ]);

        $emp = Employee::create($validated);

        return response()->json(['ok' => true, 'employee' => $emp]);
    }

    public function show(Employee $employee)
    {
        return response()->json(['ok' => true, 'employee' => $employee]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'full_name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255', Rule::unique('employees','email')->ignore($employee->id)->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'phone' => ['nullable','string','max:50'],
            'job_title' => ['nullable','string','max:100'],
            'is_active' => ['required','boolean'],
        ]);

        $employee->update($validated);

        return response()->json(['ok' => true]);
    }

    public function destroy(Employee $employee)
    {
        $employee->is_active = false;
        $employee->save();
        $employee->delete();
        AuditLog::record('employee.deleted', $employee);
        return response()->json(['ok' => true]);
    }
}
