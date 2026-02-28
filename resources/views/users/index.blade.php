@extends('layouts.app2')

@section('content')
<h3 class="mb-3">{{ __('app.users_title') }}</h3>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>{{ __('app.th_name') }}</th>
            <th>{{ __('app.th_email') }}</th>
            <th>{{ __('app.th_role') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                <select class="form-select form-select-sm role-select" data-user-id="{{ $user->id }}">
                  @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected($user->roles->first()?->name === $role->name)>
                      {{ ucfirst($role->name) }}
                    </option>
                  @endforeach
                </select>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $('.role-select').on('change', function(){
    const userId = $(this).data('user-id');
    const role = $(this).val();

    $.post(`/users/${userId}/role`, {role})
      .done(()=> showToast('success', "{{ __('app.toast_saved') }}"))
      .fail(()=> showToast('error', "{{ __('app.toast_error') }}"));
  });
</script>
@endpush

