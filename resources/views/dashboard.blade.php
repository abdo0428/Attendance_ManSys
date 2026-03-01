@extends('layouts.app2')

@section('title', __('app.dashboard_title'))

@section('content')
@php
  $totalMinutes = $stats['total_minutes_month'] ?? 0;
  $totalHours = sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);
  $companyName = \App\Models\Setting::getValue('company_name', config('app.name', 'Attendance Lite'));
@endphp

<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_overview') }}</div>
    <h1 class="page-title">{{ __('app.dashboard_title') }}</h1>
    <div class="page-subtitle">
      {{ $companyName }} · {{ now()->translatedFormat('l, d M Y') }}
    </div>
  </div>

  <div class="page-actions">
    @can('employees.create')
      <a class="btn btn-primary" href="{{ route('employees.index') }}">{{ __('app.btn_add_employee') }}</a>
    @endcan
    @can('attendance.checkin')
      <a class="btn btn-outline-secondary" href="{{ route('attendance.index') }}">{{ __('app.dashboard_mark_attendance') }}</a>
    @endcan
    @can('reports.view')
      <a class="btn btn-outline-secondary" href="{{ route('reports.monthly.export', ['month' => now()->format('Y-m')]) }}">{{ __('app.dashboard_export_monthly_csv') }}</a>
    @endcan
  </div>
</section>

<section class="kpi-grid">
  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.card_today_checkins') }}</div>
      <span class="metric-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 5v14M5 12h14"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['today_checkins'] ?? 0 }}</div>
    <div class="metric-caption">{{ __('app.dashboard_live_entries_today') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.card_today_checkouts') }}</div>
      <span class="metric-icon success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M12 19V5M19 12H5"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['today_checkouts'] ?? 0 }}</div>
    <div class="metric-caption">{{ __('app.dashboard_completed_day') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.dashboard_absent_today') }}</div>
      <span class="metric-icon danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M18 6 6 18M6 6l12 12"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['today_absent'] ?? 0 }}</div>
    <div class="metric-caption">{{ __('app.dashboard_absent_today_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.dashboard_total_employees') }}</div>
      <span class="metric-icon info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['active_employees'] ?? 0 }}</div>
    <div class="metric-caption">{{ __('app.dashboard_total_employees_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.dashboard_monthly_hours') }}</div>
      <span class="metric-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 7v5l3 3"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $totalHours }}</div>
    <div class="metric-caption">{{ __('app.dashboard_tracked_for_month', ['month' => now()->translatedFormat('F')]) }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.dashboard_pending_today') }}</div>
      <span class="metric-icon warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 8v5"></path>
          <circle cx="12" cy="16" r="1"></circle>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['today_pending'] ?? 0 }}</div>
    <div class="metric-caption">{{ __('app.dashboard_pending_today_caption') }}</div>
  </article>
</section>

