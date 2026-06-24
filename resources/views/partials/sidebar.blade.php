<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="ti ti-package"></i></div>
        <div>
            <div class="brand-text-main">UCC Inventory</div>
            <div class="brand-text-sub">Management System</div>
        </div>
    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
            <div class="user-info-name">{{ Str::limit(auth()->user()->name, 20) }}</div>
            @php $role = auth()->user()->role; @endphp
            <span class="user-info-role role-{{ $role }}">
                {{ $role === 'superadmin' ? 'Super Admin' : ucfirst($role) }}
            </span>
            @if(auth()->user()->campus)
            <div class="user-campus">
                <i class="ti ti-map-pin" style="font-size:10px"></i>
                {{ Str::limit(auth()->user()->campus->name, 18) }}
            </div>
            @endif
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        {{-- Common: all roles --}}
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        @if(auth()->user()->role === 'user')
        <a href="{{ route('consumables') }}"
           class="nav-item {{ request()->routeIs('consumables') ? 'active' : '' }}">
            <i class="ti ti-package"></i> Consumables
        </a>
        @endif

        {{-- Admin & Super Admin only --}}
        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))

        <div class="nav-section-label">Inventory</div>

        <a href="{{ route('inventory') }}"
           class="nav-item {{ request()->routeIs('inventory') ? 'active' : '' }}">
            <i class="ti ti-clipboard-list"></i> Inventory
        </a>

        <a href="{{ route('equipment') }}"
           class="nav-item {{ request()->routeIs('equipment') ? 'active' : '' }}">
            <i class="ti ti-device-desktop"></i> All Equipment
        </a>

        <a href="{{ route('locations') }}"
           class="nav-item {{ request()->routeIs('locations') ? 'active' : '' }}">
            <i class="ti ti-map-pin"></i> Locations
        </a>

        <div class="nav-section-label">Management</div>

        <a href="{{ route('consumables') }}"
           class="nav-item {{ request()->routeIs('consumables') ? 'active' : '' }}">
            <i class="ti ti-droplet"></i> Consumables
        </a>

        <a href="{{ route('history') }}"
           class="nav-item {{ request()->routeIs('history') ? 'active' : '' }}">
            <i class="ti ti-history"></i> History
        </a>

        {{-- Super Admin only --}}
        @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('condemned') }}"
        class="nav-item {{ request()->routeIs('condemned') ? 'active' : '' }}">
            <i class="ti ti-alert-triangle"></i> Condemned
        </a>

        <a href="{{ route('notifications.index') }}"
           class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="ti ti-bell"></i> Notifications
        </a>

        <a href="{{ route('system.settings') }}"
           class="nav-item {{ request()->routeIs('system.*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i> System Settings
        </a>
        @endif

        <a href="{{ route('users') }}"
           class="nav-item {{ request()->routeIs('users') ? 'active' : '' }}">
            <i class="ti ti-users"></i> Users
        </a>

        @endif

    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item logout" style="width:100%; background:none; border:none; cursor:pointer; font-family:inherit;">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>
    </div>

</aside>