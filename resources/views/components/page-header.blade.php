@props(['title', 'description' => null])
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight" style="color:var(--text-primary);">{{ $title }}</h1>
        @if($description)
            <p class="text-sm mt-1" style="color:var(--text-tertiary);">{{ $description }}</p>
        @endif
    </div>
    @if(isset($action))
        <div>{{ $action }}</div>
    @endif
</div>
