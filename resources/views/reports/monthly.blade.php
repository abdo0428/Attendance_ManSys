@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Monthly Report</h3>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Employee</label>
        <select class="form-select" id="rep_employee">
          <option value="">Select employee</option>
          @foreach($employees as $e)
            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Month</label>
        <input type="month" class="form-control" id="rep_month" value="{{ now()->format('Y-m') }}">
      </div>

      <div class="col-md-2">
        <button class="btn btn-primary w-100" id="btnLoad">Load</button>
      </div>
    </div>

    <div class="text-danger small mt-2" id="rep_err"></div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">Total Hours</div>
      <div class="fs-4 fw-bold" id="sum_hours">00:00</div>
    </div></div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">Days Present</div>
      <div class="fs-4 fw-bold" id="sum_days">0</div>
    </div></div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">Days in Month</div>
      <div class="fs-4 fw-bold" id="sum_dim">0</div>
    </div></div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-striped" id="repTable" style="width:100%">
      <thead>
        <tr>
          <th>Date</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Worked</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $('#btnLoad').on('click', function(){
    $('#rep_err').text('');
    const employee_id = $('#rep_employee').val();
    const month = $('#rep_month').val();

    if(!employee_id || !month){
      $('#rep_err').text('Please select employee and month.');
      return;
    }

    $.post("{{ route('reports.monthly.data') }}", {employee_id, month})
      .done((res)=>{
        $('#sum_hours').text(res.summary.total_hours);
        $('#sum_days').text(res.summary.days_present);
        $('#sum_dim').text(res.summary.days_in_month);

        const tbody = $('#repTable tbody');
        tbody.empty();

        res.rows.forEach(r=>{
          tbody.append(`
            <tr>
              <td>${r.work_date}</td>
              <td>${r.check_in || ''}</td>
              <td>${r.check_out || ''}</td>
              <td>${r.worked}</td>
              <td>${r.notes || ''}</td>
            </tr>
          `);
        });
      })
      .fail((xhr)=>{
        if(xhr.status===422){
          const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
          $('#rep_err').text(msg);
        }else{
          Swal.fire({icon:'error', title:'Error', text:'Could not load report'});
        }
      });
  });
</script>
@endpush