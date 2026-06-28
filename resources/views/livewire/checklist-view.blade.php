<div class="max-w-3xl mx-auto space-y-6">
    {{-- Back Link --}}
    <div>
        <a href="{{ route('checklists.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-premium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Inspections
        </a>
    </div>

    {{-- Detail Card --}}
    <div class="card overflow-hidden">
        {{-- Color status bar --}}
        <div class="h-2.5 {{ match($checklist->condition) {
            'good'    => 'bg-emerald-500',
            'average' => 'bg-amber-500',
            default   => 'bg-rose-500',
        } }}"></div>

        <div class="p-6 md:p-8 space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-450 dark:text-slate-500">Inspection Log</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">
                        Record #{{ $checklist->id }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Logged by <span class="font-bold text-slate-700 dark:text-slate-300">{{ $checklist->user->name ?? 'Unknown' }}</span>
                        on {{ $checklist->created_at->format('d M Y \a\t H:i') }}
                    </p>
                </div>
                <x-badge :condition="$checklist->condition" />
            </div>

            {{-- Details Grid --}}
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @php
                    $details = [
                        ['label' => 'Inspection Date', 'value' => $checklist->check_date->format('l, d F Y'), 'icon' => '📅'],
                        ['label' => 'Inspection Time', 'value' => $checklist->check_time, 'icon' => '⏰'],
                        ['label' => 'Inspector name',  'value' => $checklist->user->name ?? 'N/A', 'icon' => '👤'],
                    ];
                @endphp
                @foreach ($details as $d)
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20">
                        <dt class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span>{{ $d['icon'] }}</span>
                            {{ $d['label'] }}
                        </dt>
                        <dd class="text-sm font-bold text-slate-900 dark:text-white">{{ $d['value'] }}</dd>
                    </div>
                @endforeach

                {{-- Remarks --}}
                <div class="sm:col-span-2 p-5 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/20">
                    <dt class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Remarks / Observations</dt>
                    <dd class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
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
                    <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/10">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">
                            {{ $sig['label'] }}
                        </p>
                        @if ($sig['path'])
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-lg p-3 flex items-center justify-center">
                                <img src="{{ Storage::url($sig['path']) }}" alt="{{ $sig['label'] }}" class="max-h-20 object-contain">
                            </div>
                        @else
                            <div class="h-20 flex items-center justify-center text-xs text-slate-450 italic border border-dashed border-slate-200 dark:border-slate-800 rounded-lg">
                                No signature captured
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 bg-slate-50 dark:bg-slate-950/30 border-t border-slate-100 dark:border-slate-800">
            @can('update', $checklist)
                <a href="{{ route('checklists.edit', $checklist) }}" class="btn btn-secondary text-amber-600 border-amber-100 dark:border-amber-900/50">
                    ✏️ Edit Checklist
                </a>
            @endcan

            <div class="flex gap-2 ms-auto">
                <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
