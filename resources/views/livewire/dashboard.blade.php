<div class="space-y-8">

    {{-- Welcome Header --}}
    <div class="card px-8 py-7 flex flex-wrap items-center justify-between gap-6"
         style="border-left:4px solid var(--color-primary);">
        <div>
            <p class="text-sm font-medium mb-1" style="color:var(--text-tertiary);">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},</p>
            <h1 class="text-3xl font-extrabold tracking-tight" style="color:var(--text-primary);">{{ Auth::user()->name }} 👋</h1>
            <p class="text-sm mt-2" style="color:var(--text-tertiary);">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('users.index') }}" class="btn"
                   style="background:var(--surface-2);color:var(--color-primary);border:1.5px solid var(--surface-border);font-weight:600;">
                    Manage Users
                </a>
            @endif
            @if(Auth::user()->isAdmin())
                <a href="{{ route('reports.index') }}" class="btn"
                   style="background:var(--surface-2);color:var(--color-primary);border:1.5px solid var(--surface-border);font-weight:600;">
                    View Reports
                </a>
            @endif
            <a href="{{ route('checklists.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Inspection
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-5">
        <x-stat-card
            :value="$total"
            description="Total inspections"
            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'
            iconBg="#003580"
        />
        <x-stat-card
            :value="$goodCount"
            description="Good condition"
            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>'
            iconBg="#003580"
        />
        <x-stat-card
            :value="$averageCount"
            description="Average quality"
            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'
            iconBg="#d97706"
        />
        <x-stat-card
            :value="$badCount"
            description="Bad / Rejected"
            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>'
            iconBg="#dc2626"
        />
        <x-stat-card
            :value="$todayCount"
            description="Inspected today"
            icon='<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'
            iconBg="#1a52a0"
        />
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Area Chart --}}
        <div class="card lg:col-span-2 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-base font-bold" style="color:var(--text-primary);">Inspection Trend</h2>
                    <p class="text-xs mt-0.5" style="color:var(--text-tertiary);">Monthly volume — last 6 months</p>
                </div>
                <span class="badge" style="background:rgba(0,53,128,0.08);color:#003580;">PRAZ Flower Checklist System</span>
            </div>
            <div style="height:220px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- Condition Breakdown --}}
        <div class="card p-6 flex flex-col">
            <h2 class="text-base font-bold mb-1" style="color:var(--text-primary);">Quality Breakdown</h2>
            <p class="text-xs mb-6" style="color:var(--text-tertiary);">All-time condition share</p>

            @if($total > 0)
                @php
                    $bars = [
                        ['label' => 'Good',    'count' => $goodCount,    'pct' => round($goodCount / $total * 100),    'color' => '#003580', 'bg' => 'rgba(0,53,128,0.10)'],
                        ['label' => 'Average', 'count' => $averageCount, 'pct' => round($averageCount / $total * 100), 'color' => '#d97706', 'bg' => 'rgba(217,119,6,0.12)'],
                        ['label' => 'Bad',     'count' => $badCount,     'pct' => round($badCount / $total * 100),     'color' => '#dc2626', 'bg' => 'rgba(220,38,38,0.10)'],
                    ];
                @endphp
                <div class="space-y-5 flex-1">
                    @foreach($bars as $b)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold" style="color:var(--text-primary);">{{ $b['label'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold" style="color:{{ $b['color'] }};">{{ $b['count'] }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};">{{ $b['pct'] }}%</span>
                                </div>
                            </div>
                            <div class="h-2 rounded-full overflow-hidden" style="background:var(--surface-2);">
                                <div class="h-full rounded-full transition-all duration-700"
                                     style="width:{{ $b['pct'] }}%;background:{{ $b['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state title="No data yet" description="Start logging inspections to see a breakdown.">
                    <x-slot:icon><svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></x-slot:icon>
                </x-empty-state>
            @endif
        </div>
    </div>

    {{-- Recent Inspections --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color:var(--surface-border);">
            <div>
                <h2 class="text-base font-bold" style="color:var(--text-primary);">Recent Inspections</h2>
                <p class="text-xs mt-0.5" style="color:var(--text-tertiary);">Last 8 quality records</p>
            </div>
            <a href="{{ route('checklists.index') }}" class="btn btn-ghost btn-sm">
                View all
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        @if($recent->isEmpty())
            <x-empty-state
                title="No inspections yet"
                description="Create your first flower quality inspection to get started."
                actionHref="{{ route('checklists.create') }}"
                actionLabel="New Inspection">
                <x-slot:icon>
                    <svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/></svg>
                </x-slot:icon>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Inspector</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Condition</th>
                            <th>Remarks</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $item)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                                             style="background:linear-gradient(135deg,#003580,#1a52a0);">
                                            {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-sm" style="color:var(--text-primary);">{{ $item->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="font-semibold text-sm" style="color:var(--text-primary);">{{ $item->check_date->format('d M Y') }}</td>
                                <td class="text-sm" style="color:var(--text-secondary);">{{ $item->check_time }}</td>
                                <td><x-badge :condition="$item->condition" /></td>
                                <td class="text-sm max-w-xs" style="color:var(--text-tertiary);">
                                    <span class="block truncate max-w-[180px]">{{ $item->remarks ?? '—' }}</span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('checklists.show', $item) }}" class="btn btn-ghost btn-xs">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('monthlyChart');
    if (!canvas) return;

    const labels = @json(array_keys($monthlyStats));
    const data   = @json(array_values($monthlyStats));

    const ctx = canvas.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 220);
    grad.addColorStop(0, 'rgba(0, 53, 128, 0.20)');
    grad.addColorStop(1, 'rgba(0, 53, 128, 0.01)');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Inspections',
                data,
                fill: true,
                backgroundColor: grad,
                borderColor: '#003580',
                borderWidth: 2.5,
                pointBackgroundColor: '#003580',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2.5,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#94a3b8',
                    bodyColor: '#f1f5f9',
                    padding: 12,
                    cornerRadius: 8,
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', weight: '500', size: 12 }, stepSize: 1 },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', weight: '500', size: 12 } },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
