<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary ui-button']) }}>
    {{ $slot }}
</button>
