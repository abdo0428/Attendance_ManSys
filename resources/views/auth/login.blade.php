<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('app.btn_login') }}</div>
        <h2 class="auth-title">{{ __('Sign in to your workspace') }}</h2>
        <p class="auth-lead">{{ __('Use your company account to access attendance, employees, and reports.') }}</p>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-row auth-row-between">
            <label for="remember_me" class="auth-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="auth-submit">
            <x-primary-button class="w-100 justify-content-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="auth-form-footer">
        <span>{{ __('Need an account?') }}</span>
        <a class="auth-link" href="{{ route('register') }}">{{ __('Register') }}</a>
    </div>
</x-guest-layout>
