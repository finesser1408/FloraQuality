<!-- ── Top Navigation Bar ──────────────────────────────────────── -->
<header class="topbar" style="background:var(--surface-0,#ffffff); border-bottom:1px solid var(--surface-border,#d0d8e8);">

    <!-- Sidebar / Mobile toggle buttons -->
    <button @click="toggleSidebar()" class="btn-icon hidden md:flex flex-shrink-0" title="Toggle sidebar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <button @click="toggleMobile()" class="btn-icon flex md:hidden flex-shrink-0" title="Menu">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Breadcrumb -->
    <div class="flex-1 min-w-0">
        <nav class="flex items-center gap-1.5 text-sm" style="color:var(--text-tertiary,#718096);">
            <a href="{{ route('dashboard') }}"
               class="font-semibold hover:underline flex items-center gap-1.5 flex-shrink-0"
               style="color:#003580;">
                {{-- Mini PRAZ shield --}}
                <svg style="width:13px;height:13px;flex-shrink:0;" viewBox="0 0 24 24" fill="none"
                     stroke="#003580" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L3 7v5c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7L12 2z"/>
                </svg>
                PRAZ eGP
            </a>
            @if(!request()->routeIs('dashboard'))
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="font-medium truncate" style="color:var(--text-secondary,#4a5568);">
                    @if(request()->routeIs('checklists.create'))   New Checklist
                    @elseif(request()->routeIs('checklists.edit')) Edit Checklist
                    @elseif(request()->routeIs('checklists.show')) View Checklist
                    @elseif(request()->routeIs('checklists.*'))    Inspections
                    @elseif(request()->routeIs('reports.*'))       Reports
                    @elseif(request()->routeIs('users.*'))         User Management
                    @elseif(request()->routeIs('audit-logs.*'))    Audit Logs
                    @elseif(request()->routeIs('profile.*'))       Profile Settings
                    @else {{ ucfirst(request()->segment(1)) }}
                    @endif
                </span>
            @endif
        </nav>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center gap-1.5 flex-shrink-0">

        <!-- Dark Mode Toggle -->
        <button @click="toggleDark()" class="btn-icon" title="Toggle dark mode">
            <svg x-show="!isDark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="isDark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>

        <!-- Notification Bell (static UI) -->
        <button class="btn-icon relative" title="Notifications">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{-- Gold dot indicator --}}
            <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 rounded-full"
                  style="background:#c9a227; box-shadow:0 0 0 1.5px var(--surface-0,#ffffff);"></span>
        </button>

        <!-- Divider -->
        <div class="w-px h-5 mx-1 flex-shrink-0" style="background:var(--surface-border,#d0d8e8);"></div>

        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @keydown.escape="open = false"
                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg transition-colors"
                    style="border:1.5px solid var(--surface-border,#d0d8e8);"
                    onmouseover="this.style.borderColor='#003580';"
                    onmouseout="this.style.borderColor='var(--surface-border,#d0d8e8)';">
                {{-- Avatar --}}
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                     style="background:#003580;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="text-sm font-semibold hidden sm:block truncate max-w-[120px]"
                      style="color:var(--text-primary,#0f1c2e);">{{ Auth::user()->name }}</span>
                <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open"
                 @click.outside="open = false"
                 x-transition:enter="animate-fade-up"
                 class="absolute right-0 mt-2 w-56 card py-1.5 z-50"
                 style="display:none; border:1px solid var(--surface-border,#d0d8e8);">

                {{-- User Header --}}
                <div class="px-4 py-3 border-b" style="border-color:var(--surface-border,#d0d8e8);">
                    <div class="flex items-center gap-2.5 mb-0.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             style="background:#003580;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-bold truncate" style="color:var(--text-primary,#0f1c2e);">{{ Auth::user()->name }}</div>
                            <div class="text-xs truncate" style="color:var(--text-tertiary,#718096);">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    {{-- PRAZ Role Badge --}}
                    <div class="mt-2">
                        @if(Auth::user()->isSuperAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                  style="background:rgba(201,162,39,0.12); color:#a07e1a; border:1px solid rgba(201,162,39,0.25);">
                                Super Admin
                            </span>
                        @elseif(Auth::user()->isAdmin())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                  style="background:rgba(0,53,128,0.08); color:#003580; border:1px solid rgba(0,53,128,0.15);">
                                Administrator
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                  style="background:var(--surface-2,#e8edf5); color:var(--text-secondary,#4a5568); border:1px solid var(--surface-border,#d0d8e8);">
                                Inspector
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Profile Settings --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-4 py-2.5 text-sm transition-colors"
                   style="color:var(--text-secondary,#4a5568);"
                   onmouseover="this.style.background='var(--surface-1,#f4f6f9)';this.style.color='#003580';"
                   onmouseout="this.style.background='';this.style.color='var(--text-secondary,#4a5568)';">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile Settings
                </a>

                <div class="my-1" style="height:1px; background:var(--surface-border,#d0d8e8);"></div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-left transition-colors"
                            style="color:#dc2626;"
                            onmouseover="this.style.background='rgba(220,38,38,0.06)';"
                            onmouseout="this.style.background='';">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
