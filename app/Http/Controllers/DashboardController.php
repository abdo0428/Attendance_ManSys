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
        $companyKey = auth()->user()?->company_id ?? 'global';

        $stats = Cache::remember("dashboard.stats.{$companyKey}", 60, function () use ($today, $monthStart, $monthEnd) {
            $activeEmployees = Employee::where('is_active', true)->count();

            $todayCheckIns = AttendanceLog::whereDate('work_date', $today)
                ->whereNotNull('check_in')
                ->count();

            $todayCheckOuts = AttendanceLog::whereDate('work_date', $today)
                ->whereNotNull('check_out')
                ->count();

            $totalMinutes = AttendanceLog::whereBetween('work_date', [$monthStart, $monthEnd])
                ->sum('worked_minutes');

            return [
                'active_employees' => $activeEmployees,
                'today_checkins' => $todayCheckIns,
                'today_checkouts' => $todayCheckOuts,
                'total_minutes_month' => $totalMinutes,
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
        ]);
    }
}
