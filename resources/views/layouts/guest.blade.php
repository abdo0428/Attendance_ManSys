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
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $appName }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @if($isRtl)
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        @else
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-body font-sans">
        <div class="auth-page">
            <aside class="auth-side">
                <a class="auth-brand-lockup" href="{{ route('landing') }}">
                    <span class="brand-mark">AL</span>
                    <span class="auth-brand-copy">
                        <span class="brand-name">{{ $appName }}</span>
                        <span class="brand-subtitle">{{ __('app.attendance_control_panel') }}</span>
                    </span>
                </a>

                <span class="brand-badge">{{ __('app.attendance_control_panel') }}</span>
                <h1 class="auth-hero-title">{{ __('app.landing_title') }}</h1>
                <p class="auth-hero-copy">{{ __('app.landing_subtitle') }}</p>

                <div class="auth-feature-list">
                    <div class="auth-feature-item">
                        <span class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 6v6l4 2"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                            </svg>
                        </span>
                        <div>
                            <strong>{{ __('app.feature_fast') }}</strong>
                            <span>{{ __('app.feature_fast_desc') }}</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <span class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 19h16"></path>
                                <path d="M7 15V9"></path>
                                <path d="M12 15V5"></path>
                                <path d="M17 15v-3"></path>
                            </svg>
                        </span>
                        <div>
                            <strong>{{ __('app.feature_clear') }}</strong>
                            <span>{{ __('app.feature_clear_desc') }}</span>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <span class="auth-feature-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 3l7 4v5c0 5-3.5 8.5-7 9-3.5-.5-7-4-7-9V7l7-4z"></path>
                                <path d="m9.5 12 1.8 1.8 3.2-3.6"></path>
                            </svg>
                        </span>
                        <div>
                            <strong>{{ __('app.feature_ready') }}</strong>
                            <span>{{ __('app.feature_ready_desc') }}</span>
                        </div>
                    </div>
                </div>

                <div class="auth-side-footer">
                    <a href="{{ route('landing') }}" class="auth-side-link">{{ __('app.btn_back_home') }}</a>
                </div>
            </aside>

            <section class="auth-form-shell">
                <div class="auth-locale-switch">
                    <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="auth-locale-link {{ $currentLocale === 'en' ? 'active' : '' }}">{{ __('app.lang_english') }}</a>
                    <a href="{{ route('locale.switch', ['locale' => 'ar']) }}" class="auth-locale-link {{ $currentLocale === 'ar' ? 'active' : '' }}">{{ __('app.lang_arabic') }}</a>
                    <a href="{{ route('locale.switch', ['locale' => 'tr']) }}" class="auth-locale-link {{ $currentLocale === 'tr' ? 'active' : '' }}">{{ __('app.lang_turkish') }}</a>
                </div>

                <div class="auth-card">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </body>
</html>
