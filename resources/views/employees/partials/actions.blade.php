<div class="btn-group">
  @can('employees.update')
    <button class="btn btn-sm btn-outline-primary btn-edit" data-id="{{ $e->id }}">{{ __('app.btn_edit') }}</button>
  @endcan

  @can('employees.delete')
    <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $e->id }}">{{ __('app.btn_delete') }}</button>
  @endcan
</div>
