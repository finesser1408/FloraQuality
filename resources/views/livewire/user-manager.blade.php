<div>
    {{-- Page Header --}}
    <x-page-header title="User Management" description="Create, edit, and manage system users, roles, and account access.">
        <x-slot:action>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Create User
            </button>
        </x-slot:action>
    </x-page-header>

    {{-- Filter Bar --}}
    <div class="card p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="search-input-wrap">
                <svg class="search-icon w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.350ms="search" placeholder="Search users by name or email…" class="form-input">
            </div>

            <select wire:model.live="role" class="form-input">
                <option value="">All Roles</option>
                <option value="super_admin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff / Inspector</option>
            </select>

            <select wire:model.live="status" class="form-input">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    {{-- User List --}}
    <div class="card overflow-hidden">
        @if($users->isEmpty())
            <x-empty-state title="No users found" description="Adjust your filters or register a new user account.">
                <x-slot:icon>
                    <svg class="w-8 h-8" style="color:var(--text-tertiary);" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </x-slot:icon>
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-bold text-white"
                                             style="background:linear-gradient(135deg,#059669,#0891b2);">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-sm block" style="color:var(--text-primary);">{{ $user->name }}</span>
                                            <span class="text-[10px] text-slate-400 font-medium">Created: {{ $user->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm font-semibold" style="color:var(--text-secondary);">{{ $user->email }}</td>
                                <td class="text-sm" style="color:var(--text-secondary);">{{ $user->phone_number ?? '—' }}</td>
                                <td>
                                    <span class="badge" style="background:var(--surface-2);color:var(--text-secondary);">
                                        {{ $user->role === 'super_admin' ? 'Super Admin' : ($user->role === 'admin' ? 'Admin' : 'Staff') }}
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="toggleUserStatus({{ $user->id }})"
                                            class="btn btn-xs {{ $user->status === 'active' ? 'btn-secondary text-emerald-600' : 'btn-secondary text-slate-400' }} border-none hover:bg-opacity-50">
                                        {{ $user->status === 'active' ? '● Active' : '○ Inactive' }}
                                    </button>
                                </td>
                                <td class="text-xs" style="color:var(--text-tertiary);">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button wire:click="openEditModal({{ $user->id }})" class="btn btn-ghost btn-xs">Edit</button>
                                        @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                            <button onclick="confirm('Are you sure you want to delete this user?') || event.stopImmediatePropagation()"
                                                    wire:click="deleteUser({{ $user->id }})"
                                                    class="btn btn-xs" style="background:rgba(220,38,38,0.08);color:#dc2626;border:1px solid rgba(220,38,38,0.15);">Delete</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t" style="border-color:var(--surface-border);">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit User Modal --}}
    @if($showFormModal)
        <div class="modal-overlay" x-data x-transition:enter="animate-fade-in">
            <div class="modal-box max-w-md p-6">
                <h3 class="text-lg font-bold mb-4" style="color:var(--text-primary);">
                    {{ $userId ? 'Edit User Details' : 'Create User Account' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="form-label">Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model.defer="name" class="form-input @error('name') error @enderror" placeholder="John Doe">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" wire:model.defer="email" class="form-input @error('email') error @enderror" placeholder="name@company.com">
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Phone Number <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="text" wire:model.defer="phone" class="form-input @error('phone') error @enderror" placeholder="+27 82 123 4567">
                        @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">System Role <span class="text-rose-500">*</span></label>
                            <select wire:model.defer="userRole" class="form-input" {{ $userId === auth()->id() ? 'disabled' : '' }}>
                                <option value="staff">Staff / Inspector</option>
                                <option value="admin">Administrator</option>
                                <option value="super_admin">Super Administrator</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status <span class="text-rose-500">*</span></label>
                            <select wire:model.defer="userStatus" class="form-input" {{ $userId === auth()->id() ? 'disabled' : '' }}>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Password {{ $userId ? '(leave blank to keep current)' : '' }} <span class="text-rose-500">{{ $userId ? '' : '*' }}</span></label>
                        <input type="password" wire:model.defer="password" class="form-input @error('password') error @enderror" placeholder="••••••••">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="form-label">Confirm Password</label>
                        <input type="password" wire:model.defer="password_confirmation" class="form-input" placeholder="••••••••">
                    </div>

                    <div class="flex gap-3 pt-3">
                        <button type="button" wire:click="resetForm" class="btn btn-secondary flex-1" @click="$wire.showFormModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-1">Save Account</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
