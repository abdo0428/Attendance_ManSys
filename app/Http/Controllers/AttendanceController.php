<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index()
    {
        $employees = Employee::where('is_active', true)->orderBy('full_name')->get();
        $defaults = [
            'default_work_start' => Setting::getValue('default_work_start', '09:00'),
            'default_work_end' => Setting::getValue('default_work_end', '17:00'),
        ];
        $todayLogs = AttendanceLog::whereDate('work_date', now()->toDateString())->get([
            'employee_id',
            'check_in',
            'check_out',
            'notes',
        ]);

        $present = $todayLogs
            ->filter(fn ($log) => $log->check_in || $log->check_out)
            ->pluck('employee_id')
            ->unique()
            ->count();

        $absent = $todayLogs
            ->filter(fn ($log) => !$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0)
            ->pluck('employee_id')
            ->unique()
            ->count();

        $summary = [
            'present' => $present,
            'absent' => $absent,
            'pending' => max($employees->count() - $present - $absent, 0),
        ];

        return view('attendance.index', [
            'employees' => $employees,
            'defaults' => $defaults,
            'summary' => $summary,
            'initialSearch' => (string) request('search', ''),
        ]);
    }

    public function data(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $employeeId = $request->get('employee_id');

        $q = AttendanceLog::query()
            ->select(['id','employee_id','work_date','check_in','check_out','worked_minutes','notes'])
            ->with(['employee:id,full_name'])
            ->whereDate('work_date', $date);

        if ($employeeId) {
            $q->where('employee_id', $employeeId);
        }

        $recordsTotal = (clone $q)->count();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $q->where(function ($sub) use ($search) {
                $sub->where('work_date', 'like', "%{$search}%")
                    ->orWhere('check_in', 'like', "%{$search}%")
                    ->orWhere('check_out', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('employee', fn($e) => $e->where('full_name', 'like', "%{$search}%"));
            });
        }

        $recordsFiltered = (clone $q)->count();

        $orderIdx = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $columns = $request->input('columns', []);
        $orderColumn = $columns[$orderIdx]['data'] ?? 'id';

        $columnMap = [
            'id' => 'id',
            'employee_name' => 'employee_id',
            'work_date' => 'work_date',
            'check_in' => 'check_in',
            'check_out' => 'check_out',
            'worked_hours' => 'worked_minutes',
        ];

        $q->orderBy($columnMap[$orderColumn] ?? 'id', $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $q->skip($start)->take($length);
        }

        $rows = $q->get();

        $data = $rows->map(function ($r) {
            $h = intdiv($r->worked_minutes, 60);
            $m = $r->worked_minutes % 60;

            return [
                'id' => $r->id,
                'employee_name' => $r->employee?->full_name,
                'work_date' => $r->work_date?->format('Y-m-d'),
                'check_in' => $r->check_in?->format('H:i'),
                'check_out' => $r->check_out?->format('H:i'),
                'worked_hours' => sprintf('%02d:%02d', $h, $m),
                'status_badge' => $this->attendanceStatusBadge($r),
                'notes' => $this->displayNote($r->notes),
                'actions' => view('attendance.partials.actions', compact('r'))->render(),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'work_date' => ['required','date'],
            'check_in_time' => ['required','date_format:H:i'],
        ]);

        $workDate = Carbon::parse($validated['work_date'])->toDateString();
        $checkIn  = Carbon::parse($workDate.' '.$validated['check_in_time'].':00');

        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'work_date' => $workDate],
            ['check_in' => $checkIn]
        );

        if ($log->check_out && $checkIn->gt($log->check_out)) {
            return response()->json([
                'errors' => ['check_in_time' => [__('app.err_checkin_before_checkout')]]
            ], 422);
        }

        if (!$log->check_in) {
            $log->check_in = $checkIn;
            $log->save();
        }

        AuditLog::record('attendance.checkin', $log, ['work_date' => $workDate]);

        return response()->json(['ok' => true]);
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'work_date' => ['required','date'],
            'check_out_time' => ['required','date_format:H:i'],
        ]);

        $workDate = Carbon::parse($validated['work_date'])->toDateString();
        $checkOut = Carbon::parse($workDate.' '.$validated['check_out_time'].':00');

        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'work_date' => $workDate]
        );

        if ($log->check_in && $checkOut->lt($log->check_in)) {
            return response()->json([
                'errors' => ['check_out_time' => [__('app.err_checkout_after_checkin')]]
            ], 422);
        }

        $log->check_out = $checkOut;

        if ($log->check_in) {
            $mins = max(0, $log->check_in->diffInMinutes($checkOut, false));
            $log->worked_minutes = $mins;
        }

        $log->save();

        AuditLog::record('attendance.checkout', $log, ['work_date' => $workDate]);

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

        AuditLog::record('attendance.updated', $attendanceLog);

        return response()->json(['ok' => true]);
    }

    public function destroy(AttendanceLog $attendanceLog)
    {
        $attendanceLog->delete();
        AuditLog::record('attendance.deleted', $attendanceLog);
        return response()->json(['ok' => true]);
    }

    public function markAbsent(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where(fn ($q) => $q->where('company_id', $request->user()->company_id))],
            'work_date' => ['required','date'],
        ]);

        $workDate = Carbon::parse($validated['work_date'])->toDateString();

        $log = AttendanceLog::firstOrCreate(
            ['employee_id' => $validated['employee_id'], 'work_date' => $workDate]
        );

        if ($log->check_in || $log->check_out) {
            return response()->json([
                'errors' => ['work_date' => [__('app.err_absent_conflict')]]
            ], 422);
        }

        $log->notes = 'Absent';
        $log->worked_minutes = 0;
        $log->save();

        AuditLog::record('attendance.absent', $log, ['work_date' => $workDate]);

        return response()->json(['ok' => true]);
    }

    private function attendanceStatusBadge(AttendanceLog $log): string
    {
        if (!$log->check_in && !$log->check_out && strcasecmp((string) $log->notes, 'Absent') === 0) {
            return $this->badge('danger', __('app.status_absent'));
        }

        if ($log->check_in && $log->check_out) {
            return $this->badge('success', __('app.status_completed'));
        }

        if ($log->check_in) {
            return $this->badge('warning', __('app.status_missing_checkout'));
        }

        return $this->badge('neutral', __('app.status_pending'));
    }

    private function badge(string $tone, string $label): string
    {
        return sprintf(
            '<span class="ui-badge badge-%s"><span class="badge-dot"></span>%s</span>',
            $tone,
            e($label)
        );
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
