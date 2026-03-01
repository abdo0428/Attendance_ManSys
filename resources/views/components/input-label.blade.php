@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label ui-label']) }}>
    {{ $value ?? $slot }}
</label>
