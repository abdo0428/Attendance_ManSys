<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnboardingController extends Controller
{
    public function index()
    {
        $defaults = [
            'default_work_start' => Setting::getValue('default_work_start', '09:00'),
            'default_work_end' => Setting::getValue('default_work_end', '17:00'),
            'default_locale' => Setting::getValue('default_locale', config('app.locale')),
        ];

        $hasEmployees = Employee::count() > 0;

        return view('onboarding', compact('defaults', 'hasEmployees'));
    }

    public function store(Request $request)
    {
        $rules = [
            'employee_full_name' => ['nullable', 'string', 'max:255'],
            'employee_email' => ['nullable', 'email', 'max:255'],
            'employee_job_title' => ['nullable', 'string', 'max:100'],
            'default_work_start' => ['required', 'date_format:H:i'],
            'default_work_end' => ['required', 'date_format:H:i'],
            'default_locale' => ['required', 'in:en,ar,tr'],
        ];

        if (Employee::count() === 0) {
            $rules['employee_full_name'] = ['required', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $request) {
            if (Employee::count() === 0 && !empty($validated['employee_full_name'])) {
                Employee::create([
                    'full_name' => $validated['employee_full_name'],
                    'email' => $validated['employee_email'] ?? null,
                    'job_title' => $validated['employee_job_title'] ?? null,
                    'is_active' => true,
                ]);
            }

            Setting::setValue('default_work_start', $validated['default_work_start']);
            Setting::setValue('default_work_end', $validated['default_work_end']);
            Setting::setValue('default_locale', $validated['default_locale']);

            $user = $request->user();
            $user->onboarded_at = now();
            $user->save();
        });

        session(['locale' => $validated['default_locale']]);

        return redirect()->route('dashboard');
    }
}

