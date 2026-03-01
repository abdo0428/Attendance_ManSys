<!doctype html>
@php
  $currentLocale = app()->getLocale();
  $isRtl = $currentLocale === 'ar';
  $appName = \App\Models\Setting::getValue('company_name', config('app.name', 'Attendance Lite'));
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('app.privacy') }} - {{ $appName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="public-body">
  <div class="container py-5" style="max-width: 860px;">
    <div class="public-card">
      <div class="mb-3">
        <span class="badge text-bg-light">Privacy</span>
      </div>
      <h2 class="mb-3">{{ __('app.privacy') }}</h2>
      <p class="text-muted fs-5">{{ __('app.privacy_text') }}</p>
      <a class="btn btn-primary" href="{{ route('landing') }}">{{ __('app.btn_back_home') }}</a>
    </div>
  </div>
</body>
</html>

