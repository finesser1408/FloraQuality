<section>
    <div class="card overflow-hidden mt-0 border-rose-200 dark:border-rose-900">
        <div class="px-6 py-4 border-b flex items-center gap-3" style="border-color:rgba(220,38,38,0.15);">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(220,38,38,0.08);">
                <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-red-600">Danger Zone</h2>
                <p class="text-xs mt-0.5" style="color:var(--text-tertiary);">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
            </div>
        </div>

        <div class="p-6" x-data="{ open: false }">
            <button type="button" @click="open = true" class="btn btn-danger">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete My Account
            </button>

            {{-- Confirmation Modal --}}
            <div x-show="open" class="modal-overlay" x-transition:enter="animate-fade-in" style="display:none;">
                <div class="modal-box p-6">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(220,38,38,0.08);">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-center mb-2" style="color:var(--text-primary);">Delete Account?</h3>
                    <p class="text-sm text-center mb-5" style="color:var(--text-tertiary);">Once deleted, all your data will be permanently removed. Enter your password to confirm.</p>

                    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
                        @csrf
                        @method('delete')

                        <div>
                            <label for="delete_password" class="form-label">Your Current Password</label>
                            <input id="delete_password" name="password" type="password"
                                   placeholder="••••••••"
                                   class="form-input @error('password', 'userDeletion') error @enderror">
                            @error('password', 'userDeletion')
                                <p class="form-error mt-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="open = false" class="btn btn-secondary flex-1">Cancel</button>
                            <button type="submit" class="btn btn-danger flex-1">Permanently Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
