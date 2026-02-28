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
  <title>{{ $appName }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif
  <style>
    body {
      font-family: 'Manrope', sans-serif;
      background: radial-gradient(circle at 20% 20%, #edf2ff, transparent 50%),
                  radial-gradient(circle at 80% 0%, #fff7ed, transparent 45%),
                  #f8fafc;
    }
    .hero {
      padding: 80px 0 60px;
    }
    .hero-card {
      background: linear-gradient(135deg, #111827, #1f2937);
      color: #f9fafb;
      border-radius: 24px;
      padding: 32px;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
    }
    .badge-pill {
      background: #fff;
      color: #111827;
      border-radius: 999px;
      padding: 6px 14px;
      font-size: 0.85rem;
      font-weight: 600;
    }
    .feature-card {
      background: #fff;
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
      height: 100%;
    }
    .how-step {
      background: #fff;
      border-radius: 16px;
      padding: 18px;
      border: 1px solid #e5e7eb;
      height: 100%;
    }
    .cta-strip {
      background: #111827;
      color: #fff;
      border-radius: 20px;
      padding: 24px;
    }
    footer a { color: #475569; text-decoration: none; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">{{ $appName }}</a>
      <div class="d-flex gap-2">
        @if (Route::has('login'))
          <a class="btn btn-outline-dark" href="{{ route('login') }}">{{ __('app.btn_login') }}</a>
        @endif
        @if (Route::has('register'))
          <a class="btn btn-dark" href="{{ route('register') }}">{{ __('app.btn_register') }}</a>
        @endif
      </div>
    </div>
  </nav>

  <section class="hero">
    <div class="container">
      <div class="row g-4 align-items-center">
        <div class="col-lg-6">
          <div class="d-flex gap-2 mb-3">
            <span class="badge-pill">{{ __('app.landing_badge') }}</span>
          </div>
          <h1 class="display-5 fw-bold mb-3">{{ __('app.landing_title') }}</h1>
          <p class="text-muted fs-5 mb-4">{{ __('app.landing_subtitle') }}</p>
          <div class="d-flex gap-2">
            <a class="btn btn-dark btn-lg" href="{{ route('register') }}">{{ __('app.btn_get_started') }}</a>
            <a class="btn btn-outline-dark btn-lg" href="{{ route('login') }}">{{ __('app.btn_login') }}</a>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="hero-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <div class="fw-bold">{{ __('app.landing_preview_title') }}</div>
                <div class="text-white-50">{{ __('app.landing_preview_subtitle') }}</div>
              </div>
              <span class="badge bg-success">Live</span>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <div class="p-3 rounded bg-white bg-opacity-10">
                  <div class="small text-white-50">{{ __('app.card_active_employees') }}</div>
                  <div class="fs-4 fw-bold">128</div>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 rounded bg-white bg-opacity-10">
                  <div class="small text-white-50">{{ __('app.card_today_checkins') }}</div>
                  <div class="fs-4 fw-bold">94</div>
                </div>
              </div>
              <div class="col-12">
                <div class="p-3 rounded bg-white bg-opacity-10">
                  <div class="small text-white-50">{{ __('app.recent_logs') }}</div>
                  <div class="d-flex justify-content-between">
                    <span>Hala M.</span>
                    <span>09:12</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Omar S.</span>
                    <span>09:18</span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Judy K.</span>
                    <span>09:21</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-4">
          <div class="feature-card">
            <div class="mb-2">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M4 12h16M12 4v16" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <h5 class="fw-bold">{{ __('app.feature_fast') }}</h5>
            <p class="text-muted">{{ __('app.feature_fast_desc') }}</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="mb-2">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="4" width="18" height="14" rx="3" stroke="#111827" stroke-width="2"/>
                <path d="M7 8h6M7 12h10" stroke="#111827" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <h5 class="fw-bold">{{ __('app.feature_clear') }}</h5>
            <p class="text-muted">{{ __('app.feature_clear_desc') }}</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="feature-card">
            <div class="mb-2">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M7 12l3 3 7-7" stroke="#111827" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="9" stroke="#111827" stroke-width="2"/>
              </svg>
            </div>
            <h5 class="fw-bold">{{ __('app.feature_ready') }}</h5>
            <p class="text-muted">{{ __('app.feature_ready_desc') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">{{ __('app.how_it_works') }}</h3>
        <span class="text-muted">{{ __('app.three_steps') }}</span>
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="how-step">
            <div class="fw-bold">1. {{ __('app.step_add_employee') }}</div>
            <p class="text-muted mb-0">{{ __('app.step_add_employee_desc') }}</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="how-step">
            <div class="fw-bold">2. {{ __('app.step_check_in_out') }}</div>
            <p class="text-muted mb-0">{{ __('app.step_check_in_out_desc') }}</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="how-step">
            <div class="fw-bold">3. {{ __('app.step_monthly_report') }}</div>
            <p class="text-muted mb-0">{{ __('app.step_monthly_report_desc') }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <div class="cta-strip d-flex flex-column flex-lg-row justify-content-between align-items-center gap-3">
        <div>
          <h4 class="fw-bold mb-1">{{ __('app.cta_title') }}</h4>
          <div class="text-white-50">{{ __('app.cta_subtitle') }}</div>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-light" href="{{ route('register') }}">{{ __('app.btn_get_started') }}</a>
          <a class="btn btn-outline-light" href="{{ route('login') }}">{{ __('app.btn_login') }}</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="py-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <div class="text-muted">© {{ date('Y') }} {{ $appName }}</div>
      <div class="d-flex gap-3">
        <a href="{{ route('support') }}">{{ __('app.support') }}</a>
        <a href="{{ route('privacy') }}">{{ __('app.privacy') }}</a>
      </div>
    </div>
  </footer>
</body>
</html>

