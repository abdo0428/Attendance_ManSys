@extends('layouts.app2')

@section('title', __('app.settings_title'))

@section('content')
<section class="page-header">
  <div>
    <div class="page-eyebrow">{{ __('app.section_company_area') }}</div>
    <h1 class="page-title">{{ __('app.settings_title') }}</h1>
    <div class="page-subtitle">{{ __('app.settings_page_subtitle') }}</div>
  </div>
</section>

<section class="surface-card">
  <div class="surface-card-body">
    <ul class="nav nav-pills settings-tabs" id="settingsTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="company-tab" data-bs-toggle="pill" data-bs-target="#company-pane" type="button" role="tab">{{ __('app.settings_company_info') }}</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#attendance-pane" type="button" role="tab">{{ __('app.settings_attendance_rules') }}</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding-pane" type="button" role="tab">{{ __('app.settings_branding') }}</button>
      </li>
    </ul>

    <form method="POST" action="{{ route('settings.update') }}">
      @csrf

      <div class="tab-content">
        <div class="tab-pane fade show active" id="company-pane" role="tabpanel" aria-labelledby="company-tab" tabindex="0">
          <div class="filter-grid">
            <div class="field-span-6">
              <label class="form-label">{{ __('app.company_name') }}</label>
              <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $settings['company_name']) }}">
              @error('company_name') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="field-span-3">
              <label class="form-label">{{ __('app.settings_default_locale') }}</label>
              <select class="form-select" name="default_locale">
                <option value="en" @selected(old('default_locale', $settings['default_locale']) === 'en')>{{ __('app.lang_english') }}</option>
                <option value="ar" @selected(old('default_locale', $settings['default_locale']) === 'ar')>{{ __('app.lang_arabic') }}</option>
                <option value="tr" @selected(old('default_locale', $settings['default_locale']) === 'tr')>{{ __('app.lang_turkish') }}</option>
              </select>
              @error('default_locale') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="field-span-3">
              <label class="form-label">{{ __('app.settings_timezone') }}</label>
              <input type="text" class="form-control" value="{{ config('app.timezone') }}" disabled>
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="attendance-pane" role="tabpanel" aria-labelledby="attendance-tab" tabindex="0">
          <div class="filter-grid">
            <div class="field-span-4">
              <label class="form-label">{{ __('app.default_work_start') }}</label>
              <input type="time" class="form-control" name="default_work_start" value="{{ old('default_work_start', $settings['default_work_start']) }}">
              @error('default_work_start') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="field-span-4">
              <label class="form-label">{{ __('app.default_work_end') }}</label>
              <input type="time" class="form-control" name="default_work_end" value="{{ old('default_work_end', $settings['default_work_end']) }}">
              @error('default_work_end') <div class="error-text">{{ $message }}</div> @enderror
            </div>

            <div class="field-span-4">
              <label class="form-label">{{ __('app.grace_minutes') }}</label>
              <input type="number" class="form-control" name="grace_minutes" min="0" max="180" value="{{ old('grace_minutes', $settings['grace_minutes']) }}">
              @error('grace_minutes') <div class="error-text">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>

        <div class="tab-pane fade" id="branding-pane" role="tabpanel" aria-labelledby="branding-tab" tabindex="0">
          <div class="settings-note">
            {{ __('app.settings_branding_note') }}
          </div>
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary">{{ __('app.btn_save_settings') }}</button>
      </div>
    </form>
  </div>
</section>
@endsection
