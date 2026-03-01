<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('app.btn_register') }}</div>
        <h2 class="auth-title">{{ __('Create your company workspace') }}</h2>
        <p class="auth-lead">{{ __('Set up the company profile and create the first administrator account.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form-grid auth-form-grid-2">
        @csrf

        <div class="auth-field auth-span-2">
            <x-input-label for="company_name" :value="__('app.company_name')" />
            <x-text-input id="company_name" type="text" name="company_name" :value="old('company_name')" required autofocus autocomplete="organization" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
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

        <div class="auth-row auth-row-between auth-span-2">
            <div class="auth-form-footer auth-form-footer-inline">
                <span>{{ __('Already registered?') }}</span>
                <a class="auth-link" href="{{ route('login') }}">{{ __('Log in') }}</a>
            </div>

            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
