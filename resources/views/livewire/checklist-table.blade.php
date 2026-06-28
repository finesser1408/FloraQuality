<div>
    {{-- Page Header --}}
    <x-page-header title="Inspection History" description="Browse, filter, and manage all recorded flower quality inspections.">
        <x-slot:action>
            <a href="{{ route('checklists.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                New Inspection
            </a>
        </x-slot:action>
    </x-page-header>

    {{-- Filter Bar --}}
    <div class="card p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="search-input-wrap">
                <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.350ms="search"
                       placeholder="Search inspections…"
                       class="form-input">
            </div>

            {{-- Condition Filter --}}
            <select wire:model.live="condition" class="form-input">
                <option value="">All Conditions</option>
                <option value="good">Good Condition</option>
                <option value="average">Average Quality</option>
                <option value="bad">Bad / Rejected</option>
            </select>

            {{-- Date From --}}
            <input type="date" wire:model.live="dateFrom" class="form-input" placeholder="From">

            {{-- Date To --}}
            <input type="date" wire:model.live="dateTo" class="form-input" placeholder="To">

            @if(Auth::user()->isAdmin() && $staffList->isNotEmpty())
                <div class="sm:col-span-2 lg:col-span-4 pt-1">
                    <div class="text-xs font-bold uppercase tracking-wider mb-2" style="color:var(--text-tertiary);">Filter by Inspector</div>
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('staffId', null)"
                                class="btn btn-xs {{ is_null($staffId) ? 'btn-primary' : 'btn-secondary' }}">
                            All
                        </button>
                        @foreach($staffList as $staff)
                            <button wire:click="$set('staffId', {{ $staff->id }})"
                                    class="btn btn-xs {{ $staffId == $staff->id ? 'btn-primary' : 'btn-secondary' }}">
                                {{ $staff->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Loading bar --}}
    <div wire:loading class="w-full mb-4 h-0.5 rounded-full overflow-hidden" style="background:var(--surface-2);">
        <div class="h-full rounded-full animate-pulse" style="width:60%;background:#003580;"></div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        @if($checklists->isEmpty())
            <x-empty-state
                title="No inspections found"
                :description="($search || $condition || $dateFrom || $dateTo) ? 'Try adjusting your filters.' : 'No quality inspections have been recorded yet.'"
                :actionHref="($search || $condition || $dateFrom || $dateTo) ? null : route('checklists.create')"
                actionLabel="Create First Inspection">
                <x-slot:icon>
                    <svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </x-slot:icon>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            @foreach([['check_date','Date'],['check_time','Time'],['condition','Condition'],[null,'Inspector'],['created_at','Logged At']] as [$field,$label])
                                <th>
                                    @if($field)
                                        <button wire:click="sortBy('{{ $field }}')" class="flex items-center gap-1.5 hover:opacity-70 transition-opacity">
                                            {{ $label }}
                                            @if($sortField === $field)
                                                <svg class="w-3 h-3" style="color:#003580;" fill="currentColor" viewBox="0 0 20 20">
                                                    @if($sortDirection === 'asc')
                                                        <path d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                                    @else
                                                        <path d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                                    @endif
                                                </svg>
                                            @else
                                                <svg class="w-3 h-3 opacity-30" fill="currentColor" viewBox="0 0 20 20"><path d="M5 12l5-5 5 5H5z"/></svg>
                                            @endif
                                        </button>
                                    @else
                                        {{ $label }}
                                    @endif
                                </th>
                            @endforeach
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checklists as $item)
                            <tr>
                                <td class="font-semibold whitespace-nowrap" style="color:var(--text-primary);">{{ $item->check_date->format('d M Y') }}</td>
                                <td class="whitespace-nowrap" style="color:var(--text-secondary);">{{ $item->check_time }}</td>
                                <td><x-badge :condition="$item->condition" /></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-white"
                                             style="background:linear-gradient(135deg,#003580,#1a52a0);">
                                            {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-sm" style="color:var(--text-secondary);">{{ $item->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="text-sm whitespace-nowrap" style="color:var(--text-tertiary);">{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('checklists.show', $item) }}" class="btn btn-ghost btn-xs">View</a>
                                        @can('update', $item)
                                            <a href="{{ route('checklists.edit', $item) }}" class="btn btn-xs btn-secondary" style="color:#d97706;">Edit</a>
                                        @endcan
                                        @can('delete', $item)
                                            <button wire:click="confirmDelete({{ $item->id }})" class="btn btn-xs" style="background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.15);">Delete</button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t" style="border-color:var(--surface-border);">
                {{ $checklists->links() }}
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="modal-overlay" x-data x-transition:enter="animate-fade-in">
            <div class="modal-box p-6">
                <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(220,38,38,0.08);">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-lg font-bold text-center mb-2" style="color:var(--text-primary);">Delete Inspection?</h3>
                <p class="text-sm text-center mb-6" style="color:var(--text-tertiary);">This action is permanent. All signatures and data associated with this record will be deleted.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete" class="btn btn-secondary flex-1">Cancel</button>
                    <button wire:click="deleteChecklist" wire:loading.attr="disabled" class="btn btn-danger flex-1">
                        <span wire:loading.remove wire:target="deleteChecklist">Delete Forever</span>
                        <span wire:loading wire:target="deleteChecklist">Deleting…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
