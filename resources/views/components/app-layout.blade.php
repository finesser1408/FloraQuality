<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="appShell()"
      x-init="init()"
      :class="{ 'dark': isDark }"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FloraQuality') }} — {{ $title ?? 'Settings' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js" defer></script>
</head>
<body class="h-full antialiased" style="background:var(--surface-1); color:var(--text-primary);">

<div class="app-shell">
    @include('layouts.sidebar')

    <div x-show="mobileOpen"
         x-transition:enter="animate-fade-in"
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/50 z-40 md:hidden"
         style="display:none"></div>

    <div class="main-content" :class="{ 'sidebar-collapsed': !sidebarOpen }">
        @include('layouts.navigation')

        {{-- Flash Messages --}}
        <div class="px-8 pt-4" x-data>
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4500)"
                     x-transition:enter="animate-fade-up"
                     class="flex items-center gap-3 px-4 py-3 mb-4 rounded-xl text-sm font-medium border"
                     style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.2);color:#059669;">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                    <button @click="show=false" class="ml-auto opacity-60 hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)"
                     x-transition:enter="animate-fade-up"
                     class="flex items-center gap-3 px-4 py-3 mb-4 rounded-xl text-sm font-medium border"
                     style="background:rgba(220,38,38,0.07);border-color:rgba(220,38,38,0.2);color:#dc2626;">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    {{ session('error') }}
                    <button @click="show=false" class="ml-auto opacity-60 hover:opacity-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            @endif
        </div>

        <main class="page-body animate-fade-up">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts

<script>
function appShell() {
    return {
        isDark: false,
        sidebarOpen: true,
        mobileOpen: false,
        init() {
            const saved = localStorage.getItem('flora_dark');
            this.isDark = saved !== null ? saved === 'true' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            const sb = localStorage.getItem('flora_sidebar');
            if (sb !== null) this.sidebarOpen = sb !== 'false';
            this.$watch('isDark', v => localStorage.setItem('flora_dark', v));
            this.$watch('sidebarOpen', v => localStorage.setItem('flora_sidebar', v));
        },
        toggleDark() { this.isDark = !this.isDark; },
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; },
        toggleMobile() { this.mobileOpen = !this.mobileOpen; }
    }
}
</script>
</body>
</html>
