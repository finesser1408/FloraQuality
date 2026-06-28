<div class="max-w-3xl mx-auto space-y-6">
    {{-- Back Link --}}
    <div>
        <a href="{{ route('checklists.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold transition-opacity hover:opacity-70" style="color:var(--text-tertiary);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Inspections
        </a>
    </div>

    {{-- Detail Card --}}
    <div class="card overflow-hidden">
        {{-- Color status bar — condition colors are intentional status indicators --}}
        <div class="h-2.5 {{ match($checklist->condition) {
            'good'    => 'bg-emerald-500',
            'average' => 'bg-amber-500',
            default   => 'bg-rose-500',
        } }}"></div>

        <div class="p-6 md:p-8 space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest" style="color:var(--text-tertiary);">Inspection Record</span>
                    <h2 class="text-3xl font-extrabold mt-1" style="color:var(--text-primary);">
                        Record #{{ $checklist->id }}
                    </h2>
                    <p class="text-sm mt-1" style="color:var(--text-secondary);">
                        Logged by <span class="font-bold" style="color:var(--text-primary);">{{ $checklist->user->name ?? 'Unknown' }}</span>
                        on {{ $checklist->created_at->format('d M Y \a\t H:i') }}
                    </p>
                </div>
                <x-badge :condition="$checklist->condition" />
            </div>

            {{-- Details Grid --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $details = [
                        [
                            'label' => 'Inspection Date',
                            'value' => $checklist->check_date->format('l, d F Y'),
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
                        ],
                        [
                            'label' => 'Inspection Time',
                            'value' => $checklist->check_time,
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        ],
                        [
                            'label' => 'Inspector Name',
                            'value' => $checklist->user->name ?? 'N/A',
                            'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
                        ],
                    ];
                @endphp
                @foreach ($details as $d)
                    <div class="p-4 rounded-xl" style="border:1px solid var(--surface-border);background:var(--surface-1);">
                        <dt class="text-[10px] font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5" style="color:var(--text-tertiary);">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                {!! $d['icon'] !!}
                            </svg>
                            {{ $d['label'] }}
                        </dt>
                        <dd class="text-sm font-bold" style="color:var(--text-primary);">{{ $d['value'] }}</dd>
                    </div>
                @endforeach

                {{-- Remarks --}}
                <div class="sm:col-span-2 p-5 rounded-xl" style="border:1px solid var(--surface-border);background:var(--surface-1);">
                    <dt class="text-[10px] font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5" style="color:var(--text-tertiary);">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Remarks / Observations
                    </dt>
                    <dd class="text-sm whitespace-pre-line leading-relaxed" style="color:var(--text-secondary);">
                        {{ $checklist->remarks ?? 'No remarks provided for this inspection.' }}
                    </dd>
                </div>
            </dl>

            {{-- Signatures --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                @foreach ([
                    ['label' => 'Staff Member Signature',   'path' => $checklist->staff_signature],
                    ['label' => 'Flower Supplier Signature','path' => $checklist->supplier_signature],
                ] as $sig)
                    <div class="p-4 rounded-xl" style="border:1px solid var(--surface-border);background:var(--surface-1);">
                        <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:var(--text-tertiary);">
                            {{ $sig['label'] }}
                        </p>
                        @if ($sig['path'])
                            <div class="rounded-lg p-3 flex items-center justify-center" style="background:var(--surface-0);border:1px solid var(--surface-border);">
                                <img src="{{ Storage::url($sig['path']) }}" alt="{{ $sig['label'] }}" class="max-h-20 object-contain">
                            </div>
                        @else
                            <div class="h-20 flex items-center justify-center text-xs italic rounded-lg" style="border:1px dashed var(--border-color, var(--surface-border));color:var(--text-tertiary);">
                                No signature captured
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-t" style="background:var(--surface-1);border-color:var(--surface-border);">
            @can('update', $checklist)
                <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-secondary" style="color:#d97706;border-color:rgba(201,162,39,0.25);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Checklist
                </a>
            @endcan

            <div class="flex gap-2 ms-auto">
                <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
