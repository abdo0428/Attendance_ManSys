<x-guest-layout>
    <div class="auth-panel-header">
        <div class="page-eyebrow">{{ __('Profile') }}</div>
        <h2 class="auth-title">{{ __('Verify your email') }}</h2>
        <p class="auth-lead">{{ __('Complete verification to activate your workspace.') }}</p>
    </div>

    <div class="auth-status-card">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-status-card auth-status-success">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="auth-inline-actions">
        <form method="POST" action="{{ route('verification.send') }}" class="w-100">
            @csrf
            <x-primary-button class="w-100 justify-content-center">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="w-100">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
