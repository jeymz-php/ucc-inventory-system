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
        <div style="position:relative;">
            <a href="#" class="topbar-btn" title="Notifications" onclick="event.preventDefault(); toggleNotifDropdown();">
                <i class="ti ti-bell"></i>
                <span id="notif-badge" style="display:none; position:absolute; top:-4px; right:-4px; background:#e24b4a; color:#fff; font-size:10px; font-weight:700; border-radius:50%; width:18px; height:18px; align-items:center; justify-content:center; display:flex;">0</span>
            </a>

            <div id="notif-dropdown" class="settings-dropdown" style="width:340px;">
                <div class="settings-header" style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <div class="settings-user-name">Notifications</div>
                        <div class="settings-user-email" id="notif-summary">No pending requests</div>
                    </div>
                    <a href="{{ route('notifications.index') }}" style="font-size:11px; color:var(--green-dark); font-weight:600; text-decoration:none;">View All →</a>
                </div>
                <div id="notif-list" style="max-height:320px; overflow-y:auto;"></div>
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

                @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                <a href="#" class="settings-item">
                    <i class="ti ti-database-export"></i>
                    Backup &amp; Restore Data
                </a>
                @endif

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
            @csrf
            @method('PUT')

            <div class="modal-form-group">
                <div class="modal-label">Current Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock modal-input-icon"></i>
                    <input type="password" name="current_password" class="modal-input"
                           placeholder="Enter current password" id="cur-pass">
                    <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('cur-pass', this)"></i>
                </div>
                <div class="modal-hint" id="cur-pass-hint"></div>
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
                <div class="modal-hint" id="new-pass-hint"></div>
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
/* ── SETTINGS DROPDOWN ── */
.settings-wrap { position: relative; }

.settings-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    min-width: 220px;
    z-index: 200;
    overflow: hidden;
    animation: dropIn 0.18s ease;
}

.settings-dropdown.open { display: block; }

@keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.settings-header {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--border);
    background: var(--green-light);
}

.settings-user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); }
.settings-user-email { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.settings-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 1rem;
    font-size: 13px; font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: background 0.15s;
    width: 100%; background: none;
    border: none; cursor: pointer;
    font-family: 'Inter', sans-serif;
    text-align: left;
}

.settings-item:hover { background: var(--green-light); color: var(--green-dark); }
.settings-item i { font-size: 16px; color: var(--green-dark); }

.settings-logout { color: var(--red) !important; }
.settings-logout i { color: var(--red) !important; }
.settings-logout:hover { background: #fff5f5 !important; }

.settings-divider { height: 1px; background: var(--border); margin: 4px 0; }

/* ── CHANGE PASSWORD MODAL ── */
/* .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45); z-index: 300;
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }

.modal-box-sm {
    background: #fff; border-radius: 14px;
    padding: 1.5rem; width: 100%; max-width: 420px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
    animation: dropIn 0.2s ease;
}

.modal-header-row {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}

.modal-title-sm {
    font-size: 16px; font-weight: 700;
    color: var(--text-primary);
    display: flex; align-items: center; gap: 8px;
}
.modal-title-sm i { color: var(--green-dark); }

.modal-close {
    width: 28px; height: 28px; border-radius: 6px;
    border: 1px solid var(--border);
    background: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: var(--text-muted); font-size: 14px;
}
.modal-close:hover { background: #fff5f5; color: var(--red); border-color: var(--red); }

.modal-form-group { margin-bottom: 0.85rem; }

.modal-label {
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 1px;
    color: #555; margin-bottom: 5px;
}

.modal-input-wrap { position: relative; }

.modal-input-icon {
    position: absolute; left: 10px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; font-size: 14px; pointer-events: none;
}

.modal-input-right {
    position: absolute; right: 10px; top: 50%;
    transform: translateY(-50%);
    color: #aaa; font-size: 14px; cursor: pointer;
}

.modal-input {
    width: 100%; padding: 10px 34px;
    border: 1.5px solid #e0e0e0; border-radius: 8px;
    font-size: 13px; font-family: 'Inter', sans-serif;
    color: #111; outline: none; transition: border-color 0.2s;
}
.modal-input:focus { border-color: var(--green-dark); }

.modal-strength-bar { display: flex; gap: 4px; margin-top: 5px; }
.modal-seg { flex: 1; height: 3px; border-radius: 2px; background: #e0e0e0; transition: background 0.3s; }

.modal-hint { font-size: 11px; margin-top: 3px; }
.modal-hint.error   { color: var(--red); }
.modal-hint.success { color: var(--green-dark); }

.modal-btn-primary {
    width: 100%; padding: 11px;
    background: var(--green-dark); color: #fff;
    border: none; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; font-family: 'Inter', sans-serif;
    display: flex; align-items: center; justify-content: center;
    gap: 8px; margin-top: 1rem;
    transition: background 0.2s;
}
.modal-btn-primary:hover { background: #155a30; } */
</style>

<script>
// Settings dropdown
function toggleSettings() {
    document.getElementById('settings-dropdown').classList.toggle('open');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('settings-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('settings-dropdown').classList.remove('open');
    }
});

// Change password modal
function openChangePassword() {
    document.getElementById('settings-dropdown').classList.remove('open');
    document.getElementById('change-password-modal').classList.add('open');
}

function closeChangePassword() {
    document.getElementById('change-password-modal').classList.remove('open');
}

function toggleModalPass(id, icon) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('ti-eye','ti-eye-off'); }
    else { inp.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
}

function modalStrength() {
    const val  = document.getElementById('new-pass').value;
    const segs = ['ms1','ms2','ms3','ms4'].map(id => document.getElementById(id));
    let score  = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e24b4a','#ef9f27','#1D9E75','#1a6b3a'];
    segs.forEach((s,i) => s.style.background = i < score ? colors[score-1] : '#e0e0e0');
}

function modalMatch() {
    const pass  = document.getElementById('new-pass').value;
    const conf  = document.getElementById('conf-pass').value;
    const hint  = document.getElementById('conf-pass-hint');
    if (!conf) { hint.textContent = ''; return; }
    if (pass === conf) { hint.textContent = 'Passwords match.'; hint.className = 'modal-hint success'; }
    else               { hint.textContent = 'Passwords do not match.'; hint.className = 'modal-hint error'; }
}

// Mobile menu
if (window.innerWidth <= 768) {
    const btn = document.getElementById('menu-btn');
    if (btn) btn.style.display = 'flex';
}
window.addEventListener('resize', () => {
    const btn = document.getElementById('menu-btn');
    if (btn) btn.style.display = window.innerWidth <= 768 ? 'flex' : 'none';
});
</script>