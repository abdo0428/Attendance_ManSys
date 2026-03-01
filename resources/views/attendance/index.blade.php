@extends('layouts.app2')

@section('title', __('app.attendance_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_company_area') }}</div>
    <h1 class="page-title">{{ __('app.attendance_title') }}</h1>
    <div class="page-subtitle">{{ __('app.attendance_page_subtitle') }}</div>
  </div>

  <div class="page-actions">
    <button class="btn btn-outline-secondary" id="btnReload">{{ __('app.btn_reload') }}</button>
  </div>
</section>

<section class="filter-card">
  <div class="filter-grid">
    <div class="field-span-3">
      <label class="form-label">{{ __('app.th_date') }}</label>
      <input type="date" class="form-control" id="filterDate" value="{{ now()->toDateString() }}">
    </div>

    <div class="field-span-4">
      <label class="form-label">{{ __('app.employee') }}</label>
      <select class="form-select employee-select" id="filterEmployee" data-placeholder="{{ __('app.filter_all_employees') }}">
        <option value="">{{ __('app.filter_all_employees') }}</option>
        @foreach($employees as $employee)
          <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
        @endforeach
      </select>
    </div>

    <div class="field-span-5">
      <label class="form-label">{{ __('app.label_search') }}</label>
      <input type="search" class="form-control" id="attendanceSearch" placeholder="{{ __('app.attendance_search_placeholder') }}" value="{{ $initialSearch }}">
    </div>
  </div>
</section>

<section class="quick-punch-grid">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.attendance_quick_punch') }}</h2>
          <div class="panel-subtitle">{{ __('app.attendance_quick_punch_subtitle') }}</div>
        </div>
      </div>

      <div class="filter-grid">
        <div class="field-span-12">
          <label class="form-label">{{ __('app.employee') }}</label>
          <select class="form-select employee-select" id="quick_employee_id" data-placeholder="{{ __('app.select_employee') }}">
            <option value="">{{ __('app.select_employee') }}</option>
            @foreach($employees as $employee)
              <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="field-span-12">
          <div class="quick-punch-actions">
            @can('attendance.checkin')
              <button class="btn btn-primary" id="btnCheckInNow">{{ __('app.btn_checkin_now') }}</button>
            @endcan
            @can('attendance.checkout')
              <button class="btn btn-success" id="btnCheckOutNow">{{ __('app.btn_checkout_now') }}</button>
            @endcan
            @can('attendance.checkin')
              <button class="btn btn-danger" id="btnMarkAbsent">{{ __('app.btn_mark_absent') }}</button>
            @endcan
          </div>
          <div class="error-text" id="quick_err"></div>
        </div>
      </div>
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.attendance_daily_summary') }}</h2>
          <div class="panel-subtitle">{{ __('app.attendance_daily_summary_subtitle') }}</div>
        </div>
      </div>

      <div class="inline-metrics mb-4">
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_present') }}</div>
          <div class="mini-stat-value">{{ $summary['present'] }}</div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_absent') }}</div>
          <div class="mini-stat-value">{{ $summary['absent'] }}</div>
        </div>
        <div class="mini-stat">
          <div class="mini-stat-label">{{ __('app.status_pending') }}</div>
          <div class="mini-stat-value">{{ $summary['pending'] }}</div>
        </div>
      </div>

      <div class="filter-grid">
        <div class="field-span-6">
          <label class="form-label">{{ __('app.checkin') }}</label>
          <select class="form-select employee-select" id="in_employee_id" data-placeholder="{{ __('app.select_employee') }}">
            <option value="">{{ __('app.select_employee') }}</option>
            @foreach($employees as $employee)
              <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field-span-3">
          <label class="form-label">{{ __('app.th_checkin') }}</label>
          <input type="time" class="form-control" id="in_time" value="{{ $defaults['default_work_start'] }}">
        </div>
        <div class="field-span-3">
          @can('attendance.checkin')
            <button class="btn btn-primary w-100" id="btnCheckIn">{{ __('app.btn_checkin') }}</button>
          @endcan
        </div>

        <div class="field-span-6">
          <label class="form-label">{{ __('app.checkout') }}</label>
          <select class="form-select employee-select" id="out_employee_id" data-placeholder="{{ __('app.select_employee') }}">
            <option value="">{{ __('app.select_employee') }}</option>
            @foreach($employees as $employee)
              <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field-span-3">
          <label class="form-label">{{ __('app.th_checkout') }}</label>
          <input type="time" class="form-control" id="out_time" value="{{ $defaults['default_work_end'] }}">
        </div>
        <div class="field-span-3">
          @can('attendance.checkout')
            <button class="btn btn-success w-100" id="btnCheckOut">{{ __('app.btn_checkout') }}</button>
          @endcan
        </div>
      </div>

      <div class="error-text" id="in_err"></div>
      <div class="error-text" id="out_err"></div>
    </div>
  </article>
