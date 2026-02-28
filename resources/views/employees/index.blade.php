@extends('layouts.app2')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Employees</h3>

  @can('employees.create')
    <button class="btn btn-primary" id="btnAdd">+ Add Employee</button>
  @endcan
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <table class="table table-striped" id="employeesTable" style="width:100%">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Job</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employeeModalTitle">Add Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="employeeForm">
        <div class="modal-body">
          <input type="hidden" id="employee_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" required>
              <div class="text-danger small" id="err_full_name"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" id="email">
              <div class="text-danger small" id="err_email"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" id="phone">
              <div class="text-danger small" id="err_phone"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Job Title</label>
              <input type="text" class="form-control" id="job_title">
              <div class="text-danger small" id="err_job_title"></div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select class="form-select" id="is_active">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
              <div class="text-danger small" id="err_is_active"></div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button class="btn btn-primary" id="btnSave">Save</button>
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
    ajax: "{{ route('employees.data') }}",
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

  $('#btnAdd').on('click', function(){
    clearErrors();
    $('#employeeModalTitle').text('Add Employee');
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
        Swal.fire({icon:'success', title:'Saved', timer:1200, showConfirmButton:false});
      },
      error: function(xhr){
        if(xhr.status === 422){
          const errs = xhr.responseJSON.errors || {};
          Object.keys(errs).forEach(k => $('#err_'+k).text(errs[k][0]));
        }else{
          Swal.fire({icon:'error', title:'Error', text:'Something went wrong'});
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
    $('#employeeModalTitle').text('Edit Employee');

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
      title:'Delete?',
      text:'This will remove the employee.',
      showCancelButton:true,
      confirmButtonText:'Yes, delete'
    }).then((r)=>{
      if(!r.isConfirmed) return;

      $.ajax({
        url: "/employees/" + id,
        method: "POST",
        data: {_method:"DELETE"},
        success: function(){
          table.ajax.reload(null,false);
          Swal.fire({icon:'success', title:'Deleted', timer:1200, showConfirmButton:false});
        },
        error: function(){
          Swal.fire({icon:'error', title:'Error', text:'Could not delete'});
        }
      });
    });
  });
</script>
@endpush