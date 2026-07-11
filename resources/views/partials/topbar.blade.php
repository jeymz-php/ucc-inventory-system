<header class="topbar">

    <div class="topbar-left">
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
        <div style="position:relative;" id="notif-wrap">
            <a href="#" class="topbar-btn" title="Notifications"
               onclick="event.preventDefault(); toggleNotifDropdown();">
                <i class="ti ti-bell"></i>
                <span id="notif-badge"
                      style="display:none; position:absolute; top:-4px; right:-4px;
                             background:#e24b4a; color:#fff; font-size:10px; font-weight:700;
                             border-radius:50%; width:18px; height:18px;
                             align-items:center; justify-content:center;">0</span>
            </a>

            {{-- Uses notif-dropdown-box — NOT settings-dropdown — to avoid CSS conflict --}}
            <div id="notif-dropdown" class="notif-dropdown-box">
                <div class="settings-header" style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div class="settings-user-name">Notifications</div>
                        <div class="settings-user-email" id="notif-summary">No pending notifications</div>
                    </div>
                    <a href="{{ route('notifications.index') }}"
                       style="font-size:11px; color:var(--green-dark); font-weight:600; text-decoration:none;">
                        View All →
                    </a>
                </div>
                <div id="notif-list" style="max-height:360px; overflow-y:auto;"></div>
            </div>
        </div>
        @endif

        {{-- Settings Dropdown --}}
        <div class="settings-wrap" id="settings-wrap">
            <button class="topbar-btn" onclick="toggleSettings()" title="Settings" id="settings-btn">
                <i class="ti ti-settings"></i>
            </button>

            <div class="settings-dropdown" id="settings-dropdown">
                <div class="settings-header">
                    <div class="settings-user-name">{{ auth()->user()->name }}</div>
                    <div class="settings-user-email">{{ auth()->user()->email }}</div>
                </div>

                @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                <a href="{{ route('system.settings') }}" class="settings-item">
                    <i class="ti ti-adjustments-horizontal"></i>
                    System Settings
                </a>
                @endif

                <a href="#" class="settings-item" onclick="openChangePassword()">
                    <i class="ti ti-lock-password"></i>
                    Change Password
                </a>

                <a href="{{ route('account.settings') }}" class="settings-item">
                    <i class="ti ti-settings-2"></i>
                    Account Settings
                </a>

                @if(auth()->user()->role === 'superadmin')
                <div class="settings-divider"></div>
                <a href="{{ route('system.status') }}" class="settings-item">
                    <i class="ti ti-activity"></i>
                    System Status
                </a>
                @endif
            </div>
        </div>

    </div>
</header>

{{-- Change Password Modal --}}
<div class="modal-overlay" id="change-password-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-lock-password"></i> Change Password</div>
            <button class="modal-close" onclick="closeChangePassword()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('password.change') }}" id="change-pass-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Current Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock modal-input-icon"></i>
                    <input type="password" name="current_password" class="modal-input"
                           placeholder="Enter current password" id="cur-pass">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('cur-pass', this)"></i>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">New Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock-open modal-input-icon"></i>
                    <input type="password" name="password" class="modal-input"
                           placeholder="Minimum 8 characters" id="new-pass" oninput="modalStrength()">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('new-pass', this)"></i>
                </div>
                <div class="modal-strength-bar">
                    <div class="modal-seg" id="ms1"></div>
                    <div class="modal-seg" id="ms2"></div>
                    <div class="modal-seg" id="ms3"></div>
                    <div class="modal-seg" id="ms4"></div>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Confirm New Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock-check modal-input-icon"></i>
                    <input type="password" name="password_confirmation" class="modal-input"
                           placeholder="Re-enter new password" id="conf-pass" oninput="modalMatch()">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('conf-pass', this)"></i>
                </div>
                <div class="modal-hint" id="conf-pass-hint"></div>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-check"></i> Update Password
            </button>
        </form>
    </div>
</div>

