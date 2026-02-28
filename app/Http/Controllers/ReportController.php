<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthly()
    {
        $employees = Employee::orderBy('full_name')->get();
        return view('reports.monthly', compact('employees'));
    }

    public function monthlyData(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required','exists:employees,id'],
            'month' => ['required','date_format:Y-m'], // مثال: 2026-02
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $logs = AttendanceLog::where('employee_id', $validated['employee_id'])
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('work_date')
            ->get();

        $totalMinutes = $logs->sum('worked_minutes');
        $daysPresent  = $logs->filter(fn($l)=> $l->check_in || $l->check_out)->count();

        return response()->json([
            'ok' => true,
            'summary' => [
                'total_minutes' => $totalMinutes,
                'total_hours' => sprintf('%02d:%02d', intdiv($totalMinutes,60), $totalMinutes%60),
                'days_present' => $daysPresent,
                'days_in_month' => $start->daysInMonth,
            ],
            'rows' => $logs->map(function($l){
                return [
                    'work_date' => $l->work_date->format('Y-m-d'),
                    'check_in' => optional($l->check_in)->format('H:i'),
                    'check_out' => optional($l->check_out)->format('H:i'),
                    'worked' => sprintf('%02d:%02d', intdiv($l->worked_minutes,60), $l->worked_minutes%60),
                    'notes' => $l->notes,
                ];
            })->values()
        ]);
    }
}