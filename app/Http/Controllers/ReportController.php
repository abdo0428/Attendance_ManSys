<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Setting;
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
            'month' => ['required','date_format:Y-m'], // ????????: 2026-02
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $defaultStart = Setting::getValue('default_work_start', '09:00');
        $graceMinutes = (int) Setting::getValue('grace_minutes', '10');

        $logs = AttendanceLog::select(['id','work_date','check_in','check_out','worked_minutes','notes'])
            ->where('employee_id', $validated['employee_id'])
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
            'rows' => $logs->map(function($l) use ($defaultStart, $graceMinutes){
                $workDate = $l->work_date->format('Y-m-d');
                $startLimit = Carbon::parse($workDate.' '.$defaultStart.':00')->addMinutes($graceMinutes);
                $late = $l->check_in && $l->check_in->gt($startLimit);
                $overtime = $l->worked_minutes > 480;
                $missingCheckout = $l->check_in && !$l->check_out;

                return [
                    'work_date' => $workDate,
                    'check_in' => optional($l->check_in)->format('H:i'),
                    'check_out' => optional($l->check_out)->format('H:i'),
                    'worked' => sprintf('%02d:%02d', intdiv($l->worked_minutes,60), $l->worked_minutes%60),
                    'notes' => $l->notes,
                    'late' => $late,
                    'overtime' => $overtime,
                    'missing_checkout' => $missingCheckout,
                ];
            })->values()
        ]);
    }

    public function exportCsv(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required','exists:employees,id'],
            'month' => ['required','date_format:Y-m'],
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $logs = AttendanceLog::select(['work_date','check_in','check_out','worked_minutes','notes'])
            ->where('employee_id', $validated['employee_id'])
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('work_date')
            ->get();

        $filename = 'monthly_report_'.$validated['month'].'.csv';

        return response()->streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Check-in', 'Check-out', 'Worked', 'Notes']);

            foreach ($logs as $l) {
                $worked = sprintf('%02d:%02d', intdiv($l->worked_minutes,60), $l->worked_minutes%60);
                fputcsv($out, [
                    $l->work_date->format('Y-m-d'),
                    optional($l->check_in)->format('H:i'),
                    optional($l->check_out)->format('H:i'),
                    $worked,
                    $l->notes,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
