<!-- Sidebar -->
<aside class="sidebar" :class="{ 'collapsed': !sidebarOpen, 'mobile-open': mobileOpen }">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-4 py-5 border-b" style="border-color:var(--surface-border); min-height:var(--topbar-height); flex-shrink:0;">
        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-sm"
             style="background:linear-gradient(135deg,#059669,#34d399);">
            F
        </div>
        <span class="nav-label font-bold text-base tracking-tight" style="color:var(--text-primary);">floraQuality</span>
    </div>

    <!-- Navigation -->
    <div class="sidebar-content py-3 flex-1 flex flex-col gap-1">

        @php
            $navItems = [
                ['route' => 'dashboard',        'label' => 'Dashboard',   'icon' => 'grid'],
                ['route' => 'checklists.create', 'label' => 'New Checklist','icon' => 'plus-circle'],
                ['route' => 'checklists.index',  'label' => 'Inspections', 'icon' => 'clipboard'],
            ];
            if (Auth::user()->isSuperAdmin() || Auth::user()->isAdmin()) {
                $navItems[] = ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'bar-chart'];
            }
            if (Auth::user()->isSuperAdmin()) {
                $navItems[] = ['route' => 'users.index', 'label' => 'User Management', 'icon' => 'users'];
                $navItems[] = ['route' => 'audit-logs.index', 'label' => 'Audit Logs', 'icon' => 'activity'];
            }
        @endphp

        @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['route'] === 'checklists.index' ? 'checklists.index' : ($item['route'] === 'checklists.create' ? 'checklists.create' : $item['route'])); @endphp
            <a href="{{ route($item['route']) }}"
               class="nav-item {{ $isActive ? 'active' : '' }}"
               title="{{ $item['label'] }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    @if($item['icon'] === 'grid')
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    @elseif($item['icon'] === 'plus-circle')
                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12h8"/>
                    @elseif($item['icon'] === 'clipboard')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/>
                    @elseif($item['icon'] === 'bar-chart')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    @elseif($item['icon'] === 'users')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    @elseif($item['icon'] === 'activity')
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    @endif
                </svg>
                <span class="nav-label">{{ $item['label'] }}</span>
            </a>
        @endforeach

        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
            <div class="nav-label px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest" style="color:var(--text-tertiary);">Admin</div>
            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" title="Settings">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                <span class="nav-label">Settings</span>
            </a>
        @endif
    </div>

    <!-- User Footer -->
    <div class="border-t p-3 flex-shrink-0" style="border-color:var(--surface-border);">
        <div class="nav-item group" style="cursor:default;">
            <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold"
                 style="background:linear-gradient(135deg,#059669,#0891b2);">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="nav-label flex-1 min-w-0">
                <div class="text-sm font-semibold truncate" style="color:var(--text-primary);">{{ Auth::user()->name }}</div>
                <div class="text-xs truncate" style="color:var(--text-tertiary);">{{ Auth::user()->email }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item w-full mt-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20" title="Log Out">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="nav-label">Log Out</span>
            </button>
        </form>
    </div>
</aside>