</section>

<section class="table-panel position-relative">
  <div class="table-header">
    <div class="panel-copy">
      <h2 class="panel-title">{{ __('app.attendance_daily_logs') }}</h2>
      <div class="panel-subtitle">{{ __('app.attendance_daily_logs_subtitle') }}</div>
    </div>
  </div>

  <div class="loading-overlay d-none" id="attLoading">
    <div class="skeleton-card">
      <div class="skeleton-line w-75"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line w-35"></div>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table align-middle" id="attTable" style="width:100%">
      <thead>
        <tr>
          <th>{{ __('app.th_employee') }}</th>
          <th>{{ __('app.th_date') }}</th>
          <th>{{ __('app.th_checkin') }}</th>
          <th>{{ __('app.th_checkout') }}</th>
          <th>{{ __('app.th_worked') }}</th>
          <th>{{ __('app.th_status') }}</th>
          <th>{{ __('app.th_notes') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>

  <div class="empty-state mt-3 d-none" id="attEmpty">
    <span class="empty-state-icon">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
        <rect x="3" y="4" width="18" height="18" rx="3"></rect>
        <path d="M8 2v4M16 2v4M3 10h18"></path>
      </svg>
    </span>
    <div class="empty-state-title">{{ __('app.empty_attendance') }}</div>
    <div class="empty-state-text">{{ __('app.attendance_empty_hint') }}</div>
    @can('attendance.checkin')
      <button class="btn btn-primary mt-3" id="btnEmptyCheckIn">{{ __('app.btn_add_checkin') }}</button>
    @endcan
  </div>
</section>

<div class="modal fade" id="logModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title">{{ __('app.edit_log') }}</h5>
          <div class="panel-subtitle">{{ __('app.attendance_modal_subtitle') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="logForm">
        <div class="modal-body">
          <input type="hidden" id="log_id">

          <div class="mb-3">
            <label class="form-label">{{ __('app.th_checkin') }}</label>
            <input type="datetime-local" class="form-control" id="edit_check_in">
          </div>

          <div class="mb-3">
            <label class="form-label">{{ __('app.th_checkout') }}</label>
            <input type="datetime-local" class="form-control" id="edit_check_out">
          </div>

          <div class="mb-0">
            <label class="form-label">{{ __('app.th_notes') }}</label>
            <textarea class="form-control" id="edit_notes" rows="4"></textarea>
          </div>

          <div class="error-text" id="log_err"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('app.btn_close') }}</button>
          @can('attendance.update')
            <button class="btn btn-primary" id="btnSaveLog">{{ __('app.btn_save') }}</button>
          @endcan
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  const attendanceNoNotes = @json(__('app.attendance_no_notes'));

  $('.employee-select').each(function(){
    const placeholder = $(this).data('placeholder') || '';

    $(this).select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: placeholder,
      allowClear: true,
      minimumResultsForSearch: 0
    });
  });

  const logModal = new bootstrap.Modal(document.getElementById('logModal'));
  const attendanceInitialSearch = @json($initialSearch);

  const attTable = $('#attTable').DataTable({
    processing: true,
    serverSide: true,
    searchDelay: 300,
    ajax: {
      url: "{{ route('attendance.data') }}",
      data: function(data){
        data.date = $('#filterDate').val();
        data.employee_id = $('#filterEmployee').val();
      }
    },
    columns: [
      {
        data: 'employee_name',
        name: 'employee.full_name',
        render: function(data){
          return `<span class="fw-semibold">${data || '-'}</span>`;
        }
      },
      {data: 'work_date', name: 'work_date'},
      {data: 'check_in', defaultContent: '-'},
      {data: 'check_out', defaultContent: '-'},
      {data: 'worked_hours', orderable: false, searchable: false},
      {data: 'status_badge', orderable: false, searchable: false},
      {
        data: 'notes',
        name: 'notes',
        render: function(data){
          return data && data !== '-'
            ? `<span class="panel-subtitle">${data}</span>`
            : `<span class="panel-subtitle">${attendanceNoNotes}</span>`;
        }
      },
      {data: 'actions', orderable: false, searchable: false},
    ],
    order: [[1, 'desc']]
  });

  attTable.on('processing.dt', function(event, settings, processing){
    $('#attLoading').toggleClass('d-none', !processing);
  });

  attTable.on('draw.dt', function(){
    const hasRows = attTable.data().any();
    $('#attEmpty').toggleClass('d-none', hasRows);
  });

  $('#btnReload, #filterDate').on('click change', function(){
    attTable.ajax.reload();
  });

  $('#filterEmployee').on('change', function(){
    attTable.ajax.reload();
  });

  $('#attendanceSearch').on('input', function(){
    attTable.search(this.value).draw();
  });

  if (attendanceInitialSearch) {
    attTable.search(attendanceInitialSearch).draw();
  }

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#attendanceSearch').val(query);
    attTable.search(query).draw();
  });

  function postOrShowErr(url, payload, errorSelector){
    $(errorSelector).text('');

    $.post(url, payload)
      .done(function(){
        attTable.ajax.reload(null, false);
        showToast('success', "{{ __('app.toast_done') }}");
      })
      .fail(function(xhr){
        if (xhr.status === 422) {
          const message = Object.values(xhr.responseJSON.errors || {}).map((item) => item[0]).join(' | ');
          $(errorSelector).text(message);
          return;
        }

        showToast('error', "{{ __('app.toast_error') }}");
      });
  }

  $('#btnCheckIn').on('click', function(){
    postOrShowErr("{{ route('attendance.checkin') }}", {
      employee_id: $('#in_employee_id').val(),
      work_date: $('#filterDate').val(),
      check_in_time: $('#in_time').val()
    }, '#in_err');
  });

  $('#btnCheckOut').on('click', function(){
    postOrShowErr("{{ route('attendance.checkout') }}", {
      employee_id: $('#out_employee_id').val(),
      work_date: $('#filterDate').val(),
      check_out_time: $('#out_time').val()
    }, '#out_err');
  });

  function nowTime(){
    const now = new Date();
    return now.toTimeString().slice(0, 5);
  }

  $('#btnCheckInNow').on('click', function(){
    postOrShowErr("{{ route('attendance.checkin') }}", {
      employee_id: $('#quick_employee_id').val(),
      work_date: $('#filterDate').val(),
      check_in_time: nowTime()
    }, '#quick_err');
  });

  $('#btnCheckOutNow').on('click', function(){
    postOrShowErr("{{ route('attendance.checkout') }}", {
      employee_id: $('#quick_employee_id').val(),
      work_date: $('#filterDate').val(),
      check_out_time: nowTime()
    }, '#quick_err');
  });

  $('#btnMarkAbsent').on('click', function(){
    postOrShowErr("{{ route('attendance.absent') }}", {
      employee_id: $('#quick_employee_id').val(),
      work_date: $('#filterDate').val()
    }, '#quick_err');
  });

  $('#btnEmptyCheckIn').on('click', function(){
    document.getElementById('quick_employee_id')?.focus();
    window.scrollTo({top: 0, behavior: 'smooth'});
  });

  $(document).on('click', '.btn-log-edit', function(){
    const logId = $(this).data('id');
    $('#log_err').text('');

    $.get(`/attendance/logs/${logId}`, function(response){
      $('#log_id').val(response.log.id);
      $('#edit_check_in').val(response.log.check_in ? response.log.check_in.replace(' ', 'T').slice(0, 16) : '');
      $('#edit_check_out').val(response.log.check_out ? response.log.check_out.replace(' ', 'T').slice(0, 16) : '');
      $('#edit_notes').val(response.log.notes || '');
      logModal.show();
    });
  });

  $('#logForm').on('submit', function(event){
    event.preventDefault();
    $('#log_err').text('');

    const logId = $('#log_id').val();

    $.ajax({
      url: `/attendance/logs/${logId}`,
      method: 'POST',
      data: {
        _method: 'PUT',
        check_in: $('#edit_check_in').val() ? $('#edit_check_in').val().replace('T', ' ') + ':00' : null,
        check_out: $('#edit_check_out').val() ? $('#edit_check_out').val().replace('T', ' ') + ':00' : null,
        notes: $('#edit_notes').val()
      }
    }).done(function(){
      logModal.hide();
      attTable.ajax.reload(null, false);
      showToast('success', "{{ __('app.toast_saved') }}");
    }).fail(function(xhr){
      if (xhr.status === 422) {
        const message = Object.values(xhr.responseJSON.errors || {}).map((item) => item[0]).join(' | ');
        $('#log_err').text(message);
        return;
      }

      showToast('error', "{{ __('app.toast_error') }}");
    });
  });

  $(document).on('click', '.btn-log-delete', function(){
    const logId = $(this).data('id');

    Swal.fire({
      icon: 'warning',
      title: "{{ __('app.confirm_delete') }}",
      showCancelButton: true,
      confirmButtonText: "{{ __('app.btn_delete') }}"
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: `/attendance/logs/${logId}`,
        method: 'POST',
        data: {_method: 'DELETE'}
      }).done(function(){
        attTable.ajax.reload(null, false);
        showToast('success', "{{ __('app.toast_deleted') }}");
      }).fail(function(){
        showToast('error', "{{ __('app.toast_error') }}");
      });
    });
  });
</script>
@endpush
