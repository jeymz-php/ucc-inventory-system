@extends('layouts.app')
@section('title', 'System Status')
@section('page-title', 'System Status')

@section('content')

@php
    $imsDown = $currentIms?->status === 'down';
    $csDown  = $currentCs?->status  === 'down';
@endphp

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div><div class="stat-value">{{ $errorCount }}</div><div class="stat-label">Unresolved Errors</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $warningCount }}</div><div class="stat-label">Warnings</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-list"></i></div>
        <div><div class="stat-value">{{ $totalLogs }}</div><div class="stat-label">Total Logs</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-server"></i></div>
        <div>
            <div class="stat-value">{{ (!$imsDown && !$csDown) ? '2' : ($imsDown && $csDown ? '0' : '1') }}/2</div>
            <div class="stat-label">Systems Online</div>
        </div>
    </div>
</div>

{{-- IMS + CS Status Cards --}}
<div class="two-col" style="margin-bottom:1.25rem;">

    {{-- IMS Status --}}
    <div style="background:{{ $imsDown ? '#fff5f5' : '#f0faf4' }};
                border:2px solid {{ $imsDown ? '#e24b4a' : '#1a6b3a' }};
                border-radius:14px; padding:1.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:52px; height:52px; border-radius:50%;
                            background:{{ $imsDown ? '#e24b4a' : '#1a6b3a' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:22px; color:#fff;
                            {{ !$imsDown ? 'animation:pulse-green 2s infinite;' : '' }}">
                    <i class="ti ti-{{ $imsDown ? 'tools' : 'circle-check' }}"></i>
                </div>
                <div>
                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#888; margin-bottom:3px;">UCC-IMS</div>
                    <div style="font-size:18px; font-weight:700; color:{{ $imsDown ? '#c0392b' : '#1a6b3a' }};">
                        System is {{ $imsDown ? 'DOWN' : 'UP' }}
                    </div>
                    <div style="font-size:12px; color:#888; margin-top:3px;">
                        {{ $currentIms?->reason ?? 'No reason on record.' }}
                    </div>
                    <div style="font-size:11px; color:#aaa; margin-top:3px;">
                        Last changed: {{ $currentIms?->changed_at?->format('M d, Y h:i A') ?? 'Never' }}
                        @if($currentIms?->changedBy) by <strong>{{ $currentIms->changedBy->name }}</strong> @endif
                    </div>
                </div>
            </div>
            <button onclick="openToggleModal('ims')" class="btn-toggle {{ $imsDown ? 'btn-up' : 'btn-down' }}">
                <i class="ti ti-{{ $imsDown ? 'circle-check' : 'tools' }}"></i>
                Set {{ $imsDown ? 'UP' : 'DOWN' }}
            </button>
        </div>

        @if($historyIms->count())
        <div style="margin-top:1.2rem; border-top:1px solid {{ $imsDown ? '#f5c0c0' : '#c6e9d3' }}; padding-top:1rem;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#aaa; margin-bottom:8px;">Recent Changes</div>
            @foreach($historyIms as $h)
            <div style="display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px solid {{ $imsDown ? '#fde8e8' : '#e5f5eb' }};">
                <span>
                    <span class="{{ $h->status === 'down' ? 'log-badge log-error' : 'log-badge log-info' }}">{{ strtoupper($h->status) }}</span>
                    {{ Str::limit($h->reason, 40) }}
                </span>
                <span style="color:#aaa; flex-shrink:0; margin-left:8px;">{{ $h->changed_at?->format('M d h:i A') }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- CS Status --}}
    <div style="background:{{ $csDown ? '#fff5f5' : '#f0faf4' }};
                border:2px solid {{ $csDown ? '#e24b4a' : '#1a6b3a' }};
                border-radius:14px; padding:1.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div style="display:flex; align-items:center; gap:14px;">
                <div style="width:52px; height:52px; border-radius:50%;
                            background:{{ $csDown ? '#e24b4a' : '#1a6b3a' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:22px; color:#fff;
                            {{ !$csDown ? 'animation:pulse-green 2s infinite;' : '' }}">
                    <i class="ti ti-{{ $csDown ? 'tools' : 'circle-check' }}"></i>
                </div>
                <div>
                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#888; margin-bottom:3px;">UCC-CS</div>
                    <div style="font-size:18px; font-weight:700; color:{{ $csDown ? '#c0392b' : '#1a6b3a' }};">
                        System is {{ $csDown ? 'DOWN' : 'UP' }}
                    </div>
                    <div style="font-size:12px; color:#888; margin-top:3px;">
                        {{ $currentCs?->reason ?? 'No reason on record.' }}
                    </div>
                    <div style="font-size:11px; color:#aaa; margin-top:3px;">
                        Last changed: {{ $currentCs?->changed_at?->format('M d, Y h:i A') ?? 'Never' }}
                        @if($currentCs?->changedBy) by <strong>{{ $currentCs->changedBy->name }}</strong> @endif
                    </div>
                </div>
            </div>
            <button onclick="openToggleModal('cs')" class="btn-toggle {{ $csDown ? 'btn-up' : 'btn-down' }}">
                <i class="ti ti-{{ $csDown ? 'circle-check' : 'tools' }}"></i>
                Set {{ $csDown ? 'UP' : 'DOWN' }}
            </button>
        </div>

        @if($historyCs->count())
        <div style="margin-top:1.2rem; border-top:1px solid {{ $csDown ? '#f5c0c0' : '#c6e9d3' }}; padding-top:1rem;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#aaa; margin-bottom:8px;">Recent Changes</div>
            @foreach($historyCs as $h)
            <div style="display:flex; justify-content:space-between; font-size:12px; padding:4px 0; border-bottom:1px solid {{ $csDown ? '#fde8e8' : '#e5f5eb' }};">
                <span>
                    <span class="{{ $h->status === 'down' ? 'log-badge log-error' : 'log-badge log-info' }}">{{ strtoupper($h->status) }}</span>
                    {{ Str::limit($h->reason, 40) }}
                </span>
                <span style="color:#aaa; flex-shrink:0; margin-left:8px;">{{ $h->changed_at?->format('M d h:i A') }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Logs Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-terminal-2"></i> System Logs</div>
        <div style="display:flex; gap:8px;">
            <form method="POST" action="{{ route('system.logs.clear') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn-sm-action orange"
                        onclick="return confirm('Clear all resolved logs?')">
                    <i class="ti ti-trash"></i> Clear Resolved
                </button>
            </form>
        </div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>User</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="log-badge log-{{ $log->type }}">{{ $log->type }}</span></td>
                    <td style="font-size:12.5px; font-weight:600; max-width:200px;">{{ $log->title }}</td>
                    <td style="font-size:12px; color:var(--text-muted); max-width:240px;">{{ Str::limit($log->message, 60) }}</td>
                    <td style="font-size:12px;">{{ $log->user->name ?? 'System' }}</td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        @if($log->is_resolved)
                            <span class="chip-badge chip-status-active" style="font-size:10px;">Resolved</span>
                        @else
                            <span class="chip-badge chip-status-inactive" style="font-size:10px;">Open</span>
                        @endif
                    </td>
                    <td>
                        @if(!$log->is_resolved)
                        <form method="POST" action="{{ route('system.logs.resolve', $log) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-sm-action" style="background:#f0faf4; color:#1a6b3a;">
                                <i class="ti ti-check"></i> Resolve
                            </button>
                        </form>
                        @else
                        <span style="font-size:12px; color:#aaa;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state"><i class="ti ti-terminal-off"></i><p>No logs found.</p></div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} results</div>
        {{ $logs->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- TOGGLE MODAL (single modal handles both systems) --}}
<div class="modal-overlay" id="toggle-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm" id="toggle-modal-title">
                <i class="ti ti-tools"></i> Set System Status
            </div>
            <button class="modal-close" onclick="closeToggleModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;" id="toggle-modal-desc"></p>
        <form method="POST" action="{{ route('system.status.toggle') }}" id="toggle-form">
            @csrf
            <input type="hidden" name="system" id="toggle-system-input">
            <div class="modal-form-group">
                <div class="modal-label">Reason *</div>
                <textarea name="reason" class="modal-input" rows="3" required
                          placeholder="e.g., Scheduled maintenance for database updates."
                          style="padding-top:10px; padding-left:12px; resize:none;"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" id="toggle-submit-btn">
                <i class="ti ti-tools"></i> Confirm
            </button>
        </form>
    </div>
</div>

<style>
.btn-toggle {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 20px; border-radius: 10px;
    font-size: 13px; font-weight: 600;
    border: none; cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: opacity 0.2s; white-space: nowrap; flex-shrink: 0;
}
.btn-up   { background: #1a6b3a; color: #fff; }
.btn-down { background: #e24b4a; color: #fff; }
.btn-up:hover, .btn-down:hover { opacity: 0.88; }

.btn-sm-action {
    display: flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    border: none; cursor: pointer;
    font-family: 'Inter', sans-serif;
}
.btn-sm-action.orange { background: #fff8f0; color: #ef9f27; }
.btn-sm-action.orange:hover { background: #ef9f27; color: #fff; }

.log-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 6px;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.log-error   { background: #fff5f5; color: #e24b4a; }
.log-warning { background: #fff8f0; color: #ef9f27; }
.log-info    { background: #eff6ff; color: #3b82f6; }

@keyframes pulse-green {
    0%, 100% { box-shadow: 0 0 0 0 rgba(26,107,58,0.3); }
    50%       { box-shadow: 0 0 0 12px rgba(26,107,58,0); }
}

@media(max-width:768px) {
    .two-col { grid-template-columns: 1fr; }
}
</style>

@endsection

@push('scripts')
<script>
const imsDown = {{ $imsDown ? 'true' : 'false' }};
const csDown  = {{ $csDown  ? 'true' : 'false' }};

function openToggleModal(system) {
    const isDown    = system === 'ims' ? imsDown : csDown;
    const label     = system === 'ims' ? 'UCC-IMS (Inventory System)' : 'UCC-CS (Consumable System)';
    const action    = isDown ? 'UP' : 'DOWN';
    const iconClass = isDown ? 'ti-circle-check' : 'ti-tools';
    const btnColor  = isDown ? '#1a6b3a' : '#e24b4a';

    document.getElementById('toggle-system-input').value = system;
    document.getElementById('toggle-modal-title').innerHTML =
        `<i class="ti ${iconClass}"></i> Set ${label} ${action}`;
    document.getElementById('toggle-modal-desc').textContent = isDown
        ? `Setting ${label} back UP will allow all users to log in normally.`
        : `Setting ${label} DOWN will block all regular users. Only admins can still access the system.`;
    document.getElementById('toggle-submit-btn').style.background = btnColor;
    document.getElementById('toggle-submit-btn').innerHTML =
        `<i class="ti ${iconClass}"></i> Confirm — Set ${label} ${action}`;

    document.getElementById('toggle-modal').classList.add('open');
}

function closeToggleModal() {
    document.getElementById('toggle-modal').classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush