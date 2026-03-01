<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $weekStart = now()->copy()->subDays(6)->toDateString();
        $companyKey = auth()->user()?->company_id ?? 'global';

        $stats = Cache::remember("dashboard.stats.{$companyKey}", 60, function () use ($today, $monthStart, $monthEnd, $weekStart) {
            $activeEmployees = Employee::where('is_active', true)->count();

            $todayLogs = AttendanceLog::whereDate('work_date', $today)->get([
                'employee_id',
                'check_in',
                'check_out',
                'notes',
            ]);

            $todayCheckIns = $todayLogs->whereNotNull('check_in')->count();
            $todayCheckOuts = $todayLogs->whereNotNull('check_out')->count();
            $todayPresent = $todayLogs
                ->filter(fn ($log) => $log->check_in || $log->check_out)
                ->pluck('employee_id')
                ->unique()
                ->count();
            $todayAbsent = $todayLogs
                ->filter(fn ($log) => !$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0)
                ->pluck('employee_id')
                ->unique()
                ->count();

            $totalMinutes = AttendanceLog::whereBetween('work_date', [$monthStart, $monthEnd])
                ->sum('worked_minutes');

            $weekly = AttendanceLog::query()
                ->selectRaw('work_date, COUNT(DISTINCT employee_id) as total')
                ->whereBetween('work_date', [$weekStart, $today])
                ->where(function ($query) {
                    $query->whereNotNull('check_in')->orWhereNotNull('check_out');
                })
                ->groupBy('work_date')
                ->pluck('total', 'work_date');

            $weekChart = collect(range(6, 0))
                ->map(function ($offset) use ($weekly) {
                    $date = now()->copy()->subDays($offset);
                    $key = $date->toDateString();

                    return [
                        'label' => $date->translatedFormat('D'),
                        'value' => (int) ($weekly[$key] ?? 0),
                    ];
                })
                ->values();

            return [
                'active_employees' => $activeEmployees,
                'today_checkins' => $todayCheckIns,
                'today_checkouts' => $todayCheckOuts,
                'today_present' => $todayPresent,
                'today_absent' => $todayAbsent,
                'today_pending' => max($activeEmployees - $todayPresent - $todayAbsent, 0),
                'total_minutes_month' => $totalMinutes,
                'week_chart' => $weekChart,
            ];
        });

        $recentLogs = AttendanceLog::with('employee')
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'todayStatusChart' => [
                'present' => $stats['today_present'] ?? 0,
                'absent' => $stats['today_absent'] ?? 0,
                'pending' => $stats['today_pending'] ?? 0,
            ],
        ]);
    }
}
