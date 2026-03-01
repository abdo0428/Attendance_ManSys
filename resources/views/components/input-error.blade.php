@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'error-text ui-error-list list-unstyled']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
