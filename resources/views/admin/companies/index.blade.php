@extends('layouts.app2')

@section('title', __('app.companies_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_super_admin') }}</div>
    <h1 class="page-title">{{ __('app.companies_title') }}</h1>
    <div class="page-subtitle">{{ __('app.companies_subtitle') }}</div>
  </div>
</section>

<section class="table-panel">
  <div class="table-header">
    <div class="panel-copy">
      <h2 class="panel-title">{{ __('app.companies_directory') }}</h2>
      <div class="panel-subtitle">{{ __('app.companies_directory_subtitle') }}</div>
    </div>
  </div>

  <div class="datatable-toolbar">
    <div class="toolbar-search">
      <input type="search" class="form-control" id="companiesSearch" placeholder="{{ __('app.companies_search_placeholder') }}" value="{{ request('search', '') }}">
    </div>
  </div>

  <div class="table-wrapper">
    <table class="table align-middle" id="companiesTable">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('app.company_name') }}</th>
          <th>{{ __('app.th_owner') }}</th>
          <th>{{ __('app.th_users') }}</th>
          <th>{{ __('app.th_employees') }}</th>
          <th>{{ __('app.th_created') }}</th>
          <th>{{ __('app.th_actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($companies as $company)
          <tr data-search="{{ strtolower($company->name.' '.($company->owner?->name ?? '')) }}">
            <td>{{ ($companies->firstItem() ?? 0) + $loop->index }}</td>
            <td class="fw-semibold">{{ $company->name }}</td>
            <td>{{ $company->owner?->name ?? '-' }}</td>
            <td>{{ $company->users_count }}</td>
            <td>{{ $company->employees_count }}</td>
            <td>{{ $company->created_at?->format('Y-m-d') }}</td>
            <td>
              <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.companies.show', $company) }}">
                {{ __('app.btn_view') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-muted">{{ __('app.empty_companies') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $companies->links('pagination::bootstrap-5') }}
  </div>
</section>
@endsection

@push('scripts')
<script>
  $('#companiesSearch').on('input', function(){
    const query = ($(this).val() || '').trim().toLowerCase();

    $('#companiesTable tbody tr').each(function(){
      const haystack = ($(this).data('search') || '').toString();
      $(this).toggle(!query || haystack.includes(query));
    });
  });

  window.addEventListener('app:topbar-search', function(event){
    window.__topbarSearchHandled = true;
    const query = event.detail?.query || '';
    $('#companiesSearch').val(query).trigger('input');
  });

  if ($('#companiesSearch').val()) {
    $('#companiesSearch').trigger('input');
  }
</script>
@endpush
