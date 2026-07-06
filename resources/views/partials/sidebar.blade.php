@php
    $role = auth()->user()->role;
    $latestUpdate = \App\Models\SystemUpdate::where(function($q) {
        $q->where('system', 'ims')->orWhere('system', 'both');
    })->latest()->first();
    $versionLabel = $latestUpdate?->version ?? 'v1.0.0';
@endphp

<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="{{ asset('images/ucc.png') }}" alt="UCC Logo"
                 style="width:100%; height:100%; object-fit:contain; border-radius:8px;">
        </div>
        <div>
            <div class="brand-text-main">UCC Inventory</div>
            <div class="brand-text-sub">Management System</div>
            <button onclick="openVersionModal()" style="font-size:9px; color:rgba(255,255,255,0.35); margin-top:2px; font-family:monospace; letter-spacing:0.5px; background:none; border:none; cursor:pointer; padding:0; text-align:left; transition:color 0.2s;"
                    onmouseover="this.style.color='rgba(255,255,255,0.75)'"
                    onmouseout="this.style.color='rgba(255,255,255,0.35)'">
                {{ $versionLabel }} <i class="ti ti-info-circle" style="font-size:8px;"></i>
            </button>
        </div>
    </div>

    {{-- User Info --}}
    <div class="sidebar-user">
        <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
        <div>
            <div class="user-info-name">{{ Str::limit(auth()->user()->name, 20) }}</div>
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

        <div class="nav-section-label">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>

        <div class="nav-section-label">Inventory</div>
        <a href="{{ route('inventory') }}" class="nav-item {{ request()->routeIs('inventory') && !request()->routeIs('inventory.*') ? 'active' : '' }}">
            <i class="ti ti-stack-2"></i> Inventory
        </a>
        <a href="{{ route('equipment') }}" class="nav-item {{ request()->routeIs('equipment*') ? 'active' : '' }}">
            <i class="ti ti-list-details"></i> All Equipment
        </a>
        <a href="{{ route('locations') }}" class="nav-item {{ request()->routeIs('locations*') ? 'active' : '' }}">
            <i class="ti ti-map-pin"></i> Locations
        </a>

        <div class="nav-section-label">Management</div>
        <a href="{{ route('consumables') }}" class="nav-item {{ request()->routeIs('consumables*') ? 'active' : '' }}">
            <i class="ti ti-package"></i> Consumables
        </a>
        <a href="{{ route('history') }}" class="nav-item {{ request()->routeIs('history*') ? 'active' : '' }}">
            <i class="ti ti-history"></i> History
        </a>
        <a href="{{ route('condemned') }}" class="nav-item {{ request()->routeIs('condemned*') ? 'active' : '' }}">
            <i class="ti ti-alert-triangle"></i> Condemned
        </a>
        @if(in_array($role, ['admin','superadmin']))
        <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications*') ? 'active' : '' }}">
            <i class="ti ti-bell"></i> Notifications
        </a>
        @if(in_array($role, ['admin','superadmin']))
        <a href="{{ route('messages.index') }}"
        class="nav-item {{ request()->routeIs('messages*') ? 'active' : '' }}">
            <i class="ti ti-messages"></i> Messages
            @php
                $unreadTickets = \App\Models\Conversation::where('type','admin')
                    ->where('status','open')
                    ->whereHas('messages', fn($q) => $q->where('sender_type','user')->where('is_read',false))
                    ->count();
            @endphp
            @if($unreadTickets > 0)
            <span style="margin-left:auto; background:var(--red); color:#fff;
                        font-size:10px; font-weight:700; padding:2px 7px;
                        border-radius:10px; line-height:1.4;">{{ $unreadTickets }}</span>
            @endif
        </a>
        @endif
        <a href="{{ route('system.settings') }}" class="nav-item {{ request()->routeIs('system*') ? 'active' : '' }}">
            <i class="ti ti-settings"></i> System Settings
        </a>
        <a href="{{ route('users') }}" class="nav-item {{ request()->routeIs('users*') ? 'active' : '' }}">
            <i class="ti ti-users"></i> Users
        </a>
        @endif

    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item logout"
                    style="width:100%; border:none; background:none; cursor:pointer; font-family:inherit; text-align:left;">
                <i class="ti ti-logout"></i> Logout
            </button>
        </form>
    </div>

