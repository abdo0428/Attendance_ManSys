@extends('layouts.app2')

@section('title', __('app.profile'))

@section('content')
@php
  $user = auth()->user();
  $roleName = $user?->roles?->first()?->name ?? 'viewer';
  $roleKey = 'app.role_'.str_replace('-', '_', $roleName);
  $roleLabel = trans()->has($roleKey) ? __($roleKey) : ucwords(str_replace('-', ' ', $roleName));
  $initials = collect(explode(' ', (string) $user?->name))
    ->filter()
    ->take(2)
    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
    ->implode('');
@endphp

<div class="page-header">
    <div>
        <div class="page-eyebrow">{{ __('app.section_overview') }}</div>
        <h1 class="page-title">{{ __('app.profile') }}</h1>
        <div class="page-subtitle">{{ __('Manage your personal account settings and security.') }}</div>
    </div>

    <div class="page-actions">
        <a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">{{ __('app.dashboard_title') }}</a>
    </div>
</div>

<section class="profile-overview-card">
    <div class="profile-overview-main">
        <div class="profile-avatar">{{ $initials ?: 'U' }}</div>
        <div>
            <div class="page-eyebrow">{{ __('Account overview') }}</div>
            <h2 class="panel-title">{{ $user?->name }}</h2>
            <div class="panel-subtitle">{{ __('Review your account identity, role, and sign-in email.') }}</div>
        </div>
    </div>

    <div class="profile-summary-grid">
        <div class="mini-stat">
            <div class="mini-stat-label">{{ __('app.th_role') }}</div>
            <div class="mini-stat-value">{{ $roleLabel }}</div>
        </div>

        <div class="mini-stat">
            <div class="mini-stat-label">{{ __('app.th_email') }}</div>
            <div class="mini-stat-value profile-summary-text">{{ $user?->email }}</div>
        </div>

        <div class="mini-stat">
            <div class="mini-stat-label">{{ __('app.default_language') }}</div>
            <div class="mini-stat-value">{{ strtoupper(app()->getLocale()) }}</div>
        </div>
    </div>
</section>

<div class="content-grid-2 profile-panels">
    <section class="table-panel profile-panel">
        @include('profile.partials.update-profile-information-form')
    </section>

    <section class="table-panel profile-panel">
        @include('profile.partials.update-password-form')
    </section>
</div>

<section class="table-panel profile-panel profile-panel-danger">
    @include('profile.partials.delete-user-form')
</section>
@endsection
