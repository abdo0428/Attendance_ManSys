@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">{{ __('app.monthly_report') }}</h3>
  <div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" id="btnThisMonth">{{ __('app.btn_this_month') }}</button>
    <button class="btn btn-outline-secondary" id="btnLastMonth">{{ __('app.btn_last_month') }}</button>
    <button class="btn btn-success" id="btnExport">{{ __('app.btn_export_csv') }}</button>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">{{ __('app.employee') }}</label>
        <select class="form-select" id="rep_employee">
          <option value="">{{ __('app.select_employee') }}</option>
          @foreach($employees as $e)
            <option value="{{ $e->id }}">{{ $e->full_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">{{ __('app.month') }}</label>
        <input type="month" class="form-control" id="rep_month" value="{{ now()->format('Y-m') }}">
      </div>

      <div class="col-md-2">
        <button class="btn btn-primary w-100" id="btnLoad">{{ __('app.btn_load') }}</button>
      </div>
    </div>

    <div class="text-danger small mt-2" id="rep_err"></div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">{{ __('app.total_hours') }}</div>
      <div class="fs-4 fw-bold" id="sum_hours">00:00</div>
    </div></div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">{{ __('app.days_present') }}</div>
      <div class="fs-4 fw-bold" id="sum_days">0</div>
    </div></div>
  </div>
  <div class="col-md-3">
    <div class="card shadow-sm"><div class="card-body">
      <div class="text-muted">{{ __('app.days_in_month') }}</div>
      <div class="fs-4 fw-bold" id="sum_dim">0</div>
    </div></div>
  </div>
</div>

<div class="card shadow-sm position-relative">
  <div class="loading-overlay d-none" id="repLoading">
    <div class="spinner-border" role="status"></div>
  </div>
  <div class="card-body">
    <table class="table table-striped" id="repTable" style="width:100%">
      <thead>
        <tr>
          <th>{{ __('app.th_date') }}</th>
          <th>{{ __('app.th_checkin') }}</th>
          <th>{{ __('app.th_checkout') }}</th>
          <th>{{ __('app.th_worked') }}</th>
          <th>{{ __('app.th_late') }}</th>
          <th>{{ __('app.th_overtime') }}</th>
          <th>{{ __('app.th_notes') }}</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div class="empty-state mt-3 d-none" id="repEmpty">
      <div class="mb-2">{{ __('app.empty_monthly') }}</div>
      <button class="btn btn-sm btn-primary" id="btnEmptyReload">{{ __('app.btn_load') }}</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const tLate = "{{ __('app.badge_late') }}";
  const tOvertime = "{{ __('app.badge_overtime') }}";
  const tMissing = "{{ __('app.badge_missing_checkout') }}";
  function setMonthInput(date){
    const m = date.toISOString().slice(0,7);
    $('#rep_month').val(m);
  }

  $('#btnThisMonth').on('click', function(){
    setMonthInput(new Date());
  });

  $('#btnLastMonth').on('click', function(){
    const d = new Date();
    d.setMonth(d.getMonth() - 1);
    setMonthInput(d);
  });

  $('#btnExport').on('click', function(){
    const employee_id = $('#rep_employee').val();
    const month = $('#rep_month').val();
    if(!employee_id || !month){
      $('#rep_err').text("{{ __('app.err_select_employee_month') }}");
      return;
    }
    const url = "{{ route('reports.monthly.export') }}" + `?employee_id=${employee_id}&month=${month}`;
    window.location.href = url;
  });

  $('#btnLoad').on('click', function(){
    $('#rep_err').text('');
    $('#repLoading').removeClass('d-none');

    const employee_id = $('#rep_employee').val();
    const month = $('#rep_month').val();

    if(!employee_id || !month){
      $('#rep_err').text("{{ __('app.err_select_employee_month') }}");
      $('#repLoading').addClass('d-none');
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
          const lateBadge = r.late ? `<span class="badge text-bg-warning">${tLate}</span>` : '';
          const otBadge = r.overtime ? `<span class="badge text-bg-info">${tOvertime}</span>` : '';
          const missingBadge = r.missing_checkout ? `<span class="badge text-bg-danger">${tMissing}</span>` : '';

          tbody.append(`
            <tr>
              <td>${r.work_date}</td>
              <td>${r.check_in || ''}</td>
              <td>${r.check_out || ''}</td>
              <td>${r.worked}</td>
              <td>${lateBadge}</td>
              <td>${otBadge}</td>
              <td>${missingBadge} ${r.notes || ''}</td>
            </tr>
          `);
        });

        const hasRows = res.rows.length > 0;
        $('#repEmpty').toggleClass('d-none', hasRows);
      })
      .fail((xhr)=>{
        if(xhr.status===422){
          const msg = Object.values(xhr.responseJSON.errors||{}).map(a=>a[0]).join(' | ');
          $('#rep_err').text(msg);
        }else{
          showToast('error', "{{ __('app.toast_error') }}");
        }
      })
      .always(()=>{
        $('#repLoading').addClass('d-none');
      });
  });

  $('#btnEmptyReload').on('click', function(){
    $('#btnLoad').click();
  });
</script>
@endpush

