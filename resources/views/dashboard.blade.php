@extends('layouts.app2')

@section('content')
@php
  $totalMinutes = $stats['total_minutes_month'] ?? 0;
  $totalHours = sprintf('%02d:%02d', intdiv($totalMinutes,60), $totalMinutes%60);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-1">{{ __('app.dashboard_title') }}</h3>
    <div class="text-muted">{{ __('app.dashboard_subtitle') }}</div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">{{ __('app.card_active_employees') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['active_employees'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">{{ __('app.card_today_checkins') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['today_checkins'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">{{ __('app.card_today_checkouts') }}</div>
        <div class="fs-3 fw-bold">{{ $stats['today_checkouts'] ?? 0 }}</div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">{{ __('app.card_total_hours_month') }}</div>
        <div class="fs-3 fw-bold">{{ $totalHours }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">{{ __('app.recent_logs') }}</h5>
      <span class="text-muted">{{ __('app.last_10_records') }}</span>
    </div>

    @if($recentLogs->isEmpty())
      <div class="empty-state">
        <div class="mb-2">{{ __('app.empty_logs') }}</div>
        <a class="btn btn-sm btn-primary" href="{{ route('attendance.index') }}">{{ __('app.go_attendance') }}</a>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>{{ __('app.th_employee') }}</th>
              <th>{{ __('app.th_date') }}</th>
              <th>{{ __('app.th_checkin') }}</th>
              <th>{{ __('app.th_checkout') }}</th>
              <th>{{ __('app.th_worked') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentLogs as $log)
              <tr>
                <td>{{ $log->employee?->full_name ?? '-' }}</td>
                <td>{{ $log->work_date?->format('Y-m-d') }}</td>
                <td>{{ optional($log->check_in)->format('H:i') }}</td>
                <td>{{ optional($log->check_out)->format('H:i') }}</td>
                <td>{{ sprintf('%02d:%02d', intdiv($log->worked_minutes,60), $log->worked_minutes%60) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection

