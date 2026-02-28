<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        return view('attendance.index', compact('employees'));
    }

    public function data(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $q = AttendanceLog::with('employee')
            ->whereDate('work_date', $date);

        return datatables()->of($q)
            ->addColumn('employee_name', fn($r) => $r->employee?->full_name)
            ->addColumn('worked_hours', function($r){
                $h = intdiv($r->worked_minutes, 60);
                $m = $r->worked_minutes % 60;
                return sprintf('%02d:%02d', $h, $m);
            })
            ->addColumn('actions', function($r){
                return view('attendance.partials.actions', compact('r'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required','exists:employees,id'],
            'work_date' => ['required','date'],
            'check_in_time' => ['required','date_format:H:i'],
        ]);

        $workDate = Carbon::parse($validated['work_date'])->toDateString();
        $checkIn  = Carbon::parse($workDate.' '.$validated['check_in_time'].':00');

        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'work_date' => $workDate],
            ['check_in' => $checkIn]
        );

        // إذا كان موجود مسبقاً وما عنده دخول
        if (!$log->check_in) {
            $log->check_in = $checkIn;
            $log->save();
        }

        return response()->json(['ok' => true]);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required','exists:employees,id'],
            'work_date' => ['required','date'],
            'check_out_time' => ['required','date_format:H:i'],
        ]);

        $workDate = Carbon::parse($validated['work_date'])->toDateString();
        $checkOut = Carbon::parse($workDate.' '.$validated['check_out_time'].':00');

        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'work_date' => $workDate]
        );

        $log->check_out = $checkOut;

        // حساب دقائق تقريبية إذا عنده check_in
        if ($log->check_in) {
            $mins = max(0, $log->check_in->diffInMinutes($checkOut, false));
            $log->worked_minutes = $mins;
        }

        $log->save();

        return response()->json(['ok' => true]);
    }

    public function show(AttendanceLog $attendanceLog)
    {
        $attendanceLog->load('employee');
        return response()->json(['ok' => true, 'log' => $attendanceLog]);
    }

    public function update(Request $request, AttendanceLog $attendanceLog)
    {
        $validated = $request->validate([
            'check_in' => ['nullable','date'],
            'check_out' => ['nullable','date','after_or_equal:check_in'],
            'notes' => ['nullable','string'],
        ]);

        $attendanceLog->fill($validated);

        if ($attendanceLog->check_in && $attendanceLog->check_out) {
            $attendanceLog->worked_minutes = $attendanceLog->check_in->diffInMinutes($attendanceLog->check_out);
        } else {
            $attendanceLog->worked_minutes = 0;
        }

        $attendanceLog->save();

        return response()->json(['ok' => true]);
    }

    public function destroy(AttendanceLog $attendanceLog)
    {
        $attendanceLog->delete();
        return response()->json(['ok' => true]);
    }
}