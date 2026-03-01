<section class="profile-section">
    <header class="panel-header">
        <div class="panel-copy">
            <h2 class="panel-title">{{ __('Update Password') }}</h2>
            <div class="panel-subtitle">{{ __('Ensure your account is using a long, random password to stay secure.') }}</div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form-stack">
        @csrf
        @method('put')

        <div class="profile-form-grid">
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" />
                <x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="profile-form-span-2">
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="profile-form-actions">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="profile-saved-note"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
