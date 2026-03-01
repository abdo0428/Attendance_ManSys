@extends('layouts.app2')

@section('title', __('app.users_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_company_area') }}</div>
    <h1 class="page-title">{{ __('app.users_title') }}</h1>
    <div class="page-subtitle">{{ __('app.users_page_subtitle') }}</div>
  </div>
</section>

<section class="content-grid">
  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.users_create_title') }}</h2>
          <div class="panel-subtitle">{{ __('app.users_create_subtitle') }}</div>
        </div>
      </div>

      <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="filter-grid">
          <div class="field-span-6">
            <label class="form-label">{{ __('app.th_name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="error-text">{{ $message }}</div> @enderror
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.th_email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <div class="error-text">{{ $message }}</div> @enderror
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.th_role') }}</label>
            <select name="role" class="form-select" required>
              <option value="" disabled @selected(old('role') === null)>{{ __('app.th_role') }}</option>
              @foreach($roles as $role)
                @php
                  $roleKey = 'app.role_'.str_replace('-', '_', $role->name);
                  $roleLabel = trans()->has($roleKey) ? __($roleKey) : ucfirst($role->name);
                @endphp
                <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $roleLabel }}</option>
              @endforeach
            </select>
            @error('role') <div class="error-text">{{ $message }}</div> @enderror
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.password_label') }}</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <div class="error-text">{{ $message }}</div> @enderror
          </div>

          <div class="field-span-6">
            <label class="form-label">{{ __('app.password_confirm_label') }}</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>

          <div class="field-span-12">
            <button class="btn btn-primary">{{ __('app.btn_save') }}</button>
          </div>
        </div>
      </form>
    </div>
  </article>

  <article class="surface-card">
    <div class="surface-card-body">
      <div class="panel-header">
        <div class="panel-copy">
          <h2 class="panel-title">{{ __('app.users_company_users') }}</h2>
          <div class="panel-subtitle">{{ __('app.users_company_users_subtitle') }}</div>
        </div>
      </div>

      <div class="datatable-toolbar">
        <div class="toolbar-search">
          <input type="search" class="form-control" id="usersSearch" placeholder="{{ __('app.users_search_placeholder') }}" value="{{ request('search', '') }}">
        </div>
      </div>

      <div class="table-wrapper">
        <table class="table align-middle" id="usersTable">
          <thead>
            <tr>
              <th>{{ __('app.th_name') }}</th>
              <th>{{ __('app.th_email') }}</th>
              <th>{{ __('app.th_role') }}</th>
              <th>{{ __('app.th_status') }}</th>
              <th>{{ __('app.th_created') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $user)
              @php
                $role = $user->roles->first()?->name ?? 'viewer';
                $roleTone = $role === 'admin' ? 'primary' : ($role === 'hr' ? 'info' : 'neutral');
                $roleKey = 'app.role_'.str_replace('-', '_', $role);
                $roleLabel = trans()->has($roleKey) ? __($roleKey) : ucfirst($role);
              @endphp
              <tr data-search="{{ strtolower($user->name.' '.$user->email) }}">
                <td class="fw-semibold">{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="ui-badge badge-{{ $roleTone }}"><span class="badge-dot"></span>{{ $roleLabel }}</span>
                    <select class="form-select form-select-sm role-select" data-user-id="{{ $user->id }}" style="min-width: 150px;">
                      @foreach($roles as $roleOption)
                        @php
                          $optionKey = 'app.role_'.str_replace('-', '_', $roleOption->name);
                          $optionLabel = trans()->has($optionKey) ? __($optionKey) : ucfirst($roleOption->name);
                        @endphp
                        <option value="{{ $roleOption->name }}" @selected($role === $roleOption->name)>{{ $optionLabel }}</option>
                      @endforeach
                    </select>
                  </div>
                </td>
                <td><span class="ui-badge badge-success"><span class="badge-dot"></span>{{ __('app.status_active') }}</span></td>
                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-muted">{{ __('app.empty_users') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </article>
</section>
@endsection

@push('scripts')
<script>
  $('#usersSearch').on('input', function(){
    const query = ($(this).val() || '').trim().toLowerCase();

    $('#usersTable tbody tr').each(function(){
      const haystack = ($(this).data('search') || '').toString();
      $(this).toggle(!query || haystack.includes(query));
    });
  });

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#usersSearch').val(query).trigger('input');
  });

  $('.role-select').on('change', function(){
    const userId = $(this).data('user-id');
    const role = $(this).val();

    $.post(`/users/${userId}/role`, {role})
      .done(function(){
        showToast('success', "{{ __('app.toast_saved') }}");
        window.location.reload();
      })
      .fail(function(){
        showToast('error', "{{ __('app.toast_error') }}");
      });
  });

  if ($('#usersSearch').val()) {
    $('#usersSearch').trigger('input');
  }
</script>
@endpush