<section class="dashboard-layout">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.dashboard_attendance_trend') }}</h2>
          <div class="panel-subtitle">{{ __('app.dashboard_last_7_days') }}</div>
        </div>
      </div>

      <div class="chart-shell">
        <canvas id="attendanceWeekChart" class="chart-canvas"></canvas>
      </div>
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.dashboard_today_status') }}</h2>
          <div class="panel-subtitle">{{ __('app.dashboard_today_status_caption') }}</div>
        </div>
      </div>

      <div class="chart-shell chart-shell-sm">
        <canvas id="todayStatusChart" class="chart-canvas"></canvas>
      </div>

      <div class="mini-stat-grid">
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_present') }}</div>
          <div class="mini-stat-value">{{ $todayStatusChart['present'] ?? 0 }}</div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_absent') }}</div>
          <div class="mini-stat-value">{{ $todayStatusChart['absent'] ?? 0 }}</div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_pending') }}</div>
          <div class="mini-stat-value">{{ $todayStatusChart['pending'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </article>
</section>

<section class="content-grid">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.recent_logs') }}</h2>
          <div class="panel-subtitle">{{ __('app.last_10_records') }}</div>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('attendance.index') }}">{{ __('app.dashboard_view_all') }}</a>
      </div>

      @if($recentLogs->isEmpty())
        <div class="empty-state">
          <span class="empty-state-icon">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
              <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
            </svg>
          </span>
          <div class="empty-state-title">{{ __('app.empty_logs') }}</div>
          <div class="empty-state-text">{{ __('app.dashboard_latest_activity_hint') }}</div>
        </div>
      @else
        <div class="table-wrapper">
          <table class="ui-table">
            <thead>
              <tr>
                <th>#</th>
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
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $log->employee?->full_name ?? '-' }}</td>
                  <td>{{ $log->work_date?->format('Y-m-d') }}</td>
                  <td>{{ optional($log->check_in)->format('H:i') ?: '-' }}</td>
                  <td>{{ optional($log->check_out)->format('H:i') ?: '-' }}</td>
                  <td>{{ sprintf('%02d:%02d', intdiv($log->worked_minutes, 60), $log->worked_minutes % 60) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.quick_actions') }}</h2>
          <div class="panel-subtitle">{{ __('app.dashboard_shortcuts_subtitle') }}</div>
        </div>
      </div>

      <div class="quick-actions-grid">
        @can('employees.create')
          <a class="quick-action-card" href="{{ route('employees.index') }}">
            <span class="metric-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 5v14M5 12h14"></path>
              </svg>
            </span>
            <div>
              <strong>{{ __('app.btn_add_employee') }}</strong>
              <span>{{ __('app.dashboard_add_employee_desc') }}</span>
            </div>
          </a>
        @endcan

        @can('attendance.checkin')
          <a class="quick-action-card" href="{{ route('attendance.index') }}">
            <span class="metric-icon warning">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="9"></circle>
                <path d="M12 8v5"></path>
                <circle cx="12" cy="16" r="1"></circle>
              </svg>
            </span>
            <div>
              <strong>{{ __('app.dashboard_mark_absent_title') }}</strong>
              <span>{{ __('app.dashboard_mark_absent_desc') }}</span>
            </div>
          </a>
        @endcan

        @can('reports.view')
          <a class="quick-action-card" href="{{ route('reports.monthly') }}">
            <span class="metric-icon info">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M4 19h16"></path>
                <path d="M7 16V9"></path>
                <path d="M12 16V5"></path>
                <path d="M17 16v-3"></path>
              </svg>
            </span>
            <div>
              <strong>{{ __('app.dashboard_monthly_reports_title') }}</strong>
              <span>{{ __('app.dashboard_monthly_reports_desc') }}</span>
            </div>
          </a>
        @endcan
      </div>
    </div>
  </article>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const weekChartContext = document.getElementById('attendanceWeekChart');
  const todayChartContext = document.getElementById('todayStatusChart');

  if (weekChartContext) {
    const weekData = @json($stats['week_chart'] ?? []);

    new Chart(weekChartContext, {
      type: 'bar',
      data: {
        labels: weekData.map((item) => item.label),
        datasets: [{
          data: weekData.map((item) => item.value),
          borderRadius: 10,
          backgroundColor: '#2563EB',
          hoverBackgroundColor: '#1D4ED8',
          maxBarThickness: 36,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false },
            border: { display: false }
          },
          y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: 'rgba(148, 163, 184, 0.18)' },
            border: { display: false }
          }
        }
      }
    });
  }

  if (todayChartContext) {
    const todayData = @json($todayStatusChart);

    new Chart(todayChartContext, {
      type: 'doughnut',
      data: {
        labels: @json([__('app.status_present'), __('app.status_absent'), __('app.status_pending')]),
        datasets: [{
          data: [todayData.present, todayData.absent, todayData.pending],
          backgroundColor: ['#16A34A', '#DC2626', '#F59E0B'],
          borderWidth: 0,
          hoverOffset: 4,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              usePointStyle: true,
              boxWidth: 10,
              padding: 16
            }
          }
        }
      }
    });
  }
</script>
@endpush
