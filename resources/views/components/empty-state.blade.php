@props(['title', 'description' => null, 'action' => null, 'actionLabel' => null, 'actionHref' => null])
<div class="flex flex-col items-center justify-center text-center py-20 px-6">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5" style="background:rgba(0,53,128,0.06);">
        {{ $icon ?? '' }}
    </div>
    <h3 class="text-lg font-bold mb-2" style="color:var(--text-primary);">{{ $title }}</h3>
    @if($description)
        <p class="text-sm max-w-sm" style="color:var(--text-tertiary);">{{ $description }}</p>
    @endif
    @if($actionHref)
        <a href="{{ $actionHref }}" class="btn btn-primary mt-6">{{ $actionLabel }}</a>
    @endif
    {{ $slot ?? '' }}
</div>
