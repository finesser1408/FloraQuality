<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Page Header --}}
        <x-page-header title="Account Settings" description="Manage your profile, update your password, and configure your account."></x-page-header>

        {{-- Password Change Required Banner --}}
        @if(auth()->user()->require_password_change)
            <div class="p-4 rounded-xl flex items-start gap-3 animate-fade-up"
                 style="background:rgba(245,158,11,0.08);border:1.5px solid rgba(245,158,11,0.25);">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-bold text-amber-700">Password Change Required</p>
                    <p class="text-sm text-amber-600 mt-0.5">For security, you must update your default password before continuing. Please scroll down and update your password now.</p>
                </div>
            </div>
        @endif

        {{-- Status Flash --}}
        @if(session('status') === 'profile-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                 x-transition:enter="animate-fade-up" x-transition:leave="animate-fade-in"
                 class="p-4 rounded-xl flex items-center gap-3"
                 style="background:rgba(0,53,128,0.07);border:1.5px solid rgba(0,53,128,0.2);color:#003580;">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-semibold">Profile information updated successfully.</span>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
                 x-transition:enter="animate-fade-up"
                 class="p-4 rounded-xl flex items-center gap-3"
                 style="background:rgba(0,53,128,0.07);border:1.5px solid rgba(0,53,128,0.2);color:#003580;">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-semibold">Password updated successfully.</span>
            </div>
        @endif

        {{-- Profile Information --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:var(--surface-border);">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(0,53,128,0.08);">
                    <svg class="w-4.5 h-4.5" style="color:#003580;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold" style="color:var(--text-primary);">Profile Information</h2>
                    <p class="text-xs mt-0.5" style="color:var(--text-tertiary);">Update your name, phone number, and email address.</p>
                </div>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="p-6 space-y-5">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="form-label">Full Name <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name', $user->name) }}"
                               required autofocus autocomplete="name"
                               placeholder="John Doe"
                               class="form-input @error('name') error @enderror">
                        @error('name')
                            <p class="form-error mt-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone_number" class="form-label">Phone Number <span class="text-xs font-normal" style="color:var(--text-tertiary);">(optional)</span></label>
                        <input id="phone_number" name="phone_number" type="tel"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               placeholder="+263 77 123 4567"
                               class="form-input @error('phone_number') error @enderror">
                        @error('phone_number')
                            <p class="form-error mt-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="form-label">Email Address <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email', $user->email) }}"
                           required autocomplete="username"
                           placeholder="name@praz.org.zw"
                           class="form-input @error('email') error @enderror">
                    @error('email')
                        <p class="form-error mt-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Read-only role & status info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">System Role</label>
                        <div class="form-input opacity-70 cursor-not-allowed" style="background:var(--surface-1);">
                            {{ match($user->role) { 'super_admin' => 'Super Administrator', 'admin' => 'Administrator', default => 'Staff / Inspector' } }}
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Account Status</label>
                        <div class="form-input opacity-70 cursor-not-allowed {{ $user->status === 'active' ? '' : 'text-red-500' }}"
                             style="background:var(--surface-1);{{ $user->status === 'active' ? 'color:#003580;' : '' }}">
                            {{ ucfirst($user->status ?? 'active') }}
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Save Profile
                    </button>
                </div>
            </form>
        </div>

        {{-- Update Password --}}
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:var(--surface-border);">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(0,53,128,0.08);">
                    <svg class="w-4.5 h-4.5" style="color:#003580;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold" style="color:var(--text-primary);">Change Password</h2>
                    <p class="text-xs mt-0.5" style="color:var(--text-tertiary);">Use a long, unique password to keep your account secure.</p>
                </div>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="p-6 space-y-5">
                @csrf
                @method('put')

                <div>
                    <label for="update_password_current_password" class="form-label">Current Password <span class="text-red-500">*</span></label>
                    <input id="update_password_current_password" name="current_password" type="password"
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="form-input @error('current_password', 'updatePassword') error @enderror">
                    @error('current_password', 'updatePassword')
                        <p class="form-error mt-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="update_password_password" class="form-label">New Password <span class="text-red-500">*</span></label>
                        <input id="update_password_password" name="password" type="password"
                               autocomplete="new-password"
                               placeholder="Min. 8 characters"
                               class="form-input @error('password', 'updatePassword') error @enderror">
                        @error('password', 'updatePassword')
                            <p class="form-error mt-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="form-label">Confirm New Password <span class="text-red-500">*</span></label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                               autocomplete="new-password"
                               placeholder="Repeat new password"
                               class="form-input">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone: Delete Account (only for non-super-admins) --}}
        @if(!auth()->user()->isSuperAdmin())
            @include('profile.partials.delete-user-form')
        @endif
    </div>
</x-app-layout>
