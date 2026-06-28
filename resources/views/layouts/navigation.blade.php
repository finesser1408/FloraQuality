<!-- Top Navigation Bar -->
<header class="topbar">
    <!-- Hamburger / Collapse button -->
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
        <nav class="flex items-center gap-1.5 text-sm" style="color:var(--text-tertiary);">
            <span class="font-medium" style="color:var(--text-primary);">FloraQuality</span>
            @if(!request()->routeIs('dashboard'))
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="font-medium truncate" style="color:var(--text-secondary);">
                    @if(request()->routeIs('checklists.create')) New Checklist
                    @elseif(request()->routeIs('checklists.edit')) Edit Checklist
                    @elseif(request()->routeIs('checklists.show')) View Checklist
                    @elseif(request()->routeIs('checklists.*')) Inspections
                    @elseif(request()->routeIs('reports.*')) Reports
                    @elseif(request()->routeIs('profile.*')) Profile Settings
                    @else {{ ucfirst(request()->segment(1)) }}
                    @endif
                </span>
            @endif
        </nav>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center gap-2 flex-shrink-0">

        <!-- Dark Mode Toggle -->
        <button @click="toggleDark()" class="btn-icon" title="Toggle dark mode">
            <svg x-show="!isDark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="isDark" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>

        <!-- User Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @keydown.escape="open = false"
                    class="flex items-center gap-2.5 px-3 py-1.5 rounded-lg hover:opacity-80 transition-opacity"
                    style="border:1.5px solid var(--surface-border);">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold"
                     style="background:linear-gradient(135deg,#059669,#0891b2);">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <span class="text-sm font-semibold hidden sm:block" style="color:var(--text-primary);">{{ Auth::user()->name }}</span>
                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <div x-show="open" @click.outside="open = false"
                 x-transition:enter="animate-fade-up"
                 class="absolute right-0 mt-2 w-52 card py-1.5 z-50"
                 style="display:none;">
                <div class="px-4 py-3 border-b" style="border-color:var(--surface-border);">
                    <div class="text-sm font-bold" style="color:var(--text-primary);">{{ Auth::user()->name }}</div>
                    <div class="text-xs mt-0.5 truncate" style="color:var(--text-tertiary);">{{ Auth::user()->email }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm hover:bg-opacity-5 transition-colors" style="color:var(--text-secondary);" onmouseover="this.style.background='var(--surface-1)'" onmouseout="this.style.background=''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profile Settings
                </a>
                <div class="border-t my-1.5" style="border-color:var(--surface-border);"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-left text-red-500 transition-colors" onmouseover="this.style.background='rgba(220,38,38,0.06)'" onmouseout="this.style.background=''">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
