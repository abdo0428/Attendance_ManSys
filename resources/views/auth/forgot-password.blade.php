<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('Reset Password') }}</div>
        <h2 class="auth-title">{{ __('Recover access') }}</h2>
        <p class="auth-lead">{{ __('Enter your email and we will send you a reset link.') }}</p>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-submit">
            <x-primary-button class="w-100 justify-content-center">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>

    <div class="auth-form-footer">
        <a class="auth-link" href="{{ route('login') }}">{{ __('Return to login') }}</a>
    </div>
</x-guest-layout>
