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

        {{-- QR Code (Consumable System) --}}
        <a href="#" class="topbar-btn" title="UCC-CS QR Code"
           onclick="event.preventDefault(); window.openQrModal();">
            <i class="ti ti-qrcode"></i>
        </a>

        {{-- Notifications (admin+) --}}
        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
        <div style="position:relative;" id="notif-wrap">
            <a href="#" class="topbar-btn" title="Notifications"
               onclick="event.preventDefault(); window.toggleNotifDropdown();">
                <i class="ti ti-bell"></i>
                <span id="notif-badge"
                      style="display:none; position:absolute; top:-4px; right:-4px;
                             background:#e24b4a; color:#fff; font-size:10px; font-weight:700;
                             border-radius:50%; width:18px; height:18px;
                             align-items:center; justify-content:center;">0</span>
            </a>

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
            <button class="topbar-btn" onclick="window.toggleSettings()" title="Settings" id="settings-btn">
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

                <a href="#" class="settings-item" onclick="event.preventDefault(); window.openChangePassword();">
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

{{-- QR Code Modal (UCC-CS) --}}
<div class="modal-overlay" id="qr-modal">
    <div class="modal-box-sm" style="text-align:center;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-qrcode"></i> UCC-CS QR Code</div>
            <button class="modal-close" onclick="window.closeModal('qr-modal')"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:12.5px; color:var(--text-secondary); margin-bottom:1.25rem;">
            Scan this code to open the Consumable Management System on a phone or another device.
        </p>
        <div id="qr-code-container" style="display:flex; align-items:center; justify-content:center; margin-bottom:1.1rem; min-height:200px; position:relative;">
            <div id="qr-loading" style="color:#999; font-size:13px;">Loading QR code...</div>
        </div>
        <div style="font-size:11.5px; color:#999; word-break:break-all; background:#fafafa; border-radius:8px; padding:8px 12px; margin-bottom:1rem;">
            https://consumable.ucc-caloocan.com/
        </div>
        
        {{-- Download Button --}}
        <button onclick="window.downloadQR()" id="download-qr-btn" 
                style="display:none; padding:10px 24px; background:var(--green-dark); color:#fff; 
                       border:none; border-radius:8px; font-size:13px; font-weight:600; 
                       cursor:pointer; font-family:'Inter',sans-serif; 
                       transition:opacity 0.15s; align-items:center; gap:8px; margin:0 auto;">
            <i class="ti ti-download"></i> Download QR Code
        </button>
    </div>
</div>

{{-- Change Password Modal --}}
<div class="modal-overlay" id="change-password-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-lock-password"></i> Change Password</div>
            <button class="modal-close" onclick="window.closeModal('change-password-modal')"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('password.change') }}" id="change-pass-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Current Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock modal-input-icon"></i>
                    <input type="password" name="current_password" class="modal-input"
                           placeholder="Enter current password" id="cur-pass">
                    <i class="ti ti-eye modal-input-right" onclick="window.toggleModalPass('cur-pass', this)"></i>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">New Password</div>
                <div class="modal-input-wrap">
                    <i class="ti ti-lock-open modal-input-icon"></i>
                    <input type="password" name="password" class="modal-input"
                           placeholder="Minimum 8 characters" id="new-pass" oninput="window.modalStrength()">
                    <i class="ti ti-eye modal-input-right" onclick="window.toggleModalPass('new-pass', this)"></i>
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
                           placeholder="Re-enter new password" id="conf-pass" oninput="window.modalMatch()">
                    <i class="ti ti-eye modal-input-right" onclick="window.toggleModalPass('conf-pass', this)"></i>
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

/* QR Code download button styles */
#download-qr-btn {
    display: none;
    padding: 10px 24px;
    background: var(--green-dark);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: opacity 0.15s, transform 0.15s;
    align-items: center;
    gap: 8px;
    margin: 0 auto;
}

#download-qr-btn:hover {
    opacity: 0.88;
    transform: translateY(-1px);
}

#download-qr-btn:active {
    transform: translateY(0);
}

#qr-display {
    display: flex;
    align-items: center;
    justify-content: center;
}