</aside>

{{-- ═══ WHAT'S NEW MODAL (shows once per version per user) ═══ --}}
@php
    $systemKey = 'ims';

    $whatsNew = \App\Models\SystemUpdate::where(function($q) use ($systemKey) {
        $q->where('system', $systemKey)->orWhere('system', 'both');
    })->where('show_modal', true)->latest()->first();
@endphp

@if($whatsNew)
<div class="modal-overlay" id="whats-new-modal"
    style="z-index:500; align-items:center; justify-content:center; display:none;">
    <div class="modal-box-lg" style="max-width:520px;">
        <div style="background:var(--green-dark); margin:-1.5rem -1.5rem 1.5rem;
                    padding:1.5rem; border-radius:14px 14px 0 0; text-align:center;">
            <div style="font-size:12px; font-weight:600; text-transform:uppercase;
                        letter-spacing:2px; color:rgba(255,255,255,0.65); margin-bottom:6px;">
                What's New
            </div>
            <div style="font-size:22px; font-weight:700; color:#fff; display:flex;
                        align-items:center; justify-content:center; gap:10px;">
                <i class="ti ti-sparkles"></i>
                {{ $whatsNew->title }}
            </div>
            <div style="margin-top:8px; font-family:monospace; font-size:13px;
                        background:rgba(255,255,255,0.15); display:inline-block;
                        padding:3px 14px; border-radius:20px; color:#fff;">
                {{ $whatsNew->version }}
            </div>
            @if($whatsNew->system === 'both')
            <div style="margin-top:8px;">
                <span style="font-size:10px; background:rgba(255,255,255,0.2); color:#fff;
                            padding:3px 10px; border-radius:10px; font-weight:600;">
                    IMS + CS Update
                </span>
            </div>
            @elseif($whatsNew->system === $systemKey)
            <div style="margin-top:8px;">
                <span style="font-size:10px; background:rgba(255,255,255,0.2); color:#fff;
                            padding:3px 10px; border-radius:10px; font-weight:600;">
                    {{ strtoupper($systemKey) }} Update
                </span>
            </div>
            @endif
        </div>

        <div style="font-size:13.5px; line-height:1.85; color:var(--text-primary);
                    white-space:pre-wrap; max-height:320px; overflow-y:auto;
                    background:#fafafa; border-radius:10px;
                    padding:1rem 1.2rem; margin-bottom:1.25rem;">{{ $whatsNew->content }}</div>

        <div style="display:flex; gap:10px;">
            <button type="button" onclick="dismissWhatsNew()" class="btn-back-link"
                    style="flex:1; justify-content:center;">Skip</button>
            <button type="button" onclick="dismissWhatsNew()" class="modal-btn-primary"
                    style="flex:2; margin:0; background:var(--green-dark);">
                <i class="ti ti-check"></i> Got it, Let's Go!
            </button>
        </div>

        <div style="text-align:center; font-size:11px; color:#bbb; margin-top:0.75rem;">
            Published {{ $whatsNew->created_at->format('M d, Y') }}
            @if($whatsNew->author) by {{ $whatsNew->author->name }} @endif
        </div>
    </div>
</div>

<script>
(function() {
    const VERSION = '{{ $whatsNew->version }}';
    const SYSTEM  = '{{ $systemKey }}';
    const LS_KEY  = 'whats_new_dismissed_' + SYSTEM + '_' + VERSION;

    const modal = document.getElementById('whats-new-modal');
    if (!modal) return;

    // Only show if NOT already dismissed in localStorage
    if (localStorage.getItem(LS_KEY) !== '1') {
        modal.style.display = 'flex';
    }

    window.dismissWhatsNew = async function() {
        // Mark as dismissed in localStorage immediately
        localStorage.setItem(LS_KEY, '1');
        modal.style.display = 'none';

        // Also persist in server session
        try {
            await fetch('{{ route("system.updates.dismiss") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        } catch(e) {}
    };
})();
</script>
@endif

    {{-- ═══ VERSION INFO MODAL (opened by clicking version number in sidebar) ═══ --}}
    @php
        $versionModalKey = 'ims';

        $allUpdates = \App\Models\SystemUpdate::where(function($q) use ($versionModalKey) {
            $q->where('system', $versionModalKey)->orWhere('system', 'both');
        })->latest()->take(5)->get();

        $latestForModal = $allUpdates->first();
    @endphp

    <div class="modal-overlay" id="version-info-modal">
        <div class="modal-box-lg" style="max-width:520px;">
            <div style="background:var(--green-dark); margin:-1.5rem -1.5rem 1.5rem;
                        padding:1.5rem; border-radius:14px 14px 0 0;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div>
                        <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                                    letter-spacing:2px; color:rgba(255,255,255,0.6); margin-bottom:4px;">
                            System Version
                        </div>
                        <div style="font-size:22px; font-weight:700; color:#fff;
                                    display:flex; align-items:center; gap:8px;">
                            <i class="ti ti-git-branch"></i>
                            {{ $latestForModal?->version ?? 'v1.0.0' }}
                        </div>
                        @if($latestForModal)
                        <div style="font-size:12px; color:rgba(255,255,255,0.65); margin-top:4px;">
                            {{ $latestForModal->title }} &bull;
                            {{ $latestForModal->created_at->format('M d, Y') }}
                        </div>
                        @endif
                    </div>
                    <button onclick="document.getElementById('version-info-modal').classList.remove('open');"
                            style="width:28px; height:28px; border-radius:6px; border:none;
                                   background:rgba(255,255,255,0.15); color:#fff; cursor:pointer;
                                   display:flex; align-items:center; justify-content:center;
                                   flex-shrink:0; font-size:14px;">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
            </div>

            @if($latestForModal)
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                            letter-spacing:1px; color:var(--text-muted); margin-bottom:0.75rem;
                            display:flex; align-items:center; gap:6px;">
                    <i class="ti ti-sparkles" style="color:var(--green-dark);"></i> Latest Update
                </div>
                <div style="font-size:13px; line-height:1.85; color:var(--text-primary);
                            white-space:pre-wrap; background:#fafafa; border-radius:10px;
                            padding:1rem 1.2rem; max-height:220px; overflow-y:auto;">{{ $latestForModal->content }}</div>
            </div>
            @endif

            @if($allUpdates->count() > 1)
            <div style="margin-bottom:1.25rem;">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                            letter-spacing:1px; color:var(--text-muted); margin-bottom:0.75rem;
                            display:flex; align-items:center; gap:6px;">
                    <i class="ti ti-history" style="color:var(--green-dark);"></i> Previous Versions
                </div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    @foreach($allUpdates->skip(1) as $prev)
                    <div style="display:flex; align-items:center; justify-content:space-between;
                                padding:8px 12px; background:#fafafa; border-radius:8px;
                                border:1px solid var(--border);">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-family:monospace; font-size:12px; font-weight:600;
                                         color:var(--green-dark);">{{ $prev->version }}</span>
                            <span style="font-size:12px; color:var(--text-secondary);">{{ $prev->title }}</span>
                        </div>
                        <span style="font-size:11px; color:var(--text-muted); flex-shrink:0;">
                            {{ $prev->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($allUpdates->isEmpty())
            <div class="empty-state" style="padding:1.5rem;">
                <i class="ti ti-git-branch-deleted"></i>
                <p>No version history available yet.</p>
            </div>
            @endif

            <button type="button"
                    onclick="document.getElementById('version-info-modal').classList.remove('open');"
                    class="modal-btn-primary" style="background:var(--green-dark);">
                <i class="ti ti-check"></i> Close
            </button>
        </div>
    </div>

    <script>
    function openVersionModal() {
        document.getElementById('version-info-modal').classList.add('open');
    }
    </script>

    @stack('scripts')
</body>
</html>