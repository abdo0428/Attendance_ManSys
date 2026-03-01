<div class="dropdown">
  <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
    {{ __('app.th_actions') }}
  </button>
  <ul class="dropdown-menu dropdown-menu-end">
    @can('attendance.update')
      <li>
        <button class="dropdown-item btn-log-edit" type="button" data-id="{{ $r->id }}">
          {{ __('app.btn_edit') }}
        </button>
      </li>
    @endcan
    @can('attendance.delete')
      <li>
        <button class="dropdown-item text-danger btn-log-delete" type="button" data-id="{{ $r->id }}">
          {{ __('app.btn_delete') }}
        </button>
      </li>
    @endcan
  </ul>
</div>
