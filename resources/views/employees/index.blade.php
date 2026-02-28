@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-0">{{ __('app.employees_title') }}</h3>
    <div class="text-muted">{{ __('app.employees_subtitle') }}</div>
  </div>

  <div class="d-flex gap-2">
    <select class="form-select" id="filterStatus" style="max-width: 180px;">
      <option value="">{{ __('app.filter_all') }}</option>
      <option value="active">{{ __('app.status_active') }}</option>
      <option value="inactive">{{ __('app.status_inactive') }}</option>
    </select>
    @can('employees.create')
      <button class="btn btn-primary" id="btnAdd">+ {{ __('app.btn_add_employee') }}</button>
    @endcan
  </div>
</div>

<div class="card shadow-sm position-relative">
  <div class="loading-overlay d-none" id="empLoading">
    <div class="spinner-border" role="status"></div>
  </div>
  <div class="card-body">
    <table class="table table-striped" id="employeesTable" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>{{ __('app.th_full_name') }}</th>
          <th>{{ __('app.th_email') }}</th>
          <th>{{ __('app.th_phone') }}</th>
          <th>{{ __('app.th_job') }}</th>
          <th>{{ __('app.th_status') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
    </table>

    <div class="empty-state mt-3 d-none" id="empEmpty">
      <div class="mb-2">{{ __('app.empty_employees') }}</div>
      @can('employees.create')
        <button class="btn btn-sm btn-primary" id="btnEmptyAdd">{{ __('app.btn_add_employee') }}</button>
      @endcan
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employeeModalTitle">{{ __('app.modal_add_employee') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="employeeForm">
        <div class="modal-body">
          <input type="hidden" id="employee_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('app.th_full_name') }}</label>
              <input type="text" class="form-control" id="full_name" required>
              <div class="text-danger small" id="err_full_name"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('app.th_email') }}</label>
              <input type="email" class="form-control" id="email">
              <div class="text-danger small" id="err_email"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('app.th_phone') }}</label>
              <input type="text" class="form-control" id="phone">
              <div class="text-danger small" id="err_phone"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ __('app.th_job') }}</label>
              <input type="text" class="form-control" id="job_title">
              <div class="text-danger small" id="err_job_title"></div>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('app.th_status') }}</label>
              <select class="form-select" id="is_active">
                <option value="1">{{ __('app.status_active') }}</option>
                <option value="0">{{ __('app.status_inactive') }}</option>
              </select>
              <div class="text-danger small" id="err_is_active"></div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('app.btn_close') }}</button>
          <button class="btn btn-primary" id="btnSave">{{ __('app.btn_save') }}</button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const modalEl = document.getElementById('employeeModal');
  const modal = new bootstrap.Modal(modalEl);

  function clearErrors(){
    ['full_name','email','phone','job_title','is_active'].forEach(f => $('#err_'+f).text(''));
  }

  function fillForm(emp){
    $('#employee_id').val(emp.id);
    $('#full_name').val(emp.full_name);
    $('#email').val(emp.email || '');
    $('#phone').val(emp.phone || '');
    $('#job_title').val(emp.job_title || '');
    $('#is_active').val(emp.is_active ? 1 : 0);
  }

  const table = $('#employeesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('employees.data') }}",
      data: function(d){
        d.status = $('#filterStatus').val();
      }
    },
    columns: [
      {data:'id'},
      {data:'full_name'},
      {data:'email', defaultContent:''},
      {data:'phone', defaultContent:''},
      {data:'job_title', defaultContent:''},
      {data:'status', orderable:false, searchable:false},
      {data:'actions', orderable:false, searchable:false},
    ]
  });

  table.on('processing.dt', function(e, settings, processing){
    $('#empLoading').toggleClass('d-none', !processing);
  });

  table.on('draw.dt', function(){
    const hasRows = table.data().any();
    $('#empEmpty').toggleClass('d-none', hasRows);
  });

  $('#filterStatus').on('change', function(){
    table.ajax.reload();
  });

  $('#btnAdd, #btnEmptyAdd').on('click', function(){
    clearErrors();
    $('#employeeModalTitle').text("{{ __('app.modal_add_employee') }}");
    $('#employeeForm')[0].reset();
    $('#employee_id').val('');
    modal.show();
  });

  $('#employeeForm').on('submit', function(e){
    e.preventDefault();
    clearErrors();

    const id = $('#employee_id').val();
    const payload = {
      full_name: $('#full_name').val(),
      email: $('#email').val(),
      phone: $('#phone').val(),
      job_title: $('#job_title').val(),
      is_active: $('#is_active').val(),
    };

    let url = "{{ route('employees.store') }}";
    let method = "POST";

    if(id){
      url = "/employees/" + id;
      payload._method = "PUT";
    }

    $('#btnSave').prop('disabled', true);

    $.ajax({
      url, method,
      data: payload,
      success: function(){
        modal.hide();
        table.ajax.reload(null,false);
        showToast('success', "{{ __('app.toast_saved') }}");
      },
      error: function(xhr){
        if(xhr.status === 422){
          const errs = xhr.responseJSON.errors || {};
          Object.keys(errs).forEach(k => $('#err_'+k).text(errs[k][0]));
        }else{
          showToast('error', "{{ __('app.toast_error') }}");
        }
      },
      complete: function(){
        $('#btnSave').prop('disabled', false);
      }
    });
  });

  // Edit
  $(document).on('click', '.btn-edit', function(){
    const id = $(this).data('id');
    clearErrors();
    $('#employeeModalTitle').text("{{ __('app.modal_edit_employee') }}");

    $.get("/employees/" + id, function(res){
      fillForm(res.employee);
      modal.show();
    });
  });

  // Delete
  $(document).on('click', '.btn-delete', function(){
    const id = $(this).data('id');
    Swal.fire({
      icon:'warning',
      title:"{{ __('app.confirm_delete') }}",
      text:"{{ __('app.confirm_delete_employee') }}",
      showCancelButton:true,
      confirmButtonText:"{{ __('app.btn_delete') }}"
    }).then((r)=>{
      if(!r.isConfirmed) return;

      $.ajax({
        url: "/employees/" + id,
        method: "POST",
        data: {_method:"DELETE"},
        success: function(){
          table.ajax.reload(null,false);
          showToast('success', "{{ __('app.toast_deleted') }}");
        },
        error: function(){
          showToast('error', "{{ __('app.toast_error') }}");
        }
      });
    });
  });
</script>
@endpush

