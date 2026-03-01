@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'auth-status-card auth-status-success']) }}>
        {{ $status }}
    </div>
@endif
