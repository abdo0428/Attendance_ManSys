@php
  $isSuperAdmin = $user?->can('companies.manage');
@endphp

<div class="sidebar-section">
  <div class="sidebar-section-label">{{ __('app.section_company_area') }}</div>

  <div class="sidebar-nav">
    <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
      <span class="sidebar-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M3 12l9-9 9 9"></path>
          <path d="M9 21V9h6v12"></path>
        </svg>
      </span>
      <span>{{ __('app.dashboard_title') }}</span>
    </a>

    @can('attendance.view')
      <a class="sidebar-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="3"></rect>
            <path d="M8 2v4M16 2v4M3 10h18"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_attendance') }}</span>
      </a>
    @endcan

    @can('employees.view')
      <a class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_employees') }}</span>
      </a>
    @endcan

    @can('reports.view')
      <a class="sidebar-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.monthly') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 19h16"></path>
            <path d="M7 16V9"></path>
            <path d="M12 16V5"></path>
            <path d="M17 16v-3"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_reports') }}</span>
      </a>
    @endcan

    @can('users.manage')
      <a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M19 8h4M21 6v4"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_users') }}</span>
      </a>
    @endcan

    @can('audit.view')
      <a class="sidebar-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 7v5l3 3"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_audit') }}</span>
      </a>
    @endcan

    @can('settings.manage')
      <a class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.7 1.7 0 0 0 .33 1.82 2 2 0 1 1-2.83 2.83 1.7 1.7 0 0 0-1.82.33 1.7 1.7 0 0 0-.5 1.57 2 2 0 1 1-4 0 1.7 1.7 0 0 0-.5-1.57 1.7 1.7 0 0 0-1.82-.33 2 2 0 1 1-2.83-2.83 1.7 1.7 0 0 0 .33-1.82 1.7 1.7 0 0 0-1.57-.5 2 2 0 1 1 0-4 1.7 1.7 0 0 0 1.57-.5 1.7 1.7 0 0 0 .33-1.82 2 2 0 1 1 2.83-2.83 1.7 1.7 0 0 0 1.82-.33 1.7 1.7 0 0 0 .5-1.57 2 2 0 1 1 4 0 1.7 1.7 0 0 0 .5 1.57 1.7 1.7 0 0 0 1.82.33 2 2 0 1 1 2.83 2.83 1.7 1.7 0 0 0-.33 1.82 1.7 1.7 0 0 0 1.57.5 2 2 0 1 1 0 4 1.7 1.7 0 0 0-1.57.5z"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_settings') }}</span>
      </a>
    @endcan
  </div>
</div>

@if($isSuperAdmin)
  <div class="sidebar-section">
    <div class="sidebar-section-label">{{ __('app.section_super_admin') }}</div>

    <div class="sidebar-nav">
      <a class="sidebar-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" href="{{ route('admin.companies.index') }}">
        <span class="sidebar-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M3 21h18"></path>
            <path d="M5 21V7l7-4 7 4v14"></path>
            <path d="M9 21v-6h6v6"></path>
          </svg>
        </span>
        <span>{{ __('app.nav_companies') }}</span>
      </a>
    </div>
  </div>
@endif
