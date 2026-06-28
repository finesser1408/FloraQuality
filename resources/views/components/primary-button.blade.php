<button {{ $attributes->merge(['type' => 'button']) }}
    class="btn btn-primary {{ $attributes->get('class') }}">
    {{ $slot }}
</button>
