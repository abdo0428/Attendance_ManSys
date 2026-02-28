@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">{{ __('app.attendance_title') }}</h3>

  <div class="d-flex gap-2">
    <input type="date" class="form-control" id="filterDate" value="{{ now()->toDateString() }}" style="max-width: 180px;">
    <select class="form-select" id="filterEmployee" style="max-width: 220px;">
      <option value="">{{ __('app.filter_all_employees') }}</option>
      @foreach($employees as $emp)
        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
      @endforeach
    </select>
    <button class="btn btn-outline-secondary" id="btnReload">{{ __('app.btn_reload') }}</button>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <h6 class="mb-2">{{ __('app.quick_actions') }}</h6>
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">{{ __('app.employee') }}</label>
        <select class="form-select" id="quick_employee_id">
          <option value="">{{ __('app.select_employee') }}</option>
          @foreach($employees as $emp)
            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-8">
        <div class="d-flex gap-2 flex-wrap">
          @can('attendance.checkin')
            <button class="btn btn-success" id="btnCheckInNow">{{ __('app.btn_checkin_now') }}</button>
          @endcan
          @can('attendance.checkout')
            <button class="btn btn-primary" id="btnCheckOutNow">{{ __('app.btn_checkout_now') }}</button>
          @endcan
          @can('attendance.checkin')
            <button class="btn btn-outline-danger" id="btnMarkAbsent">{{ __('app.btn_mark_absent') }}</button>
          @endcan
        </div>
        <div class="text-danger small mt-2" id="quick_err"></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>{{ __('app.checkin') }}</h6>
        <div class="row g-2">
          <div class="col-md-6">
            <select class="form-select" id="in_employee_id">
              <option value="">{{ __('app.select_employee') }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <input type="time" class="form-control" id="in_time" value="{{ $defaults['default_work_start'] }}">
          </div>
          <div class="col-md-3">
            @can('attendance.checkin')
              <button class="btn btn-success w-100" id="btnCheckIn">{{ __('app.btn_checkin') }}</button>
            @endcan
          </div>
        </div>
        <div class="text-danger small mt-2" id="in_err"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>{{ __('app.checkout') }}</h6>
        <div class="row g-2">
          <div class="col-md-6">
            <select class="form-select" id="out_employee_id">
              <option value="">{{ __('app.select_employee') }}</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <input type="time" class="form-control" id="out_time" value="{{ $defaults['default_work_end'] }}">
          </div>
          <div class="col-md-3">
            @can('attendance.checkout')
              <button class="btn btn-primary w-100" id="btnCheckOut">{{ __('app.btn_checkout') }}</button>
            @endcan
          </div>
        </div>
        <div class="text-danger small mt-2" id="out_err"></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm position-relative">
  <div class="loading-overlay d-none" id="attLoading">
    <div class="spinner-border" role="status"></div>
  </div>
  <div class="card-body">
    <table class="table table-striped" id="attTable" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ __('app.th_employee') }}</th>
          <th>{{ __('app.th_date') }}</th>
          <th>{{ __('app.th_checkin') }}</th>
          <th>{{ __('app.th_checkout') }}</th>
          <th>{{ __('app.th_worked') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
    </table>

    <div class="empty-state mt-3 d-none" id="attEmpty">
      <div class="mb-2">{{ __('app.empty_attendance') }}</div>
      @can('attendance.checkin')
        <button class="btn btn-sm btn-primary" id="btnEmptyCheckIn">{{ __('app.btn_add_checkin') }}</button>
      @endcan
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="logModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('app.edit_log') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="logForm">
        <div class="modal-body">
          <input type="hidden" id="log_id">

          <div class="mb-2">
            <label class="form-label">{{ __('app.th_checkin') }}</label>
            <input type="datetime-local" class="form-control" id="edit_check_in">
          </div>
          <div class="mb-2">
            <label class="form-label">{{ __('app.th_checkout') }}</label>
            <input type="datetime-local" class="form-control" id="edit_check_out">
          </div>
          <div class="mb-2">
            <label class="form-label">{{ __('app.th_notes') }}</label>
            <textarea class="form-control" id="edit_notes" rows="3"></textarea>
          </div>

          <div class="text-danger small" id="log_err"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.btn_close') }}</button>
          @can('attendance.update')
            <button class="btn btn-primary" id="btnSaveLog">{{ __('app.btn_save') }}</button>
          @endcan
        </div>
      </form>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const logModal = new bootstrap.Modal(document.getElementById('logModal'));

  const attTable = $('#attTable').DataTable({
    processing:true,
    serverSide:true,
    ajax:{
      url: "{{ route('attendance.data') }}",
      data: function(d){
        d.date = $('#filterDate').val();
        d.employee_id = $('#filterEmployee').val();
      }
    },
    columns:[
      {data:'id'},
      {data:'employee_name', name:'employee.full_name'},
      {data:'work_date'},
      {data:'check_in', defaultContent:''},
      {data:'check_out', defaultContent:''},
      {data:'worked_hours', orderable:false, searchable:false},
      {data:'actions', orderable:false, searchable:false},
    ]
  });

  attTable.on('processing.dt', function(e, settings, processing){
    $('#attLoading').toggleClass('d-none', !processing);
  });

  attTable.on('draw.dt', function(){
    const hasRows = attTable.data().any();
    $('#attEmpty').toggleClass('d-none', hasRows);
  });

  $('#btnReload, #filterDate, #filterEmployee').on('click change', ()=> attTable.ajax.reload());

  function postOrShowErr(url, payload, errSel){
    $(errSel).text('');
    $.post(url, payload)
      .done(()=>{
        attTable.ajax.reload(null,false);
        showToast('success', "{{ __('app.toast_done') }}");
      })
      .fail((xhr)=>{
        if(xhr.status===422){
          const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
          $(errSel).text(msg);
        }else{
          showToast('error', "{{ __('app.toast_error') }}");
        }
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
    const d = new Date();
    return d.toTimeString().slice(0,5);
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
    document.getElementById('in_employee_id').focus();
    window.scrollTo({top: 0, behavior: 'smooth'});
  });

  // Edit
  $(document).on('click', '.btn-log-edit', function(){
    const id = $(this).data('id');
    $('#log_err').text('');

    $.get("/attendance/logs/" + id, function(res){
      $('#log_id').val(res.log.id);

      const ci = res.log.check_in ? res.log.check_in.replace(' ', 'T').slice(0,16) : '';
      const co = res.log.check_out ? res.log.check_out.replace(' ', 'T').slice(0,16) : '';

      $('#edit_check_in').val(ci);
      $('#edit_check_out').val(co);
      $('#edit_notes').val(res.log.notes || '');
      logModal.show();
    });
  });

  // Save edit
  $('#logForm').on('submit', function(e){
    e.preventDefault();
    $('#log_err').text('');

    const id = $('#log_id').val();

    $.ajax({
      url: "/attendance/logs/" + id,
      method: "POST",
      data: {
        _method: "PUT",
        check_in: $('#edit_check_in').val() ? $('#edit_check_in').val().replace('T',' ') + ':00' : null,
        check_out: $('#edit_check_out').val() ? $('#edit_check_out').val().replace('T',' ') + ':00' : null,
        notes: $('#edit_notes').val()
      }
    }).done(()=>{
      logModal.hide();
      attTable.ajax.reload(null,false);
      showToast('success', "{{ __('app.toast_saved') }}");
    }).fail((xhr)=>{
      if(xhr.status===422){
        const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
        $('#log_err').text(msg);
      }else{
        showToast('error', "{{ __('app.toast_error') }}");
      }
    });
  });

  // Delete log
  $(document).on('click', '.btn-log-delete', function(){
    const id = $(this).data('id');
    Swal.fire({icon:'warning', title:"{{ __('app.confirm_delete') }}", showCancelButton:true, confirmButtonText:"{{ __('app.btn_delete') }}"})
      .then((r)=>{
        if(!r.isConfirmed) return;
        $.ajax({
          url: "/attendance/logs/" + id,
          method: "POST",
          data: {_method:"DELETE"}
        }).done(()=>{
          attTable.ajax.reload(null,false);
          showToast('success', "{{ __('app.toast_deleted') }}");
        }).fail(()=>{
          showToast('error', "{{ __('app.toast_error') }}");
        });
      });
  });
</script>
@endpush

