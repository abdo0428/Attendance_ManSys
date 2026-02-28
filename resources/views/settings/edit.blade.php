@extends('layouts.app2')

@section('content')
<h3 class="mb-3">{{ __('app.settings_title') }}</h3>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="{{ route('settings.update') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">{{ __('app.company_name') }}</label>
          <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $settings['company_name']) }}">
          @error('company_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">{{ __('app.default_work_start') }}</label>
          <input type="time" class="form-control" name="default_work_start" value="{{ old('default_work_start', $settings['default_work_start']) }}">
          @error('default_work_start') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">{{ __('app.default_work_end') }}</label>
          <input type="time" class="form-control" name="default_work_end" value="{{ old('default_work_end', $settings['default_work_end']) }}">
          @error('default_work_end') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">{{ __('app.grace_minutes') }}</label>
          <input type="number" class="form-control" name="grace_minutes" min="0" max="180" value="{{ old('grace_minutes', $settings['grace_minutes']) }}">
          @error('grace_minutes') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label class="form-label">{{ __('app.default_language') }}</label>
          <select class="form-select" name="default_locale">
            <option value="en" @selected(old('default_locale', $settings['default_locale']) === 'en')>English</option>
            <option value="ar" @selected(old('default_locale', $settings['default_locale']) === 'ar')>العربية</option>
            <option value="tr" @selected(old('default_locale', $settings['default_locale']) === 'tr')>Türkçe</option>
          </select>
          @error('default_locale') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-primary">{{ __('app.btn_save_settings') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection

