@extends('layouts.app2')

@section('title', __('app.monthly_report'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_reports') }}</div>
    <h1 class="page-title">{{ __('app.monthly_report') }}</h1>
    <div class="page-subtitle">{{ __('app.reports_page_subtitle') }}</div>
  </div>

  <div class="page-actions">
    <button class="btn btn-outline-secondary" id="btnThisMonth">{{ __('app.btn_this_month') }}</button>
    <button class="btn btn-outline-secondary" id="btnLastMonth">{{ __('app.btn_last_month') }}</button>
    <button class="btn btn-primary" id="btnLoad">{{ __('app.btn_load') }}</button>
    <button class="btn btn-success" id="btnExport">{{ __('app.btn_export_csv') }}</button>
  </div>
</section>

<section class="filter-card">
  <div class="filter-grid">
    <div class="field-span-4">
      <label class="form-label">{{ __('app.reports_search_label') }}</label>
      <input type="search" class="form-control" id="reportSearch" placeholder="{{ __('app.reports_search_placeholder') }}" value="{{ request('search', '') }}">
    </div>

    <div class="field-span-4">
      <label class="form-label">{{ __('app.employee') }}</label>
      <select class="form-select" id="rep_employee">
        <option value="">{{ __('app.filter_all_employees') }}</option>
        @foreach($employees as $employee)
          <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
        @endforeach
      </select>
    </div>

    <div class="field-span-4">
      <label class="form-label">{{ __('app.month') }}</label>
      <input type="month" class="form-control" id="rep_month" value="{{ now()->format('Y-m') }}">
    </div>
  </div>

  <div class="error-text" id="rep_err"></div>
</section>

<section class="summary-grid">
  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.total_hours') }}</div>
      <span class="metric-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 7v5l3 3"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value" id="sum_hours">00:00</div>
    <div class="metric-caption">{{ __('app.reports_total_hours_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.reports_average_employee') }}</div>
      <span class="metric-icon info">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M4 19h16"></path>
          <path d="M7 16V9"></path>
          <path d="M12 16V5"></path>
          <path d="M17 16v-3"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value" id="sum_average">00:00</div>
    <div class="metric-caption">{{ __('app.reports_average_employee_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.reports_absence_count') }}</div>
      <span class="metric-icon danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M18 6 6 18M6 6l12 12"></path>
        </svg>
      </span>
    </div>
    <div class="metric-value" id="sum_absences">0</div>
    <div class="metric-caption">{{ __('app.reports_absence_count_caption') }}</div>
  </article>

  <article class="metric-card">
    <div class="metric-head">
      <div class="metric-label">{{ __('app.reports_missing_punches') }}</div>
      <span class="metric-icon warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="12" cy="12" r="9"></circle>
          <path d="M12 8v5"></path>
          <circle cx="12" cy="16" r="1"></circle>
        </svg>
      </span>
    </div>
    <div class="metric-value" id="sum_missing">0</div>
    <div class="metric-caption">{{ __('app.reports_missing_punches_caption') }}</div>
  </article>
</section>

<section class="table-panel position-relative">
  <div class="table-header">
    <div class="panel-copy">
      <h2 class="panel-title">{{ __('app.reports_summary_table') }}</h2>
      <div class="panel-subtitle">{{ __('app.reports_summary_table_subtitle') }}</div>
    </div>
  </div>

  <div class="loading-overlay d-none" id="repLoading">
    <div class="skeleton-card">
      <div class="skeleton-line w-75"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line"></div>
      <div class="skeleton-line w-50"></div>
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table align-middle" id="reportTable">
      <thead>
        <tr>
          <th>{{ __('app.th_employee') }}</th>
          <th>{{ __('app.reports_work_days') }}</th>
          <th>{{ __('app.total_hours') }}</th>
          <th>{{ __('app.reports_absences') }}</th>
          <th>{{ __('app.reports_missing_punches') }}</th>
          <th>{{ __('app.th_overtime') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="empty-state mt-3 d-none" id="repEmpty">
    <span class="empty-state-icon">
      <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8">
        <path d="M4 19h16"></path>
        <path d="M7 16V9"></path>
        <path d="M12 16V5"></path>
        <path d="M17 16v-3"></path>
      </svg>
    </span>
    <div class="empty-state-title">{{ __('app.empty_monthly') }}</div>
    <div class="empty-state-text">{{ __('app.reports_empty_hint') }}</div>
    <button class="btn btn-primary mt-3" id="btnEmptyReload">{{ __('app.btn_load') }}</button>
  </div>
</section>

<div class="modal fade" id="reportDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="reportDetailsTitle">{{ __('app.reports_employee_details') }}</h5>
          <div class="panel-subtitle">{{ __('app.reports_employee_details_subtitle') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-wrapper">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>{{ __('app.th_date') }}</th>
                <th>{{ __('app.th_checkin') }}</th>
                <th>{{ __('app.th_checkout') }}</th>
                <th>{{ __('app.th_worked') }}</th>
                <th>{{ __('app.th_status') }}</th>
                <th>{{ __('app.th_notes') }}</th>
              </tr>
            </thead>
            <tbody id="reportDetailsBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const reportDetailsModal = new bootstrap.Modal(document.getElementById('reportDetailsModal'));
  const tEmployee = @json(__('app.employee'));
  const tViewDetails = @json(__('app.reports_view_details'));
  const tChooseMonth = @json(__('app.reports_choose_month'));
  const tNoDetailRecords = @json(__('app.reports_no_detail_records'));
  let reportRows = [];
  let visibleReportRows = [];

  function setMonthInput(date){
    $('#rep_month').val(date.toISOString().slice(0, 7));
  }

  function badge(tone, label){
    return `<span class="ui-badge badge-${tone}"><span class="badge-dot"></span>${label}</span>`;
  }

  function renderReportRows(){
    const tbody = $('#reportTable tbody');
    const query = ($('#reportSearch').val() || '').trim().toLowerCase();
    const rows = reportRows.filter((row) => row.employee_name.toLowerCase().includes(query));
    visibleReportRows = rows;

    tbody.empty();

    rows.forEach((row, index) => {
      tbody.append(`
        <tr>
          <td>
            <div class="detail-stack">
              <span class="fw-semibold">${row.employee_name}</span>
              <span class="panel-subtitle">${tEmployee} #${row.employee_id}</span>
            </div>
          </td>
          <td>${row.work_days}</td>
          <td>${row.total_hours}</td>
          <td>${row.absences}</td>
          <td>${row.missing_punches}</td>
          <td>${row.overtime}</td>
          <td>
            <button class="btn btn-outline-secondary btn-sm btn-report-details" type="button" data-index="${index}">
              ${tViewDetails}
            </button>
          </td>
        </tr>
      `);
    });

    $('#repEmpty').toggleClass('d-none', rows.length > 0);
  }

  function loadReport(){
    $('#rep_err').text('');
    $('#repLoading').removeClass('d-none');

    const employeeId = $('#rep_employee').val();
    const month = $('#rep_month').val();

    if (!month) {
      $('#rep_err').text(tChooseMonth);
      $('#repLoading').addClass('d-none');
      return;
    }

    $.post("{{ route('reports.monthly.data') }}", {
      employee_id: employeeId,
      month: month
    }).done((response) => {
      $('#sum_hours').text(response.summary.total_hours);
      $('#sum_average').text(response.summary.average_hours);
      $('#sum_absences').text(response.summary.absence_count);
      $('#sum_missing').text(response.summary.missing_punches);

      reportRows = response.rows || [];
      renderReportRows();
    }).fail((xhr) => {
      if (xhr.status === 422) {
        const message = Object.values(xhr.responseJSON.errors || {}).map((item) => item[0]).join(' | ');
        $('#rep_err').text(message);
        return;
      }

      showToast('error', "{{ __('app.toast_error') }}");
    }).always(() => {
      $('#repLoading').addClass('d-none');
    });
  }

  $('#btnThisMonth').on('click', function(){
    setMonthInput(new Date());
  });

  $('#btnLastMonth').on('click', function(){
    const date = new Date();
    date.setMonth(date.getMonth() - 1);
    setMonthInput(date);
  });

  $('#btnLoad, #btnEmptyReload').on('click', loadReport);

  $('#btnExport').on('click', function(){
    const employeeId = $('#rep_employee').val();
    const month = $('#rep_month').val();

    if (!month) {
      $('#rep_err').text(tChooseMonth);
      return;
    }

    const params = new URLSearchParams({month});
    if (employeeId) {
      params.set('employee_id', employeeId);
    }

    window.location.href = "{{ route('reports.monthly.export') }}" + '?' + params.toString();
  });

  $('#reportSearch').on('input', renderReportRows);

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#reportSearch').val(query);
    renderReportRows();
  });

  $(document).on('click', '.btn-report-details', function(){
    const index = Number($(this).data('index'));
    const row = visibleReportRows[index];
    const detailsBody = $('#reportDetailsBody');

    if (!row) {
      return;
    }

    $('#reportDetailsTitle').text(row.employee_name);
    detailsBody.empty();

    if (!row.details.length) {
      detailsBody.append(`
        <tr>
          <td colspan="6" class="text-muted">${tNoDetailRecords}</td>
        </tr>
      `);
    } else {
      row.details.forEach((detail) => {
        detailsBody.append(`
          <tr>
            <td>${detail.work_date}</td>
            <td>${detail.check_in || '-'}</td>
            <td>${detail.check_out || '-'}</td>
            <td>${detail.worked}</td>
            <td>${badge(detail.status_tone, detail.status)}</td>
            <td>${detail.notes}</td>
          </tr>
        `);
      });
    }

    reportDetailsModal.show();
  });

  loadReport();
</script>
@endpush
