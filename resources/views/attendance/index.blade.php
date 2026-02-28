@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Daily Attendance</h3>

  <div class="d-flex gap-2">
    <input type="date" class="form-control" id="filterDate" value="{{ now()->toDateString() }}" style="max-width: 180px;">
    <button class="btn btn-outline-secondary" id="btnReload">Reload</button>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Check-in</h6>
        <div class="row g-2">
          <div class="col-md-6">
            <select class="form-select" id="in_employee_id">
              <option value="">Select employee</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <input type="time" class="form-control" id="in_time" value="09:00">
          </div>
          <div class="col-md-3">
            @can('attendance.checkin')
              <button class="btn btn-success w-100" id="btnCheckIn">Check-in</button>
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
        <h6>Check-out</h6>
        <div class="row g-2">
          <div class="col-md-6">
            <select class="form-select" id="out_employee_id">
              <option value="">Select employee</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <input type="time" class="form-control" id="out_time" value="17:00">
          </div>
          <div class="col-md-3">
            @can('attendance.checkout')
              <button class="btn btn-primary w-100" id="btnCheckOut">Check-out</button>
            @endcan
          </div>
        </div>
        <div class="text-danger small mt-2" id="out_err"></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-striped" id="attTable" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Employee</th>
          <th>Date</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Worked (HH:MM)</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="logModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Attendance Log</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="logForm">
        <div class="modal-body">
          <input type="hidden" id="log_id">

          <div class="mb-2">
            <label class="form-label">Check-in</label>
            <input type="datetime-local" class="form-control" id="edit_check_in">
          </div>
          <div class="mb-2">
            <label class="form-label">Check-out</label>
            <input type="datetime-local" class="form-control" id="edit_check_out">
          </div>
          <div class="mb-2">
            <label class="form-label">Notes</label>
            <textarea class="form-control" id="edit_notes" rows="3"></textarea>
          </div>

          <div class="text-danger small" id="log_err"></div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          @can('attendance.update')
            <button class="btn btn-primary" id="btnSaveLog">Save</button>
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

  $('#btnReload, #filterDate').on('click change', ()=> attTable.ajax.reload());

  function postOrShowErr(url, payload, errSel){
    $(errSel).text('');
    $.post(url, payload)
      .done(()=>{ attTable.ajax.reload(null,false); Swal.fire({icon:'success', title:'Done', timer:1000, showConfirmButton:false}); })
      .fail((xhr)=>{
        if(xhr.status===422){
          const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
          $(errSel).text(msg);
        }else{
          Swal.fire({icon:'error', title:'Error', text:'Something went wrong'});
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

  // Edit
  $(document).on('click', '.btn-log-edit', function(){
    const id = $(this).data('id');
    $('#log_err').text('');

    $.get("/attendance/logs/" + id, function(res){
      $('#log_id').val(res.log.id);

      // تحويل datetime إلى format input datetime-local (YYYY-MM-DDTHH:MM)
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
      Swal.fire({icon:'success', title:'Saved', timer:1000, showConfirmButton:false});
    }).fail((xhr)=>{
      if(xhr.status===422){
        const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
        $('#log_err').text(msg);
      }else{
        Swal.fire({icon:'error', title:'Error', text:'Something went wrong'});
      }
    });
  });

  // Delete log
  $(document).on('click', '.btn-log-delete', function(){
    const id = $(this).data('id');
    Swal.fire({icon:'warning', title:'Delete log?', showCancelButton:true, confirmButtonText:'Delete'})
      .then((r)=>{
        if(!r.isConfirmed) return;
        $.ajax({
          url: "/attendance/logs/" + id,
          method: "POST",
          data: {_method:"DELETE"}
        }).done(()=>{
          attTable.ajax.reload(null,false);
          Swal.fire({icon:'success', title:'Deleted', timer:1000, showConfirmButton:false});
        }).fail(()=>{
          Swal.fire({icon:'error', title:'Error', text:'Could not delete'});
        });
      });
  });
</script>
@endpush