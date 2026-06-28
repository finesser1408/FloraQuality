<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log In — {{ config('app.name', 'FloraQuality') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.getItem('flora_dark') === 'true' ||
            (!('flora_dark' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-full antialiased gradient-bg flex items-center justify-center p-4">

<div class="w-full max-w-[460px] animate-fade-up">

    <!-- Card -->
    <div class="card p-8 md:p-10 relative overflow-hidden">
        {{-- Top decorative accent line --}}
        <div class="absolute top-0 left-0 right-0 h-1.5" style="background:linear-gradient(135deg,#059669 0%,#0891b2 100%);"></div>

        <!-- Header -->
        <div class="flex flex-col items-center text-center mb-8">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 text-white font-black text-lg shadow-md"
                 style="background:linear-gradient(135deg,#059669,#34d399);">
                F
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight" style="color:var(--text-primary);">floraQuality Portal</h1>
            <p class="text-sm mt-1" style="color:var(--text-tertiary);">Sign in to manage checklist records and audits.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="form-label">Work Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                           placeholder="name@company.com"
                           class="form-input @error('email') error @enderror">
                </div>
                @error('email')
                    <p class="form-error mt-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="password" class="form-label mb-0">Password</label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-semibold hover:opacity-85 transition-opacity" style="color:var(--color-brand);" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                       placeholder="••••••••"
                       class="form-input @error('password') error @enderror">
                @error('password')
                    <p class="form-error mt-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 rounded border-slate-300 dark:border-slate-800 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition-premium">
                <label for="remember_me" class="ms-2 text-xs font-semibold" style="color:var(--text-secondary);">Remember my session</label>
            </div>

            <!-- Action -->
            <button type="submit" class="btn btn-primary w-full py-2.5 mt-2 justify-center font-bold text-sm tracking-wide">
                Sign In
            </button>
        </form>
    </div>

    <!-- Info Footer -->
    <div class="text-center mt-6">
        <p class="text-xs" style="color:var(--text-tertiary);">floraQuality Inspection Portal. All rights reserved.</p>
    </div>

</div>

</body>
</html>
