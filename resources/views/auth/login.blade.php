<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — {{ config('app.name', 'PRAZ Flower Checklist System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased flex flex-col" style="background:var(--surface-1);">

    {{-- Government top header bar --}}
    <header style="background:#003580;" class="w-full flex-shrink-0">
        <div class="max-w-5xl mx-auto px-6 py-5 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest mb-1" style="color:rgba(255,255,255,0.65);">
                Procurement Regulatory Authority of Zimbabwe
            </p>
            <h1 class="text-xl font-extrabold tracking-tight text-white leading-tight">
                PRAZ Flower Checklist System
            </h1>
            <p class="text-xs mt-1 font-medium" style="color:#c9a227;">Official Internal System</p>
        </div>
    </header>

    {{-- Page body --}}
    <main class="flex-1 flex items-center justify-center p-4 py-10">
        <div class="w-full max-w-[420px] animate-fade-up">

            {{-- Login card --}}
            <div class="card relative overflow-hidden" style="box-shadow:var(--shadow-lg);">

                {{-- Top accent bar --}}
                <div class="absolute top-0 left-0 right-0 h-1" style="background:linear-gradient(90deg,#003580 0%,#c9a227 100%);"></div>

                <div class="p-8 md:p-10 pt-9">

                    {{-- PRAZ logo mark --}}
                    <div class="flex flex-col items-center text-center mb-8">
                        <div class="w-14 h-14 flex items-center justify-center rounded-2xl mb-4"
                             style="background:rgba(0,53,128,0.08);">
                            {{-- Shield / document icon --}}
                            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="#003580" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2L3 7v5c0 5.25 3.75 10.15 9 11.25C17.25 22.15 21 17.25 21 12V7L12 2z"/>
                                <path d="M9 12l2 2 4-4"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color:#003580;">PRAZ</p>
                        <h2 class="text-2xl font-extrabold tracking-tight" style="color:var(--text-primary);">Sign In</h2>
                        <p class="text-sm mt-1" style="color:var(--text-tertiary);">Access the Flower Checklist System</p>
                    </div>

                    {{-- Session status --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    {{-- Login form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Work Email --}}
                        <div>
                            <label for="email" class="form-label">Work Email</label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="name@praz.org.zw"
                                   class="form-input @error('email') error @enderror">
                            @error('email')
                                <p class="form-error mt-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Password with show/hide toggle --}}
                        <div x-data="{ show: false }">
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="form-label mb-0">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="text-xs font-semibold hover:opacity-80 transition-opacity"
                                       style="color:#003580;">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <input id="password"
                                       :type="show ? 'text' : 'password'"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="form-input pr-10 @error('password') error @enderror">
                                <button type="button"
                                        @click="show = !show"
                                        tabindex="-1"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 transition-opacity hover:opacity-70"
                                        style="color:var(--text-tertiary);"
                                        :aria-label="show ? 'Hide password' : 'Show password'">
                                    {{-- Eye open --}}
                                    <svg x-show="!show" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{-- Eye closed --}}
                                    <svg x-show="show" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 012.573-4.169M9.88 9.88a3 3 0 104.24 4.24M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="form-error mt-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Remember me --}}
                        <div class="flex items-center">
                            <input id="remember_me"
                                   type="checkbox"
                                   name="remember"
                                   class="h-4 w-4 rounded border-slate-300 focus:ring-offset-0 transition-colors"
                                   style="accent-color:#003580;">
                            <label for="remember_me" class="ms-2 text-xs font-semibold" style="color:var(--text-secondary);">
                                Remember my session
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="btn w-full py-2.5 justify-center font-bold text-sm tracking-wide text-white mt-2"
                                style="background:#003580;border-color:#003580;">
                            Sign In
                        </button>
                    </form>

                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-6 space-y-1">
                <p class="text-xs font-medium" style="color:var(--text-tertiary);">
                    PRAZ &copy; {{ date('Y') }} — Procurement Regulatory Authority of Zimbabwe
                </p>
                <p class="text-xs" style="color:var(--text-tertiary);opacity:0.7;">
                    Authorised users only. Unauthorised access is prohibited.
                </p>
            </div>

        </div>
    </main>

</body>
</html>
