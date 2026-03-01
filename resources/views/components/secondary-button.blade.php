<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary ui-button']) }}>
    {{ $slot }}
</button>
