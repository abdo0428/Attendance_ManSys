<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('Confirm Password') }}</div>
        <h2 class="auth-title">{{ __('Confirm your identity') }}</h2>
        <p class="auth-lead">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form-grid">
        @csrf

        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="auth-submit">
            <x-primary-button class="w-100 justify-content-center">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
