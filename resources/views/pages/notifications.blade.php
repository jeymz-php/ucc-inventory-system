@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-clock"></i></div>
        <div><div class="stat-value">{{ $stats['pending'] }}</div><div class="stat-label">Pending</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['approved'] }}</div><div class="stat-label">Approved</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-circle-x"></i></div>
        <div><div class="stat-value">{{ $stats['rejected'] }}</div><div class="stat-label">Rejected</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem; display:flex; gap:16px; flex-wrap:wrap; align-items:center;">

        {{-- Type filter --}}
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:6px;">Type</div>
            <div class="filter-pills">
                <a href="{{ route('notifications.index', ['status' => $status, 'type' => 'all']) }}" class="filter-pill {{ $type === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('notifications.index', ['status' => $status, 'type' => 'deletion']) }}" class="filter-pill {{ $type === 'deletion' ? 'active' : '' }}">Account Deletions</a>
                <a href="{{ route('notifications.index', ['status' => $status, 'type' => 'consumable']) }}" class="filter-pill {{ $type === 'consumable' ? 'active' : '' }}">Consumable Requests</a>
            </div>
        </div>

        {{-- Status filter --}}
        <div>
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); margin-bottom:6px;">Status</div>
            <div class="filter-pills">
                <a href="{{ route('notifications.index', ['status' => 'all', 'type' => $type]) }}" class="filter-pill {{ $status === 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('notifications.index', ['status' => 'pending', 'type' => $type]) }}" class="filter-pill {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('notifications.index', ['status' => 'approved', 'type' => $type]) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('notifications.index', ['status' => 'rejected', 'type' => $type]) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}">Rejected</a>
            </div>
        </div>

    </div>
</div>

{{-- Unified Table --}}
<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-bell"></i> All Notifications ({{ $paginated->total() }})</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Reviewed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginated as $notif)
                <tr>
                    <td>
                        @if($notif['type'] === 'deletion')
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-user-x" style="font-size:10px"></i> Account Deletion</span>
                        @else
                            <span class="chip-badge chip-campus"><i class="ti ti-package" style="font-size:10px"></i> Consumable Request</span>
                        @endif
                    </td>
                    <td>
                        <div class="cell-primary">{{ $notif['title'] }}</div>
                        <div class="cell-secondary">{{ $notif['subtitle'] }}</div>
                    </td>
                    <td style="font-size:12px; max-width:200px;">{{ $notif['detail'] ?: '—' }}</td>
                    <td>
                        @if($notif['status'] === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @elseif(in_array($notif['status'], ['approved', 'partial']))
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> {{ ucfirst($notif['status']) }}</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $notif['created_at']->format('M d, Y h:i A') }}</td>
                    <td style="font-size:12px;">{{ $notif['reviewed_by'] }}</td>
                    <td>
                        <div class="table-actions">
                            @if($notif['type'] === 'deletion' && $notif['status'] === 'pending')
                                <button class="table-icon-btn" style="background:#fff5f5; color:var(--red);" title="Approve & Delete"
                                        onclick="approveDeletionPage({{ $notif['id'] }}, '{{ addslashes($notif['title']) }}')">
                                    <i class="ti ti-check"></i>
                                </button>
                                <button class="table-icon-btn" style="background:#f0faf4; color:var(--green-dark);" title="Reject"
                                        onclick="rejectDeletionPage({{ $notif['id'] }})">
                                    <i class="ti ti-x"></i>
                                </button>
                            @elseif($notif['type'] === 'consumable')
                                <a href="{{ route('consumable-requests') }}?highlight={{ $notif['id'] }}"
                                   class="table-icon-btn view" title="View Request">
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            @else
                                <span class="chip-dash">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-bell-off"></i>
                            <p>No notifications found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginated->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $paginated->firstItem() }} to {{ $paginated->lastItem() }} of {{ $paginated->total() }} results
        </div>
        {{ $paginated->onEachSide(1)->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
async function approveDeletionPage(id, name) {
    if (!confirm(`Permanently delete ${name}'s account? This cannot be undone.`)) return;
    await fetch(`/notifications/${id}/approve`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    window.location.reload();
}

async function rejectDeletionPage(id) {
    if (!confirm('Reject this deletion request?')) return;
    await fetch(`/notifications/${id}/reject`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    window.location.reload();
}
</script>
@endpush