#qr-display canvas {
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
</style>

@push('scripts')
{{-- Load QRCode library from CDN --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
// Use IIFE to avoid variable conflicts
(function() {
    'use strict';

    // ── SOUND ──
    window.playNotifSound = function() {
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
    };

    // ── QR CODE ──
    let qrGenerated = false;
    let qrInstance = null;
    let qrCanvas = null;

    window.openQrModal = function() {
        const modal = document.getElementById('qr-modal');
        if (!modal) return;
        
        modal.classList.add('open');
        
        // Always regenerate QR code when opening to ensure fresh canvas
        const container = document.getElementById('qr-code-container');
        const loading = document.getElementById('qr-loading');
        const downloadBtn = document.getElementById('download-qr-btn');
        
        if (!container) return;
        
        // Check if QRCode library is loaded
        if (typeof QRCode === 'undefined') {
            if (loading) {
                loading.textContent = 'QR library loading... Please refresh.';
            }
            console.warn('QRCode library not loaded yet');
            return;
        }

        try {
            // Clear container but keep loading element
            container.innerHTML = '';
            
            // Create wrapper for QR code
            const qrContainer = document.createElement('div');
            qrContainer.id = 'qr-display';
            container.appendChild(qrContainer);

            // Generate QR code with black color
            qrInstance = new QRCode(qrContainer, {
                text: 'https://consumable.ucc-caloocan.com/',
                width: 200,
                height: 200,
                colorDark: '#000000',  // Black color
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
            
            // Find the canvas element created by QRCode
            const canvas = qrContainer.querySelector('canvas');
            if (canvas) {
                qrCanvas = canvas;
                canvas.id = 'qr-canvas-generated';
                // Show download button
                if (downloadBtn) {
                    downloadBtn.style.display = 'inline-flex';
                }
            }
            
            qrGenerated = true;
            console.log('QR code generated successfully');
        } catch(e) {
            console.error('Error generating QR code:', e);
            container.innerHTML = `
                <div style="color:var(--red); font-size:13px; padding:20px;">
                    <i class="ti ti-alert-triangle"></i> Could not generate QR code.
                    <br><span style="font-size:11px; color:#999;">Please refresh and try again.</span>
                </div>
            `;
        }
    };

    // ── DOWNLOAD QR CODE ──
    window.downloadQR = function() {
        const canvas = document.getElementById('qr-canvas-generated') || document.querySelector('#qr-display canvas');
        
        if (!canvas) {
            alert('QR code not generated yet. Please open the modal first.');
            return;
        }
        
        try {
            // Create a download link
            const link = document.createElement('a');
            link.download = 'UCC-CS_QR_Code.png';
            link.href = canvas.toDataURL('image/png');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch(e) {
            console.error('Error downloading QR code:', e);
            alert('Could not download QR code. Please try again.');
        }
    };

    // ── NOTIFICATION DROPDOWN TOGGLE ──
    let prevNotifCount = 0;

    window.toggleNotifDropdown = function() {
        const dd = document.getElementById('notif-dropdown');
        if (!dd) return;
        const isOpen = dd.classList.contains('open');
        // Close settings if open
        const sd = document.getElementById('settings-dropdown');
        if (sd) sd.classList.remove('open');
        dd.classList.toggle('open', !isOpen);
    };

    // Close notif dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('notif-wrap');
        if (wrap && !wrap.contains(e.target)) {
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.classList.remove('open');
        }
    });

    // ── SETTINGS DROPDOWN ──
    window.toggleSettings = function() {
        const dd = document.getElementById('settings-dropdown');
        if (!dd) return;
        const isOpen = dd.classList.contains('open');
        // Close notifications if open
        const nd = document.getElementById('notif-dropdown');
        if (nd) nd.classList.remove('open');
        dd.classList.toggle('open', !isOpen);
    };

    // Close settings when clicking outside
    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('settings-wrap');
        if (wrap && !wrap.contains(e.target)) {
            const dd = document.getElementById('settings-dropdown');
            if (dd) dd.classList.remove('open');
        }
    });

    // ── CHANGE PASSWORD ──
    window.openChangePassword = function() {
        const modal = document.getElementById('change-password-modal');
        if (modal) modal.classList.add('open');
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('open');
        }
    };

    window.toggleModalPass = function(inputId, icon) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ti ti-eye-off modal-input-right';
        } else {
            input.type = 'password';
            icon.className = 'ti ti-eye modal-input-right';
        }
    };

    window.modalStrength = function() {
        const pass = document.getElementById('new-pass');
        if (!pass) return;
        const val = pass.value;
        let score = 0;
        if (val.length >= 8) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;
        
        for (let i = 1; i <= 4; i++) {
            const seg = document.getElementById('ms' + i);
            if (seg) {
                seg.className = 'modal-seg' + (i <= score ? ' active' : '');
            }
        }
    };

    window.modalMatch = function() {
        const pass = document.getElementById('new-pass');
        const conf = document.getElementById('conf-pass');
        const hint = document.getElementById('conf-pass-hint');
        if (!pass || !conf || !hint) return;
        
        if (conf.value.length === 0) {
            hint.textContent = '';
            hint.className = 'modal-hint';
        } else if (pass.value === conf.value) {
            hint.textContent = '✓ Passwords match';
            hint.className = 'modal-hint success';
            hint.style.color = '#1a6b3a';
        } else {
            hint.textContent = '✗ Passwords do not match';
            hint.className = 'modal-hint error';
            hint.style.color = '#e24b4a';
        }
    };

    // ── NOTIFICATION FUNCTIONS ──
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

    window.markNotifAsRead = markNotifAsRead;

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
                window.playNotifSound();
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
                            <button onclick="window.approveDeletion(${r.id})"
                                    style="flex:1; padding:6px; border:none; border-radius:6px; background:#fff5f5; color:#e24b4a; font-size:11.5px; font-weight:600; cursor:pointer; font-family:inherit;">
                                <i class="ti ti-check"></i> Approve & Delete
                            </button>
                            <button onclick="window.rejectDeletion(${r.id})"
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
                        onclick="window.markNotifAsRead('ticket', ${r.id})"
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
                        onclick="window.markNotifAsRead('stock', ${r.id})"
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
                        <a href="#" onclick="window.reviewConsumableRequest(event, ${r.id})"
                        style="display:block; text-align:center; padding:6px; border-radius:6px; background:#eff6ff; color:#3b82f6; font-size:11.5px; font-weight:600; text-decoration:none;">
                            <i class="ti ti-eye"></i> Review Request
                        </a>
                    </div>`;
                }
            }).join('') || '<div style="padding:20px; text-align:center; font-size:12px; color:#999;">No pending notifications.</div>';

        } catch (e) { 
            console.warn('Notification poll error:', e);
        }
    }

    window.reviewConsumableRequest = function(e, id) {
        e.preventDefault();
        markNotifAsRead('consumable', id);
        pollNotifications();
        window.location.href = `{{ route('consumable-requests') }}?highlight=${id}`;
    };

    window.approveDeletion = async function(id) {
        if (!confirm('Permanently delete this user account? This cannot be undone.')) return;
        try {
            await fetch(`/notifications/${id}/approve`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            pollNotifications();
        } catch(e) {
            console.error('Error approving deletion:', e);
        }
    };

    window.rejectDeletion = async function(id) {
        try {
            await fetch(`/notifications/${id}/reject`, {
                method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            pollNotifications();
        } catch(e) {
            console.error('Error rejecting deletion:', e);
        }
    };

    // ── LIVE CLOCK ──
    function updateClock() {
        const clock = document.getElementById('live-clock');
        if (clock) {
            const now = new Date();
            clock.textContent = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
        }
    }

    // ── INITIALIZATION ──
    function init() {
        // Start live clock
        updateClock();
        setInterval(updateClock, 1000);

        // Start notification polling
        if (document.getElementById('notif-badge')) {
            pollNotifications();
            setInterval(pollNotifications, 8000);
        }

        console.log('Topbar initialized successfully');
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})(); // End of IIFE
</script>
@endpush