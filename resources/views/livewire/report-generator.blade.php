<div class="space-y-6">
    {{-- Page Header --}}
    <x-page-header title="Inspection Reports" description="Configure, preview, and export checklist data."></x-page-header>

    {{-- Configuration --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider mb-4" style="color:var(--text-tertiary);">Report Parameters</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Report Type</label>
                <select wire:model.live="reportType" class="form-input">
                    <option value="daily">Daily Inspections</option>
                    <option value="weekly">Weekly Summary</option>
                    <option value="monthly">Monthly Summary</option>
                    <option value="condition">Condition Summary</option>
                    <option value="user_activity">User Activity</option>
                </select>
            </div>

            <div>
                <label class="form-label">Date From</label>
                <input type="date" wire:model.live="dateFrom" class="form-input">
            </div>

            <div>
                <label class="form-label">Date To</label>
                <input type="date" wire:model.live="dateTo" class="form-input">
            </div>

            <div class="flex items-end">
                <button wire:click="exportCsv" wire:loading.attr="disabled" class="btn btn-primary w-full flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="exportCsv">📥 Export CSV</span>
                    <span wire:loading wire:target="exportCsv" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Generating…
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Live Preview Table --}}
    <div class="card overflow-hidden" wire:loading.class="opacity-60">
        <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color:var(--surface-border);">
            <h3 class="font-bold text-base" style="color:var(--text-primary);">Preview Data</h3>
            <span class="badge" style="background:var(--surface-2);color:var(--text-secondary);">
                {{ $reportData->count() }} records
            </span>
        </div>

        @if ($reportData->isEmpty())
            <x-empty-state title="No preview data available" description="Select a different date range or parameters.">
                <x-slot:icon>
                    <svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </x-slot:icon>
            </x-empty-state>
        @elseif (in_array($reportType, ['condition']))
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Condition Status</th>
                        <th>Total Inspections</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $row)
                        <tr>
                            <td class="font-semibold capitalize" style="color:var(--text-primary);">{{ $row->condition }}</td>
                            <td class="font-bold text-slate-700 dark:text-slate-300">{{ $row->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif ($reportType === 'user_activity')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Inspector Name</th>
                        <th>Total Logged Inspections</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $row)
                        <tr>
                            <td class="font-semibold" style="color:var(--text-primary);">{{ $row->user->name ?? 'Unknown' }}</td>
                            <td class="font-bold text-slate-700 dark:text-slate-300">{{ $row->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Condition</th>
                            <th>Inspector</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reportData as $row)
                            <tr>
                                <td class="font-semibold whitespace-nowrap" style="color:var(--text-primary);">{{ $row->check_date->format('d M Y') }}</td>
                                <td class="whitespace-nowrap" style="color:var(--text-secondary);">{{ $row->check_time }}</td>
                                <td><x-badge :condition="$row->condition" /></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-white"
                                             style="background:linear-gradient(135deg,#059669,#0891b2);">
                                            {{ strtoupper(substr($row->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-sm" style="color:var(--text-secondary);">{{ $row->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="text-sm text-slate-500 max-w-xs truncate">{{ $row->remarks ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