<style>
/* Notification dropdown — separate from settings-dropdown to avoid conflict */
.notif-dropdown-box {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 360px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    z-index: 300;
    overflow: hidden;
    animation: dropIn 0.18s ease;
}
.notif-dropdown-box.open { display: block; }
</style>

<script>
// ── SOUND ──
function playNotifSound() {
    try {
        const ctx  = new (window.AudioContext || window.webkitAudioContext)();
        const osc  = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.setValueAtTime(660, ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.45);
    } catch(e) {}
}

// ── NOTIFICATION DROPDOWN TOGGLE ──
let prevNotifCount = 0;

function toggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    if (!dd) return;
    const isOpen = dd.classList.contains('open');
    // Close settings if open
    const sd = document.getElementById('settings-dropdown');
    if (sd) sd.classList.remove('open');
    dd.classList.toggle('open', !isOpen);
}

// Close notif dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notif-wrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.classList.remove('open');
    }
});

function getReadNotifIds() {
    try { return JSON.parse(localStorage.getItem('read_notif_ids_ims') || '[]'); }
    catch (e) { return []; }
}

function markNotifAsRead(type, id) {
    const key     = `${type}-${id}`;
    const readIds = getReadNotifIds();
    if (!readIds.includes(key)) {
        readIds.push(key);
        localStorage.setItem('read_notif_ids_ims', JSON.stringify(readIds));
    }
}

