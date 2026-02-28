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
  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 800px;">
    <h2 class="mb-3">{{ __('app.privacy') }}</h2>
    <p class="text-muted">{{ __('app.privacy_text') }}</p>
    <a class="btn btn-dark" href="{{ route('landing') }}">{{ __('app.btn_back_home') }}</a>
  </div>
</body>
</html>

