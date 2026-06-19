@extends('layouts.app')
@section('title', 'System Status')
@section('page-title', 'System Status')

@section('content')

@php $isDown = $current?->status === 'down'; @endphp

{{-- Status Banner --}}
<div style="background: {{ $isDown ? '#fff5f5' : '#f0faf4' }};
            border: 2px solid {{ $isDown ? '#e24b4a' : '#1a6b3a' }};
            border-radius: 14px; padding: 1.5rem 2rem;
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
    <div style="display:flex; align-items:center; gap:16px;">
        <div style="width:56px; height:56px; border-radius:50%;
                    background: {{ $isDown ? '#e24b4a' : '#1a6b3a' }};
                    display:flex; align-items:center; justify-content:center;
                    font-size:24px; color:#fff;
                    {{ !$isDown ? 'animation: pulse-green 2s infinite;' : '' }}">
            <i class="ti ti-{{ $isDown ? 'tools' : 'circle-check' }}"></i>
        </div>
        <div>
            <div style="font-size:20px; font-weight:700; color:{{ $isDown ? '#c0392b' : '#1a6b3a' }};">
                System is {{ $isDown ? 'DOWN' : 'UP' }}
            </div>
            <div style="font-size:13px; color:#666; margin-top:3px;">
                {{ $current?->reason ?? 'No reason provided.' }}
            </div>
            <div style="font-size:11px; color:#aaa; margin-top:4px;">
                Last changed: {{ $current?->changed_at?->format('M d, Y h:i A') }}
                @if($current?->changedBy)
                    by <strong>{{ $current->changedBy->name }}</strong>
                @endif
            </div>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button onclick="openToggleModal()" class="btn-toggle {{ $isDown ? 'btn-up' : 'btn-down' }}">
        <i class="ti ti-{{ $isDown ? 'circle-check' : 'tools' }}"></i>
        Set System {{ $isDown ? 'UP' : 'DOWN' }}
    </button>
</div>

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div>
            <div class="stat-value">{{ $errorCount }}</div>
            <div class="stat-label">Unresolved Errors</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $warningCount }}</div>
            <div class="stat-label">Warnings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-list"></i></div>
        <div>
            <div class="stat-value">{{ $totalLogs }}</div>
            <div class="stat-label">Total Logs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-history"></i></div>
        <div>
            <div class="stat-value">{{ $history->count() }}</div>
            <div class="stat-label">Status Changes</div>
        </div>
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
                    <th>URL</th>
                    <th>IP</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        @if($log->type === 'error')
                            <span class="log-badge log-error"><i class="ti ti-alert-circle"></i> Error</span>
                        @elseif($log->type === 'warning')
                            <span class="log-badge log-warning"><i class="ti ti-alert-triangle"></i> Warning</span>
                        @else
                            <span class="log-badge log-info"><i class="ti ti-info-circle"></i> Info</span>
                        @endif
                    </td>
                    <td style="font-size:13px; font-weight:500; max-width:160px;">{{ $log->title }}</td>
                    <td style="font-size:12px; color:var(--text-secondary); max-width:200px;">
                        {{ Str::limit($log->message, 80) }}
                    </td>
                    <td style="font-size:12px;">
                        @if($log->user)
                            <div style="font-weight:500;">{{ $log->user->name }}</div>
                            <div style="color:var(--text-muted);">{{ $log->user_role }}</div>
                        @else
                            <span style="color:var(--text-muted);">Guest</span>
                        @endif
                    </td>
                    <td style="font-size:11px; color:var(--text-muted); max-width:140px; word-break:break-all;">
                        {{ Str::limit($log->url, 50) }}
                    </td>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $log->ip_address }}</td>
                    <td style="font-size:11px; color:var(--text-muted);">
                        {{ $log->created_at->format('M d h:i A') }}
                    </td>
                    <td>
                        @if($log->is_resolved)
                            <span class="status-badge active">Resolved</span>
                        @else
                            <span class="status-badge archived">Open</span>
                        @endif
                    </td>
                    <td>
                        @if(!$log->is_resolved)
                        <form method="POST" action="{{ route('system.logs.resolve', $log) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon-action green" title="Mark Resolved">
                                <i class="ti ti-check"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ti ti-circle-check"></i>
                            <p>No logs yet. System is running clean!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
        {{ $logs->links() }}
    </div>
    @endif
</div>

{{-- Toggle Modal --}}
<div class="modal-overlay" id="toggle-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-{{ $isDown ? 'circle-check' : 'tools' }}"></i>
                Set System {{ $isDown ? 'UP' : 'DOWN' }}
            </div>
            <button class="modal-close" onclick="closeToggleModal()"><i class="ti ti-x"></i></button>
        </div>

        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            @if($isDown)
                Setting the system back UP will allow all users to log in and use the system normally.
            @else
                Setting the system DOWN will block all regular users. Only admins can still log in via the admin login page.
            @endif
        </p>

        <form method="POST" action="{{ route('system.status.toggle') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Reason *</div>
                <textarea name="reason" class="modal-input" rows="3" required
                          placeholder="{{ $isDown ? 'e.g., Maintenance completed, system restored.' : 'e.g., Scheduled maintenance for database updates.' }}"
                          style="padding-top:10px; padding-left:12px; resize:none;"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:{{ $isDown ? '#1a6b3a' : '#e24b4a' }}">
                <i class="ti ti-{{ $isDown ? 'circle-check' : 'tools' }}"></i>
                Confirm — Set System {{ $isDown ? 'UP' : 'DOWN' }}
            </button>
        </form>
    </div>
</div>

<style>
.btn-toggle {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 24px; border-radius: 10px;
    font-size: 14px; font-weight: 600;
    border: none; cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: opacity 0.2s;
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
</style>

@endsection

@push('scripts')
<script>
function openToggleModal()  { document.getElementById('toggle-modal').classList.add('open'); }
function closeToggleModal() { document.getElementById('toggle-modal').classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush