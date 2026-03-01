@extends('layouts.app2')

@section('title', __('app.audit_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_governance') }}</div>
    <h1 class="page-title">{{ __('app.audit_title') }}</h1>
    <div class="page-subtitle">{{ __('app.audit_page_subtitle') }}</div>
  </div>
</section>

<section class="filter-card">
  <form method="GET">
    <div class="filter-grid">
      <div class="field-span-4">
        <label class="form-label">{{ __('app.audit_filter_action') }}</label>
        <select class="form-select" name="action">
          <option value="">{{ __('app.audit_filter_all_actions') }}</option>
          @foreach($actions as $action)
            @php
              $actionKey = 'app.audit_action_'.str_replace('.', '_', $action);
              $actionLabel = trans()->has($actionKey) ? __($actionKey) : $action;
            @endphp
            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>{{ $actionLabel }}</option>
          @endforeach
        </select>
      </div>

      <div class="field-span-3">
        <label class="form-label">{{ __('app.audit_filter_from') }}</label>
        <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
      </div>

      <div class="field-span-3">
        <label class="form-label">{{ __('app.audit_filter_to') }}</label>
        <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
      </div>

      <div class="field-span-2">
        <button class="btn btn-primary w-100">{{ __('app.btn_apply') }}</button>
      </div>
    </div>
  </form>
</section>

<section class="table-panel">
  <div class="table-header">
    <div class="panel-copy">
      <h2 class="panel-title">{{ __('app.audit_timeline') }}</h2>
      <div class="panel-subtitle">{{ __('app.audit_timeline_subtitle') }}</div>
    </div>
  </div>

  <div class="datatable-toolbar">
    <div class="toolbar-search">
      <input type="search" class="form-control" id="auditClientSearch" placeholder="{{ __('app.audit_search_placeholder') }}" value="{{ request('search', '') }}">
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table align-middle" id="auditTable">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('app.th_time') }}</th>
          <th>{{ __('app.th_user') }}</th>
          <th>{{ __('app.th_action') }}</th>
          <th>{{ __('app.th_model') }}</th>
          <th>{{ __('app.th_details') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          @php
            $meta = $log->meta ? json_encode($log->meta, JSON_UNESCAPED_UNICODE) : '';
            $actionKey = 'app.audit_action_'.str_replace('.', '_', $log->action);
            $actionLabel = trans()->has($actionKey) ? __($actionKey) : $log->action;
          @endphp
          <tr data-search="{{ strtolower(($log->user?->name ?? '').' '.$actionLabel.' '.($log->model_type ? class_basename($log->model_type) : '')) }}">
            <td>{{ ($logs->firstItem() ?? 0) + $loop->index }}</td>
            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $log->user?->name ?? '-' }}</td>
            <td><span class="ui-badge badge-primary"><span class="badge-dot"></span>{{ $actionLabel }}</span></td>
            <td>{{ $log->model_type ? class_basename($log->model_type).' #'.$log->model_id : '-' }}</td>
            <td>
              @if($log->meta)
                <button
                  class="btn btn-outline-secondary btn-sm btn-audit-meta"
                  type="button"
                  data-meta="{{ e($meta) }}"
                  data-title="{{ $actionLabel }}"
                >
                  {{ __('app.audit_view_json') }}
                </button>
              @else
                <span class="panel-subtitle">{{ __('app.audit_no_metadata') }}</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-muted">{{ __('app.empty_audit') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $logs->links('pagination::bootstrap-5') }}
  </div>
</section>

<div class="modal fade" id="auditMetaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title" id="auditMetaTitle">{{ __('app.audit_metadata') }}</h5>
          <div class="panel-subtitle">{{ __('app.audit_metadata_subtitle') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="audit-meta-box">
          <pre id="auditMetaContent"></pre>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const auditMetaModal = new bootstrap.Modal(document.getElementById('auditMetaModal'));
  const tAuditMetadata = @json(__('app.audit_metadata'));

  $('#auditClientSearch').on('input', function(){
    const query = ($(this).val() || '').trim().toLowerCase();

    $('#auditTable tbody tr').each(function(){
      const haystack = ($(this).data('search') || '').toString();
      $(this).toggle(!query || haystack.includes(query));
    });
  });

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#auditClientSearch').val(query).trigger('input');
  });

  $(document).on('click', '.btn-audit-meta', function(){
    const rawMeta = $(this).data('meta') || '{}';
    let formattedMeta = rawMeta;

    try {
      formattedMeta = JSON.stringify(JSON.parse(rawMeta), null, 2);
    } catch (error) {
      formattedMeta = rawMeta;
    }

    $('#auditMetaTitle').text($(this).data('title') || tAuditMetadata);
    $('#auditMetaContent').text(formattedMeta);
    auditMetaModal.show();
  });

  if ($('#auditClientSearch').val()) {
    $('#auditClientSearch').trigger('input');
  }
</script>
@endpush
