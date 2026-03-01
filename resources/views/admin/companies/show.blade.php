@extends('layouts.app2')

@section('title', $company->name)

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_super_admin') }}</div>
    <h1 class="page-title">{{ $company->name }}</h1>
    <div class="page-subtitle">{{ __('app.company_show_subtitle') }}</div>
  </div>

  <div class="page-actions">
    <a class="btn btn-outline-secondary" href="{{ route('admin.companies.index') }}">{{ __('app.btn_back') }}</a>
  </div>
</section>

<section class="company-summary-grid">
  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.stat_users') }}</div>
      <span class="metric-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['users'] }}</div>
    <div class="metric-caption">{{ __('app.company_users_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.stat_employees') }}</div>
      <span class="metric-icon info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['employees'] }}</div>
    <div class="metric-caption">{{ __('app.company_employees_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.stat_logs') }}</div>
      <span class="metric-icon warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['logs'] }}</div>
    <div class="metric-caption">{{ __('app.company_logs_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.stat_hours_month') }}</div>
      <span class="metric-icon success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 7v5l3 3"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value">{{ $stats['hours_month'] }}</div>
    <div class="metric-caption">{{ __('app.company_hours_caption') }}</div>
  </article>
</section>

<section class="content-grid">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.company_details') }}</h2>
          <div class="panel-subtitle">{{ __('app.company_details_subtitle') }}</div>
        </div>
      </div>

      <form method="POST" action="{{ route('admin.companies.update', $company) }}">
        @csrf
        @method('PATCH')

        <div class="filter-grid">
          <div class="field-span-12">
            <label class="form-label">{{ __('app.company_name') }}</label>
            <input type="text" class="form-control" name="name" value="{{ old('name', $company->name) }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.th_owner') }}</label>
            <input type="text" class="form-control" value="{{ $company->owner?->name ?? '-' }}" disabled>
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.th_created') }}</label>
            <input type="text" class="form-control" value="{{ $company->created_at?->format('Y-m-d') }}" disabled>
          </div>

          <div class="field-span-12">
            <button class="btn btn-primary">{{ __('app.btn_save') }}</button>
          </div>
        </div>
      </form>
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.section_settings') }}</h2>
          <div class="panel-subtitle">{{ __('app.company_settings_subtitle') }}</div>
        </div>
      </div>

      @if($settings->isEmpty())
        <div class="empty-state">
          <div class="empty-state-title">{{ __('app.empty_settings') }}</div>
        </div>
      @else
        <div class="info-list">
          @foreach($settings as $setting)
            <div class="info-list-item">
              <div class="stat-pair-label">{{ $setting->key }}</div>
              <div class="stat-pair-value">{{ $setting->value }}</div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </article>
</section>

<section class="content-grid-2">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.section_users') }}</h2>
          <div class="panel-subtitle">{{ __('app.company_users_subtitle') }}</div>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('app.th_name') }}</th>
              <th>{{ __('app.th_email') }}</th>
              <th>{{ __('app.th_role') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              @php
                $roleName = $user->roles->first()?->name ?? '-';
                $roleKey = 'app.role_'.str_replace('-', '_', $roleName);
                $roleLabel = $roleName !== '-' && trans()->has($roleKey) ? __($roleKey) : ucfirst($roleName);
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $roleLabel }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-muted">{{ __('app.empty_users') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.section_employees') }}</h2>
          <div class="panel-subtitle">{{ __('app.company_employees_subtitle') }}</div>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>{{ __('app.th_full_name') }}</th>
              <th>{{ __('app.th_job') }}</th>
              <th>{{ __('app.th_status') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($employees as $employee)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $employee->full_name }}</td>
                <td>{{ $employee->job_title ?? '-' }}</td>
                <td>
                  @if($employee->is_active)
                    <span class="ui-badge badge-success"><span class="badge-dot"></span>{{ __('app.status_active') }}</span>
                  @else
                    <span class="ui-badge badge-danger"><span class="badge-dot"></span>{{ __('app.status_inactive') }}</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-muted">{{ __('app.empty_employees') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </article>
</section>

<section class="surface-card">
  <div class="surface-card-body">
    <div class="panel-header">
      <div class="panel-copy">
        <h2 class="panel-title">{{ __('app.section_audits') }}</h2>
        <div class="panel-subtitle">{{ __('app.company_audits_subtitle') }}</div>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('app.th_time') }}</th>
            <th>{{ __('app.th_user') }}</th>
            <th>{{ __('app.th_action') }}</th>
            <th>{{ __('app.th_model') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($auditLogs as $log)
            @php
              $actionKey = 'app.audit_action_'.str_replace('.', '_', $log->action);
              $actionLabel = trans()->has($actionKey) ? __($actionKey) : $log->action;
            @endphp
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $log->user?->name ?? '-' }}</td>
              <td>{{ $actionLabel }}</td>
              <td>{{ $log->model_type ? class_basename($log->model_type).' #'.$log->model_id : '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-muted">{{ __('app.empty_audit') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection
