<header class="topbar">

    <div class="topbar-left">
        {{-- Mobile menu toggle --}}
        <button class="topbar-btn" onclick="toggleSidebar()" style="display:none" id="menu-btn">
            <i class="ti ti-menu-2"></i>
        </button>

        <div class="page-title-bar">
            <i class="ti ti-layout-dashboard" style="color: var(--green-dark); font-size:18px"></i>
            @yield('page-title', 'Dashboard')

            @php $role = auth()->user()->role; @endphp
            @if($role === 'superadmin')
                <span class="page-badge badge-superadmin">Super Admin</span>
            @elseif($role === 'admin')
                <span class="page-badge badge-admin">Admin</span>
            @else
                <span class="page-badge badge-user">User</span>
            @endif
        </div>
    </div>

    <div class="topbar-right">

        {{-- Date & Time --}}
        <div class="datetime-chip">
            <div class="chip">
                <i class="ti ti-calendar"></i>
                {{ now()->format('M d, Y') }}
            </div>
            <div class="chip" id="live-clock">
                {{ now()->format('h:i:s A') }}
            </div>
        </div>

        {{-- Notifications (admin+) --}}
        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
        <a href="#" class="topbar-btn" title="Notifications">
            <i class="ti ti-bell"></i>
        </a>
        @endif

        {{-- Profile --}}
        <div class="topbar-avatar" title="{{ auth()->user()->name }}">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

    </div>
</header>

<script>
// Show mobile menu button on small screens
if (window.innerWidth <= 768) {
    document.getElementById('menu-btn').style.display = 'flex';
}
window.addEventListener('resize', () => {
    const btn = document.getElementById('menu-btn');
    if (btn) btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
});
</script>