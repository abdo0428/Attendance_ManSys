<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger ui-button']) }}>
    {{ $slot }}
</button>
