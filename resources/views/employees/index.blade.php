@extends('layouts.app2')

@section('title', __('app.employees_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_company_area') }}</div>
    <h1 class="page-title">{{ __('app.employees_title') }}</h1>
    <div class="page-subtitle">{{ __('app.employees_subtitle') }}</div>
  </div>

  <div class="page-actions">
    @can('employees.create')
      <button class="btn btn-primary" id="btnAdd">+ {{ __('app.btn_add_employee') }}</button>
    @endcan
  </div>
</section>

<section class="filter-card">
  <div class="filter-grid">
    <div class="field-span-5">
      <label class="form-label">{{ __('app.label_search') }}</label>
      <input type="search" class="form-control" id="employeeSearch" placeholder="{{ __('app.employees_search_placeholder') }}" value="{{ $initialSearch }}">
    </div>

    <div class="field-span-3">
      <label class="form-label">{{ __('app.th_status') }}</label>
      <select class="form-select" id="filterStatus">
        <option value="">{{ __('app.filter_all') }}</option>
        <option value="active">{{ __('app.status_active') }}</option>
        <option value="inactive">{{ __('app.status_inactive') }}</option>
      </select>
    </div>

    <div class="field-span-4">
      <label class="form-label">{{ __('app.employees_status_guide') }}</label>
      <div class="d-flex flex-wrap gap-2">
        <span class="ui-badge badge-success"><span class="badge-dot"></span>{{ __('app.status_active') }}</span>
        <span class="ui-badge badge-danger"><span class="badge-dot"></span>{{ __('app.status_inactive') }}</span>
        <span class="ui-badge badge-warning"><span class="badge-dot"></span>{{ __('app.status_missing_checkout') }}</span>
      </div>
    </div>
  </div>
</section>

