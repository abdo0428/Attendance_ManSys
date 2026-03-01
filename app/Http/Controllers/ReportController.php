<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'employee_id' => ['nullable', Rule::exists('employees', 'id')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'month' => ['required','date_format:Y-m'],
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();
        $employeeId = $validated['employee_id'] ?? null;

        $employeeQuery = Employee::select(['id', 'full_name'])->orderBy('full_name');
        if ($employeeId) {
            $employeeQuery->whereKey($employeeId);
        }
        $employees = $employeeQuery->get();

        $logQuery = AttendanceLog::select([
            'id',
            'employee_id',
            'work_date',
            'check_in',
            'check_out',
            'worked_minutes',
            'notes',
        ])
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()]);

        if ($employeeId) {
            $logQuery->where('employee_id', $employeeId);
        }

        $logsByEmployee = $logQuery
            ->orderBy('work_date')
            ->get()
            ->groupBy('employee_id');

        $rows = $employees->map(function ($employee) use ($logsByEmployee) {
            $employeeLogs = ($logsByEmployee->get($employee->id) ?? collect())->values();
            $totalMinutes = $employeeLogs->sum('worked_minutes');
            $workDays = $employeeLogs->filter(fn ($log) => $log->check_in || $log->check_out)->count();
            $absences = $employeeLogs->filter(fn ($log) => !$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0)->count();
            $missingPunches = $employeeLogs->filter(fn ($log) => $log->check_in && !$log->check_out)->count();
            $overtime = $employeeLogs->filter(fn ($log) => $log->worked_minutes > 480)->count();

            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name,
                'work_days' => $workDays,
                'total_minutes' => $totalMinutes,
                'total_hours' => sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60),
                'absences' => $absences,
                'missing_punches' => $missingPunches,
                'overtime' => $overtime,
                'details' => $employeeLogs->map(function ($log) {
                    $status = $this->reportStatus($log);

                    return [
                        'work_date' => $log->work_date->format('Y-m-d'),
                        'check_in' => optional($log->check_in)->format('H:i'),
                        'check_out' => optional($log->check_out)->format('H:i'),
                        'worked' => sprintf('%02d:%02d', intdiv($log->worked_minutes, 60), $log->worked_minutes % 60),
                        'notes' => $this->displayNote($log->notes),
                        'status' => $status['label'],
                        'status_tone' => $status['tone'],
                    ];
                })->values(),
            ];
        })
            ->filter(fn ($row) => $employeeId || $row['work_days'] || $row['absences'] || $row['missing_punches'])
            ->values();

        $totalMinutes = $rows->sum('total_minutes');
        $employeeCount = max($rows->count(), 1);

        return response()->json([
            'ok' => true,
            'summary' => [
                'total_hours' => sprintf('%02d:%02d', intdiv($totalMinutes,60), $totalMinutes%60),
                'average_hours' => sprintf('%02d:%02d', intdiv((int) floor($totalMinutes / $employeeCount), 60), ((int) floor($totalMinutes / $employeeCount)) % 60),
                'absence_count' => $rows->sum('absences'),
                'missing_punches' => $rows->sum('missing_punches'),
                'days_in_month' => $start->daysInMonth,
            ],
            'rows' => $rows,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', Rule::exists('employees', 'id')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'month' => ['required','date_format:Y-m'],
        ]);

        $start = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $end   = (clone $start)->endOfMonth();
        $employeeId = $validated['employee_id'] ?? null;

        $filename = 'monthly_report_'.$validated['month'].'.csv';

        if ($employeeId) {
            $logs = AttendanceLog::select(['work_date','check_in','check_out','worked_minutes','notes'])
                ->where('employee_id', $employeeId)
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('work_date')
                ->get();

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

        $employees = Employee::select(['id', 'full_name'])->orderBy('full_name')->get();
        $logsByEmployee = AttendanceLog::select(['employee_id', 'check_in', 'check_out', 'worked_minutes', 'notes'])
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('employee_id');

        return response()->streamDownload(function () use ($employees, $logsByEmployee) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Employee', 'Work days', 'Total hours', 'Absences', 'Missing punches', 'Overtime days']);

            foreach ($employees as $employee) {
                $employeeLogs = ($logsByEmployee->get($employee->id) ?? collect())->values();
                $totalMinutes = $employeeLogs->sum('worked_minutes');
                $workDays = $employeeLogs->filter(fn ($log) => $log->check_in || $log->check_out)->count();
                $absences = $employeeLogs->filter(fn ($log) => !$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0)->count();
                $missingPunches = $employeeLogs->filter(fn ($log) => $log->check_in && !$log->check_out)->count();
                $overtime = $employeeLogs->filter(fn ($log) => $log->worked_minutes > 480)->count();

                if (!$workDays && !$absences && !$missingPunches) {
                    continue;
                }

                fputcsv($out, [
                    $employee->full_name,
                    $workDays,
                    sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60),
                    $absences,
                    $missingPunches,
                    $overtime,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function reportStatus(AttendanceLog $log): array
    {
        if (!$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0) {
            return ['label' => __('app.status_absent'), 'tone' => 'danger'];
        }

        if ($log->check_in && !$log->check_out) {
            return ['label' => __('app.status_missing_checkout'), 'tone' => 'warning'];
        }

        if ($log->check_in || $log->check_out) {
            return ['label' => __('app.status_present'), 'tone' => 'success'];
        }

        return ['label' => __('app.status_pending'), 'tone' => 'neutral'];
    }

    private function displayNote(?string $note): string
    {
        if ($note === null || $note === '') {
            return '-';
        }

        if (strcasecmp($note, 'Absent') === 0) {
            return __('app.status_absent');
        }

        return $note;
    }
}
