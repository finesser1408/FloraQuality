<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }"
      x-init="$watch('darkMode', v => localStorage.setItem('darkMode', v))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Flower Quality System') }}</title>

    {{-- Tailwind CSS CDN (swap for compiled in production) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {
                colors: { brand: { 50:'#f0fdf4', 500:'#22c55e', 700:'#15803d', 900:'#14532d' } }
            }}
        }
    </script>

    {{-- AlpineJS --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Signature Pad --}}
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    @livewireStyles
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans antialiased min-h-screen">

    {{-- ── Sidebar ────────────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-xl
                  transform transition-transform duration-300 ease-in-out
                  -translate-x-full md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           @click.away="sidebarOpen = false">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-gray-700">
            <span class="text-2xl">🌸</span>
            <div>
                <p class="font-bold text-gray-900 dark:text-white text-sm leading-tight">Flower Quality</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Checklist System</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="mt-4 px-3 space-y-1">
            @php
                $navItems = [
                    ['route' => 'dashboard',         'label' => 'Dashboard',    'icon' => '📊'],
                    ['route' => 'checklists.create',  'label' => 'New Checklist','icon' => '➕'],
                    ['route' => 'checklists.index',   'label' => 'History',      'icon' => '📋'],
                    ['route' => 'reports.index',      'label' => 'Reports',      'icon' => '📈'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                          {{ request()->routeIs($item['route'])
                              ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                              : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <span>{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- User card at bottom --}}
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ Auth::user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-gray-400 hover:text-red-500 transition-colors text-sm"
                            title="Logout">🚪</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main Content ─────────────────────────────────────────────────────── --}}
    <div class="md:pl-64 flex flex-col min-h-screen">

        {{-- Top Bar --}}
        <header class="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between px-4 py-3">
                {{-- Mobile menu toggle --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    ☰
                </button>

                <h1 class="text-base font-semibold text-gray-800 dark:text-white md:text-lg">
                    Flower Quality Checklist System
                </h1>

                <div class="flex items-center gap-2">
                    {{-- Dark mode toggle --}}
                    <button @click="darkMode = !darkMode"
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <span x-show="!darkMode">🌙</span>
                        <span x-show="darkMode">☀️</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-4 mt-4 flex items-center gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/30
                        border border-green-200 dark:border-green-700 rounded-lg text-green-800 dark:text-green-200 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show"
                 class="mx-4 mt-4 flex items-center gap-2 px-4 py-3 bg-red-50 dark:bg-red-900/30
                        border border-red-200 dark:border-red-700 rounded-lg text-red-800 dark:text-red-200 text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 md:p-6">
            {{ $slot }}
        </main>

        <footer class="text-center text-xs text-gray-400 dark:text-gray-600 py-4">
            &copy; {{ date('Y') }} Flower Quality Checklist System &middot; ZCHPC
        </footer>
    </div>

    @livewireScripts
</body>
</html>
