<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Attendance Lite</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- DataTables -->
  <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('styles')
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('dashboard') }}">Attendance Lite</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        @can('employees.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">Employees</a></li>
        @endcan
        @can('attendance.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('attendance.index') }}">Attendance</a></li>
        @endcan
        @can('reports.view')
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.monthly') }}">Monthly Report</a></li>
        @endcan
      </ul>

      <div class="d-flex align-items-center gap-2 text-white">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-light btn-sm">Logout</button>
        </form>
      </div>
    </div>
  </div>
</nav>

<main class="container py-4">
  @yield('content')
</main>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
  $.ajaxSetup({
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
  });
</script>

@stack('scripts')
</body>
</html>