@extends('layouts.app2')

@section('content')
<h3 class="mb-3">{{ __('app.audit_title') }}</h3>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>{{ __('app.th_time') }}</th>
            <th>{{ __('app.th_user') }}</th>
            <th>{{ __('app.th_action') }}</th>
            <th>{{ __('app.th_model') }}</th>
            <th>{{ __('app.th_details') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $log->user?->name ?? '-' }}</td>
              <td>{{ $log->action }}</td>
              <td>{{ $log->model_type ? class_basename($log->model_type).' #'.$log->model_id : '-' }}</td>
              <td>
                @if($log->meta)
                  <pre class="small mb-0">{{ json_encode($log->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                  -
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-muted">{{ __('app.empty_audit') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $logs->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection

