<div>
    {{-- Page Header --}}
    <x-page-header title="Audit & Activity Logs" description="Review all critical actions logged across the entire system."></x-page-header>

    {{-- Filters --}}
    <div class="card p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="search-input-wrap">
                <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.350ms="search" placeholder="Search description or IP…" class="form-input">
            </div>

            <select wire:model.live="action" class="form-input">
                <option value="">All Actions</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
                <option value="exported">Exported</option>
            </select>

            <select wire:model.live="selectedUser" class="form-input">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="card overflow-hidden">
        @if($logs->isEmpty())
            <x-empty-state title="No activity logged" description="Select a different filter or parameter.">
                <x-slot:icon>
                    <svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </x-slot:icon>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Browser/Device</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                    @if($log->user)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                                                 style="background:linear-gradient(135deg,#059669,#0891b2);">
                                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold" style="color:var(--text-primary);">{{ $log->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs italic" style="color:var(--text-tertiary);">System</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $actionColors = [
                                            'login' => 'background:rgba(5,150,105,0.1);color:#059669;',
                                            'logout' => 'background:rgba(71,85,105,0.1);color:#475569;',
                                            'created' => 'background:rgba(56,189,248,0.1);color:#0284c7;',
                                            'updated' => 'background:rgba(245,158,11,0.1);color:#d97706;',
                                            'deleted' => 'background:rgba(239,68,68,0.1);color:#dc2626;',
                                            'exported' => 'background:rgba(139,92,246,0.1);color:#7c3aed;',
                                        ];
                                        $style = $actionColors[$log->action] ?? 'background:var(--surface-2);color:var(--text-secondary);';
                                    @endphp
                                    <span class="badge" style="{{ $style }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="text-sm font-medium" style="color:var(--text-secondary);">
                                    {{ $log->description }}
                                </td>
                                <td class="text-sm font-mono" style="color:var(--text-secondary);">
                                    {{ $log->ip_address ?? '—' }}
                                </td>
                                <td class="text-xs" style="color:var(--text-tertiary); max-w-xs truncate;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?? '—' }}
                                </td>
                                <td class="text-xs whitespace-nowrap" style="color:var(--text-tertiary);">
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t" style="border-color:var(--surface-border);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
