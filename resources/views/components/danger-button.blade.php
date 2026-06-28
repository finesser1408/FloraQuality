<button {{ $attributes->merge(['type' => 'button']) }}
    class="btn btn-danger {{ $attributes->get('class') }}">
    {{ $slot }}
</button>
