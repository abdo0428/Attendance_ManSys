<!doctype html>
@php
  $currentLocale = app()->getLocale();
  $isRtl = $currentLocale === 'ar';
  $appName = \App\Models\Setting::getValue('company_name', config('app.name', 'Attendance Lite'));
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $appName }}</title>

  <!-- Bootstrap -->
  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif

  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- App Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <!-- SweetAlert2 (confirm dialogs) -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('styles')
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('dashboard') }}">{{ $appName }}</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        @can('employees.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">{{ __('app.nav_employees') }}</a></li>
        @endcan
        @can('attendance.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">{{ __('app.nav_attendance') }}</a></li>
        @endcan
        @can('reports.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.monthly') }}">{{ __('app.nav_reports') }}</a></li>
        @endcan
        @can('settings.manage')
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.edit') }}">{{ __('app.nav_settings') }}</a></li>
        @endcan
        @can('users.manage')
          <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">{{ __('app.nav_users') }}</a></li>
        @endcan
        @can('audit.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('audit.index') }}">{{ __('app.nav_audit') }}</a></li>
        @endcan
      </ul>

      <div class="d-flex align-items-center gap-2 text-white">
        <div class="dropdown">
          <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
            {{ strtoupper($currentLocale) }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'en']) }}">English</a></li>
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'ar']) }}">العربية</a></li>
            <li><a class="dropdown-item" href="{{ route('locale.switch', ['locale' => 'tr']) }}">Türkçe</a></li>
          </ul>
        </div>

        <button class="btn btn-outline-light btn-sm" id="themeToggle" type="button">
          {{ __('app.nav_theme') }}
        </button>

        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-light btn-sm">{{ __('app.nav_logout') }}</button>
        </form>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
  @yield('content')
</main>

<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
  });

  function applyTheme(theme){
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('theme', theme);
  }

  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  document.getElementById('themeToggle').addEventListener('click', function(){
    const current = document.documentElement.dataset.theme || 'light';
    applyTheme(current === 'dark' ? 'light' : 'dark');
  });

  window.showToast = function(type, message){
    const container = document.getElementById('toastContainer');
    const el = document.createElement('div');
    const bg = type === 'error' ? 'danger' : type;

    el.className = 'toast align-items-center text-bg-' + bg + ' border-0';
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'assertive');
    el.setAttribute('aria-atomic', 'true');

    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

    container.appendChild(el);
    const toast = new bootstrap.Toast(el, {delay: 2000});
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
  };

  @if(session('toast'))
    showToast("{{ session('toast.type') }}", "{{ session('toast.message') }}");
  @endif
</script>

@stack('scripts')
</body>
</html>