async function pollNotifications() {
    const badge   = document.getElementById('notif-badge');
    const list    = document.getElementById('notif-list');
    const summary = document.getElementById('notif-summary');
    if (!badge) return;

    try {
        const res     = await fetch('{{ route("notifications.poll") }}');
        const data    = await res.json();
        const readIds = getReadNotifIds();

        const unreadCount = (data.requests || []).filter(r =>
            !readIds.includes(`${r.type}-${r.id}`)
        ).length;

        // Sound on new notification
        if (unreadCount > prevNotifCount && prevNotifCount !== 0) {
            playNotifSound();
        }
        prevNotifCount = unreadCount;

        if (unreadCount > 0) {
            badge.style.display = 'flex';
            badge.textContent   = unreadCount;
            const parts = [];
            if (data.deletion_count  > 0) parts.push(`${data.deletion_count} account deletion`);
            if (data.consumable_count > 0) parts.push(`${data.consumable_count} consumable request`);
            if (data.ticket_count    > 0) parts.push(`${data.ticket_count} new ticket(s)`);
            if (data.stock_count     > 0) parts.push(`${data.stock_count} item(s) out of stock`);
            summary.textContent = parts.join(', ') || `${unreadCount} pending`;
        } else {
            badge.style.display = 'none';
            summary.textContent = data.count > 0 ? 'All caught up' : 'No pending notifications';
        }

        list.innerHTML = (data.requests || []).map(r => {
            const isRead   = readIds.includes(`${r.type}-${r.id}`);
            const rowStyle = isRead ? 'opacity:0.55;' : '';
            const dot      = !isRead
                ? '<span style="width:7px;height:7px;border-radius:50%;background:var(--red);display:inline-block;margin-left:4px;"></span>'
                : '';

            if (r.type === 'deletion') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px; font-weight:700; background:#fff5f5; color:#e24b4a; padding:2px 7px; border-radius:10px; text-transform:uppercase;">Account Deletion</span>
                        ${dot}
                    </div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">${r.title}</div>
                    <div style="font-size:11px; color:var(--text-muted); margin:2px 0 6px;">${r.subtitle} • ${r.created_at}</div>
                    ${r.reason ? `<div style="font-size:12px; color:#666; margin-bottom:8px; font-style:italic;">"${r.reason}"</div>` : ''}
                    <div style="display:flex; gap:6px;">
                        <button onclick="approveDeletion(${r.id})"
                                style="flex:1; padding:6px; border:none; border-radius:6px; background:#fff5f5; color:#e24b4a; font-size:11.5px; font-weight:600; cursor:pointer; font-family:inherit;">
                            <i class="ti ti-check"></i> Approve & Delete
                        </button>
                        <button onclick="rejectDeletion(${r.id})"
                                style="flex:1; padding:6px; border:none; border-radius:6px; background:#f0faf4; color:#1a6b3a; font-size:11.5px; font-weight:600; cursor:pointer; font-family:inherit;">
                            <i class="ti ti-x"></i> Reject
                        </button>
                    </div>
                </div>`;

            } else if (r.type === 'ticket') {
                const sourceBadge = r.source === 'ims'
                    ? '<span style="font-size:9px;font-weight:700;background:#f0faf4;color:#1a6b3a;padding:2px 6px;border-radius:6px;">IMS</span>'
                    : '<span style="font-size:9px;font-weight:700;background:#eff6ff;color:#1a56db;padding:2px 6px;border-radius:6px;">CS</span>';
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px; font-weight:700; background:#f4f0ff; color:#7c3aed; padding:2px 7px; border-radius:10px; text-transform:uppercase;">New Ticket</span>
                        ${sourceBadge}
                        ${dot}
                    </div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">${r.title}</div>
                    <div style="font-size:11px; color:var(--text-muted); margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="{{ url('/messages') }}/${r.id}"
                    onclick="markNotifAsRead('ticket', ${r.id})"
                    style="display:block; text-align:center; padding:6px; border-radius:6px; background:#f4f0ff; color:#7c3aed; font-size:11.5px; font-weight:600; text-decoration:none;">
                        <i class="ti ti-message-circle"></i> Open Ticket
                    </a>
                </div>`;

            } else if (r.type === 'stock') {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px; font-weight:700; background:#f5f5f5; color:#666; padding:2px 7px; border-radius:10px; text-transform:uppercase;">Out of Stock</span>
                        ${dot}
                    </div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">${r.title}</div>
                    <div style="font-size:11px; color:var(--text-muted); margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="{{ route('consumables') }}?openRefill=${r.id}"
                    onclick="markNotifAsRead('stock', ${r.id})"
                    style="display:block; text-align:center; padding:6px; border-radius:6px; background:#f5f5f5; color:#444; font-size:11.5px; font-weight:600; text-decoration:none;">
                        <i class="ti ti-plus"></i> Restock Item
                    </a>
                </div>`;

            } else {
                return `
                <div style="padding:12px 16px; border-bottom:1px solid var(--border); ${rowStyle}">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px;">
                        <span style="font-size:9px; font-weight:700; background:#eff6ff; color:#3b82f6; padding:2px 7px; border-radius:10px; text-transform:uppercase;">Consumable Request</span>
                        ${dot}
                    </div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">${r.title}</div>
                    <div style="font-size:11px; color:var(--text-muted); margin:2px 0 8px;">${r.subtitle} • ${r.created_at}</div>
                    <a href="#" onclick="reviewConsumableRequest(event, ${r.id})"
                    style="display:block; text-align:center; padding:6px; border-radius:6px; background:#eff6ff; color:#3b82f6; font-size:11.5px; font-weight:600; text-decoration:none;">
                        <i class="ti ti-eye"></i> Review Request
                    </a>
                </div>`;
            }
        }).join('') || '<div style="padding:20px; text-align:center; font-size:12px; color:#999;">No pending notifications.</div>';

    } catch (e) { /* silent fail */ }
}

function reviewConsumableRequest(e, id) {
    e.preventDefault();
    markNotifAsRead('consumable', id);
    pollNotifications();
    window.location.href = `{{ route('consumable-requests') }}?highlight=${id}`;
}

async function approveDeletion(id) {
    if (!confirm('Permanently delete this user account? This cannot be undone.')) return;
    await fetch(`/notifications/${id}/approve`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    pollNotifications();
}

async function rejectDeletion(id) {
    await fetch(`/notifications/${id}/reject`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    pollNotifications();
}

if (document.getElementById('notif-badge')) {
    pollNotifications();
    setInterval(pollNotifications, 8000);
}
</script>