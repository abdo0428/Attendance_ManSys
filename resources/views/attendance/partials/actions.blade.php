<div class="btn-group">
  @can('attendance.update')
    <button class="btn btn-sm btn-outline-primary btn-log-edit" data-id="{{ $r->id }}">{{ __('app.btn_edit') }}</button>
  @endcan
  @can('attendance.delete')
    <button class="btn btn-sm btn-outline-danger btn-log-delete" data-id="{{ $r->id }}">{{ __('app.btn_delete') }}</button>
  @endcan
</div>
