<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('Reset Password') }}</div>
        <h2 class="auth-title">{{ __('Choose a new password') }}</h2>
        <p class="auth-lead">{{ __('Secure your account with a fresh password.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="auth-form-grid auth-form-grid-2">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field auth-span-2">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="auth-submit auth-span-2">
            <x-primary-button class="w-100 justify-content-center">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