<section class="table-panel position-relative">
  <div class="table-header">
    <div class="panel-copy">
      <h2 class="panel-title">{{ __('app.employees_directory') }}</h2>
      <div class="panel-subtitle">{{ __('app.employees_directory_subtitle') }}</div>
    </div>
  </div>

  <div class="loading-overlay d-none" id="empLoading">
    <div class="skeleton-card">
      <div class="skeleton-line w-75"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line w-50"></div>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table align-middle" id="employeesTable" style="width:100%">
      <thead>
        <tr>
          <th>{{ __('app.employee_id') }}</th>
          <th>{{ __('app.th_full_name') }}</th>
          <th>{{ __('app.department_job') }}</th>
          <th>{{ __('app.th_status') }}</th>
          <th>{{ __('app.today_label') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
    </table>
  </div>

  <div class="empty-state mt-3 d-none" id="empEmpty">
    <span class="empty-state-icon">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
        <path d="M19 8h4M21 6v4"></path>
      </svg>
    </span>
    <div class="empty-state-title">{{ __('app.empty_employees') }}</div>
    <div class="empty-state-text">{{ __('app.employees_empty_hint') }}</div>
    @can('employees.create')
      <button class="btn btn-primary mt-3" id="btnEmptyAdd">{{ __('app.btn_add_employee') }}</button>
    @endcan
  </div>
</section>

<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="employeeModalTitle">{{ __('app.modal_add_employee') }}</h5>
          <div class="panel-subtitle">{{ __('app.employee_modal_subtitle') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="employeeForm">
        <div class="modal-body">
          <input type="hidden" id="employee_id">

          <div class="filter-grid">
            <div class="field-span-6">
              <label class="form-label">{{ __('app.th_full_name') }}</label>
              <input type="text" class="form-control" id="full_name" required>
              <div class="error-text" id="err_full_name"></div>
            </div>

            <div class="field-span-6">
              <label class="form-label">{{ __('app.th_email') }}</label>
              <input type="email" class="form-control" id="email">
              <div class="error-text" id="err_email"></div>
            </div>

            <div class="field-span-6">
              <label class="form-label">{{ __('app.th_phone') }}</label>
              <input type="text" class="form-control" id="phone">
              <div class="error-text" id="err_phone"></div>
            </div>

            <div class="field-span-6">
              <label class="form-label">{{ __('app.department_job') }}</label>
              <input type="text" class="form-control" id="job_title">
              <div class="error-text" id="err_job_title"></div>
            </div>

            <div class="field-span-4">
              <label class="form-label">{{ __('app.th_status') }}</label>
              <select class="form-select" id="is_active">
                <option value="1">{{ __('app.status_active') }}</option>
                <option value="0">{{ __('app.status_inactive') }}</option>
              </select>
              <div class="error-text" id="err_is_active"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('app.btn_close') }}</button>
          <button class="btn btn-primary" id="btnSave">{{ __('app.btn_save') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
  const employeeInitialSearch = @json($initialSearch);
  const employeeNoContact = @json(__('app.no_contact_details'));
  const employeeAssignedDepartment = @json(__('app.assigned_department_job'));
  const employeeNotAssigned = @json(__('app.not_assigned'));

  function clearErrors(){
    ['full_name','email','phone','job_title','is_active'].forEach((field) => {
      $('#err_' + field).text('');
    });
  }

  function fillForm(employee){
    $('#employee_id').val(employee.id);
    $('#full_name').val(employee.full_name);
    $('#email').val(employee.email || '');
    $('#phone').val(employee.phone || '');
    $('#job_title').val(employee.job_title || '');
    $('#is_active').val(employee.is_active ? 1 : 0);
  }

  const employeeTable = $('#employeesTable').DataTable({
    processing: true,
    serverSide: true,
    searchDelay: 300,
    ajax: {
      url: "{{ route('employees.data') }}",
      data: function(data){
        data.status = $('#filterStatus').val();
      }
    },
    columns: [
      {data: 'employee_code', name: 'id'},
      {
        data: 'full_name',
        name: 'full_name',
        render: function(data, type, row){
          const email = row.email ? `<div class="panel-subtitle">${row.email}</div>` : '';
          const phone = row.phone ? `<div class="panel-subtitle">${row.phone}</div>` : '';
          return `
            <div class="detail-stack">
              <div class="fw-semibold">${row.full_name}</div>
              ${email || phone || `<div class="panel-subtitle">${employeeNoContact}</div>`}
            </div>
          `;
        }
      },
      {
        data: 'job_title',
        name: 'job_title',
        render: function(data){
          return data
            ? `<div class="detail-stack"><span class="fw-semibold">${data}</span><span class="panel-subtitle">${employeeAssignedDepartment}</span></div>`
            : `<span class="panel-subtitle">${employeeNotAssigned}</span>`;
        }
      },
      {data: 'status_badge', name: 'status', orderable: false, searchable: false},
      {data: 'today_status_badge', name: 'today_status', orderable: false, searchable: false},
      {data: 'actions', orderable: false, searchable: false},
    ],
    order: [[1, 'asc']]
  });

  employeeTable.on('processing.dt', function(event, settings, processing){
    $('#empLoading').toggleClass('d-none', !processing);
  });

  employeeTable.on('draw.dt', function(){
    const hasRows = employeeTable.data().any();
    $('#empEmpty').toggleClass('d-none', hasRows);
  });

  $('#filterStatus').on('change', function(){
    employeeTable.ajax.reload();
  });

  $('#employeeSearch').on('input', function(){
    employeeTable.search(this.value).draw();
  });

  if (employeeInitialSearch) {
    employeeTable.search(employeeInitialSearch).draw();
  }

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#employeeSearch').val(query);
    employeeTable.search(query).draw();
  });

  $('#btnAdd, #btnEmptyAdd').on('click', function(){
    clearErrors();
    $('#employeeModalTitle').text("{{ __('app.modal_add_employee') }}");
    $('#employeeForm')[0].reset();
    $('#employee_id').val('');
    employeeModal.show();
  });

  $('#employeeForm').on('submit', function(event){
    event.preventDefault();
    clearErrors();

    const employeeId = $('#employee_id').val();
    const payload = {
      full_name: $('#full_name').val(),
      email: $('#email').val(),
      phone: $('#phone').val(),
      job_title: $('#job_title').val(),
      is_active: $('#is_active').val()
    };

    let url = "{{ route('employees.store') }}";
    let method = "POST";

    if (employeeId) {
      url = `/employees/${employeeId}`;
      payload._method = 'PUT';
    }

    $('#btnSave').prop('disabled', true);

    $.ajax({
      url: url,
      method: method,
      data: payload,
      success: function(){
        employeeModal.hide();
        employeeTable.ajax.reload(null, false);
        showToast('success', "{{ __('app.toast_saved') }}");
      },
      error: function(xhr){
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors || {};
          Object.keys(errors).forEach((key) => $('#err_' + key).text(errors[key][0]));
          return;
        }

        showToast('error', "{{ __('app.toast_error') }}");
      },
      complete: function(){
        $('#btnSave').prop('disabled', false);
      }
    });
  });

  $(document).on('click', '.btn-edit', function(){
    const employeeId = $(this).data('id');
    clearErrors();
    $('#employeeModalTitle').text("{{ __('app.modal_edit_employee') }}");

    $.get(`/employees/${employeeId}`, function(response){
      fillForm(response.employee);
      employeeModal.show();
    });
  });

  $(document).on('click', '.btn-delete', function(){
    const employeeId = $(this).data('id');

    Swal.fire({
      icon: 'warning',
      title: "{{ __('app.confirm_delete') }}",
      text: "{{ __('app.confirm_delete_employee') }}",
      showCancelButton: true,
      confirmButtonText: "{{ __('app.btn_delete') }}"
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      $.ajax({
        url: `/employees/${employeeId}`,
        method: 'POST',
        data: {_method: 'DELETE'},
        success: function(){
          employeeTable.ajax.reload(null, false);
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
