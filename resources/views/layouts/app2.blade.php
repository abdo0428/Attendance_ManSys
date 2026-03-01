<!doctype html>
@php
  $currentLocale = app()->getLocale();
  $isRtl = $currentLocale === 'ar';
  $appName = \App\Models\Setting::getValue('company_name', config('app.name', 'Attendance Lite'));
  $user = auth()->user();
  $roleName = $user?->roles?->first()?->name ?? 'viewer';
  $roleKey = 'app.role_'.str_replace('-', '_', $roleName);
  $roleLabel = trans()->has($roleKey) ? __($roleKey) : ucwords(str_replace('-', ' ', $roleName));
  $initials = collect(explode(' ', (string) $user?->name))
    ->filter()
    ->take(2)
    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
    ->implode('');
  $searchFallbackUrl = $user?->can('employees.view')
    ? route('employees.index')
    : ($user?->can('attendance.view')
      ? route('attendance.index')
      : ($user?->can('reports.view')
        ? route('reports.monthly')
        : ($user?->can('companies.manage')
          ? route('admin.companies.index')
          : route('dashboard'))));
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', $appName)</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif

  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('styles')
</head>

<body class="app-body font-sans" data-route="{{ request()->route()?->getName() }}">
<a href="#mainContent" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 btn btn-primary btn-sm">
  {{ __('app.skip_to_content') }}
</a>

<div class="app-shell">
  <aside class="app-sidebar d-none d-lg-flex">
    <div class="app-sidebar-panel">
      <div class="brand-card">
        <div class="brand-mark">AL</div>
        <div class="brand-copy">
          <div class="brand-name">{{ $appName }}</div>
          <div class="brand-subtitle">{{ __('app.attendance_control_panel') }}</div>
        </div>
      </div>

      <div class="sidebar-user-card">
        <div class="sidebar-user-avatar">{{ $initials ?: 'U' }}</div>
        <div>
          <div class="sidebar-user-name">{{ $user?->name }}</div>
          <span class="brand-badge">{{ $roleLabel }}</span>
        </div>
      </div>

      @include('layouts.partials.sidebar-nav', ['user' => $user])
    </div>
  </aside>

  <div class="offcanvas offcanvas-start app-offcanvas-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
      <div>
        <div class="brand-name" id="mobileSidebarLabel">{{ $appName }}</div>
        <div class="brand-subtitle">{{ __('app.attendance_control_panel') }}</div>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <div class="sidebar-user-card mb-4">
        <div class="sidebar-user-avatar">{{ $initials ?: 'U' }}</div>
        <div>
          <div class="sidebar-user-name">{{ $user?->name }}</div>
          <span class="brand-badge">{{ $roleLabel }}</span>
        </div>
      </div>

      @include('layouts.partials.sidebar-nav', ['user' => $user])
    </div>
  </div>

  <div class="app-main">
    <header class="app-topbar">
      <div class="topbar-left">
        <button class="btn btn-icon d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="{{ __('app.open_navigation') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16"></path>
          </svg>
        </button>

        <div>
          <div class="topbar-company">{{ $appName }}</div>
          <div class="topbar-meta">
            <span class="role-badge">{{ $roleLabel }}</span>
            <span class="topbar-date">{{ now()->translatedFormat('D, d M Y') }}</span>
          </div>
        </div>
      </div>

      <div class="topbar-actions">
        <form class="topbar-search-form" id="topbarSearchForm">
          <span class="topbar-search-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="m20 20-3.5-3.5"></path>
            </svg>
          </span>
          <input
            type="search"
            id="topbarSearchInput"
            class="form-control shell-search"
            placeholder="{{ __('app.search_employees_attendance') }}"
            aria-label="{{ __('app.search_employees_attendance') }}"
            value="{{ request('search', '') }}"
          >
        </form>

        <button class="btn btn-icon position-relative" type="button" title="{{ __('app.notifications') }}" aria-label="{{ __('app.notifications') }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
          <span class="notification-dot"></span>
        </button>

        <div class="dropdown">
          <button class="btn btn-icon btn-text dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            {{ strtoupper($currentLocale) }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end {{ $isRtl ? 'text-end' : '' }}">
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'en']) }}">{{ __('app.lang_english') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'ar']) }}">{{ __('app.lang_arabic') }}</a></li>
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'tr']) }}">{{ __('app.lang_turkish') }}</a></li>
          </ul>
        </div>

        <div class="dropdown">
          <button class="btn avatar-button dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar-pill">{{ $initials ?: 'U' }}</span>
            <span class="d-none d-md-inline">{{ $user?->name }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end {{ $isRtl ? 'text-end' : '' }}">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('app.profile') }}</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item">{{ __('app.nav_logout') }}</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main id="mainContent" class="app-content">
      <div class="app-content-inner">
        @yield('content')
      </div>
    </main>
  </div>
</div>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
  });

  window.showToast = function(type, message){
    const container = document.getElementById('toastContainer');
    const el = document.createElement('div');
    const tone = type === 'error' ? 'danger' : type;

    el.className = 'toast shell-toast align-items-center border-0 toast-' + tone;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');

    el.innerHTML = `
      <div class="d-flex align-items-center">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

    container.appendChild(el);
    const toast = new bootstrap.Toast(el, {delay: 2400});
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  window.dispatchTopbarSearch = function(query){
    window.dispatchEvent(new CustomEvent('app:topbar-search', {detail: {query}}));
  };

  document.getElementById('topbarSearchForm')?.addEventListener('submit', function(event){
    event.preventDefault();
    const query = document.getElementById('topbarSearchInput')?.value.trim() || '';

    if (!query) {
      return;
    }

    dispatchTopbarSearch(query);

    const searchFallbackUrl = @json($searchFallbackUrl);
    setTimeout(function(){
      if (!window.__topbarSearchHandled) {
        window.location.href = searchFallbackUrl + '?search=' + encodeURIComponent(query);
      }

      window.__topbarSearchHandled = false;
    }, 20);
  });

  @if(session('toast'))
    showToast(@json(session('toast.type')), @json(session('toast.message')));
  @endif
</script>

@stack('scripts')
</body>
</html>
