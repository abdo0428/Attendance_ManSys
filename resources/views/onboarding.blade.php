<!doctype html>
@php
  $currentLocale = app()->getLocale();
  $isRtl = $currentLocale === 'ar';
@endphp
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('app.onboarding_title') }}</title>
  @if($isRtl)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @endif
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5" style="max-width: 900px;">
    <div class="text-center mb-4">
      <h2 class="mb-2">{{ __('app.onboarding_title') }}</h2>
      <p class="text-muted">{{ __('app.onboarding_subtitle') }}</p>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="progress mb-4" style="height: 8px;">
          <div class="progress-bar" id="stepProgress" style="width: 33%;"></div>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" id="onboardingForm">
          @csrf

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="step" data-step="1">
            <h5 class="mb-3">{{ __('app.onboarding_step1_title') }}</h5>
            @if($hasEmployees)
              <div class="alert alert-info">{{ __('app.onboarding_employee_exists') }}</div>
            @endif
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">{{ __('app.th_full_name') }}</label>
                <input type="text" class="form-control" name="employee_full_name" placeholder="{{ __('app.example_name') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('app.th_email') }}</label>
                <input type="email" class="form-control" name="employee_email" placeholder="name@company.com">
              </div>
              <div class="col-md-6">
                <label class="form-label">{{ __('app.th_job') }}</label>
                <input type="text" class="form-control" name="employee_job_title" placeholder="{{ __('app.example_job') }}">
              </div>
            </div>
          </div>

          <div class="step d-none" data-step="2">
            <h5 class="mb-3">{{ __('app.onboarding_step2_title') }}</h5>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">{{ __('app.default_work_start') }}</label>
                <input type="time" class="form-control" name="default_work_start" value="{{ $defaults['default_work_start'] }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ __('app.default_work_end') }}</label>
                <input type="time" class="form-control" name="default_work_end" value="{{ $defaults['default_work_end'] }}" required>
              </div>
            </div>
          </div>

          <div class="step d-none" data-step="3">
            <h5 class="mb-3">{{ __('app.onboarding_step3_title') }}</h5>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">{{ __('app.default_language') }}</label>
                <select class="form-select" name="default_locale" required>
                  <option value="en" @selected($defaults['default_locale'] === 'en')>English</option>
                  <option value="ar" @selected($defaults['default_locale'] === 'ar')>العربية</option>
                  <option value="tr" @selected($defaults['default_locale'] === 'tr')>Türkçe</option>
                </select>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary" id="btnPrev" disabled>{{ __('app.btn_back') }}</button>
            <button type="button" class="btn btn-primary" id="btnNext">{{ __('app.btn_next') }}</button>
            <button type="submit" class="btn btn-success d-none" id="btnFinish">{{ __('app.btn_finish') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentStep = 1;
    const totalSteps = 3;

    function updateStep(){
      document.querySelectorAll('.step').forEach(step => {
        step.classList.toggle('d-none', parseInt(step.dataset.step) !== currentStep);
      });

      document.getElementById('btnPrev').disabled = currentStep === 1;
      document.getElementById('btnNext').classList.toggle('d-none', currentStep === totalSteps);
      document.getElementById('btnFinish').classList.toggle('d-none', currentStep !== totalSteps);

      const progress = Math.round((currentStep / totalSteps) * 100);
      document.getElementById('stepProgress').style.width = progress + '%';
    }

    document.getElementById('btnNext').addEventListener('click', function(){
      if(currentStep < totalSteps){
        currentStep++;
        updateStep();
      }
    });

    document.getElementById('btnPrev').addEventListener('click', function(){
      if(currentStep > 1){
        currentStep--;
        updateStep();
      }
    });

    updateStep();
  </script>
</body>
</html>


