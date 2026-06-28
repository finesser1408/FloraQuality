@props(['value', 'description' => null, 'icon', 'iconBg' => '#003580', 'iconColor' => '#ffffff', 'trend' => null, 'trendUp' => null])
<div class="card stat-card card-hover">
    <div class="flex items-center justify-between">
        <div class="stat-icon-wrap" style="background:{{ $iconBg }}20;">
            <svg class="w-5 h-5" fill="none" stroke="{{ $iconBg }}" stroke-width="1.75" viewBox="0 0 24 24">{!! $icon !!}</svg>
        </div>
        @if($trend !== null)
            <div class="flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full
                {{ $trendUp ? 'text-blue-600' : 'text-red-500' }}"
                style="{{ $trendUp ? 'background:rgba(0,53,128,0.08)' : 'background:rgba(220,38,38,0.08)' }}">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    @if($trendUp)
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                    @endif
                </svg>
                {{ $trend }}
            </div>
        @endif
    </div>
    <div>
        <div class="text-3xl font-extrabold tracking-tight" style="color:var(--text-primary);">{{ $value }}</div>
        @if($description)
            <div class="text-sm font-medium mt-0.5" style="color:var(--text-tertiary);">{{ $description }}</div>
        @endif
    </div>
</div>
