@extends('layouts.app2')

@section('content')
<h3 class="mb-3">{{ __('app.users_title') }}</h3>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="POST" action="{{ route('users.store') }}" class="row g-3">
      @csrf

      <div class="col-md-4">
        <label class="form-label">{{ __('app.th_name') }}</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-4">
        <label class="form-label">{{ __('app.th_email') }}</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-4">
        <label class="form-label">{{ __('app.th_role') }}</label>
        <select name="role" class="form-select" required>
          <option value="" disabled @selected(old('role') === null)>{{ __('app.th_role') }}</option>
          @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ ucfirst($role->name) }}</option>
          @endforeach
        </select>
        @error('role') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-4">
        <label class="form-label">{{ __('Password') }}</label>
        <input type="password" name="password" class="form-control" required>
        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-4">
        <label class="form-label">{{ __('Confirm Password') }}</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>

      <div class="col-12">
        <button class="btn btn-primary">{{ __('app.btn_save') }}</button>
      </div>
    </form>
  </div>
</div>

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

