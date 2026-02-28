<?php

namespace App\Http\Controllers;

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
        $q = Employee::query();

        return datatables()->of($q)
            ->addColumn('status', fn($e) => $e->is_active ? 'Active' : 'Inactive')
            ->addColumn('actions', function ($e) {
                return view('employees.partials.actions', compact('e'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required','string','max:255'],
            'email' => ['nullable','email','max:255', Rule::unique('employees','email')],
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
            'email' => ['nullable','email','max:255', Rule::unique('employees','email')->ignore($employee->id)],
            'phone' => ['nullable','string','max:50'],
            'job_title' => ['nullable','string','max:100'],
            'is_active' => ['required','boolean'],
        ]);

        $employee->update($validated);

        return response()->json(['ok' => true]);
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['ok' => true]);
    }
}