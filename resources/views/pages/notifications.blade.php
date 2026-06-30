@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

{{-- Tab Toggle --}}
<div style="display:flex; gap:10px; margin-bottom:1.25rem;">
    <a href="{{ route('notifications.index', ['tab' => 'deletions']) }}" class="tab-toggle-btn {{ $tab === 'deletions' ? 'active' : '' }}">
        <i class="ti ti-user-x"></i> Account Deletions
    </a>
    <a href="{{ route('notifications.index', ['tab' => 'consumables']) }}" class="tab-toggle-btn {{ $tab === 'consumables' ? 'active' : '' }}">
        <i class="ti ti-package"></i> Consumable Requests
    </a>
</div>

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

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <div class="filter-pills">
            <a href="{{ route('notifications.index', ['tab' => $tab, 'status' => 'pending']) }}" class="filter-pill {{ $status === 'pending' ? 'active' : '' }}" style="text-decoration:none;">Pending</a>
            <a href="{{ route('notifications.index', ['tab' => $tab, 'status' => 'approved']) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}" style="text-decoration:none;">Approved</a>
            <a href="{{ route('notifications.index', ['tab' => $tab, 'status' => 'rejected']) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}" style="text-decoration:none;">Rejected</a>
            <a href="{{ route('notifications.index', ['tab' => $tab, 'status' => 'all']) }}" class="filter-pill {{ $status === 'all' ? 'active' : '' }}" style="text-decoration:none;">All</a>
        </div>
    </div>
</div>

@if($tab === 'deletions')
<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-bell"></i> Account Deletion Requests ({{ $requests->total() }})</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Reviewed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $req->user->name ?? 'Deleted User' }}</div>
                        <div class="cell-secondary">{{ $req->user->email ?? '—' }}</div>
                    </td>
                    <td style="font-size:12px; max-width:240px;">{{ $req->reason ?: '—' }}</td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Approved</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                    <td style="font-size:12px;">{{ $req->reviewer->name ?? '—' }}</td>
                    <td>
                        @if($req->status === 'pending')
                        <div class="table-actions">
                            <button class="table-icon-btn" style="background:#fff5f5; color:var(--red);" title="Approve & Delete"
                                    onclick="approveDeletionPage({{ $req->id }}, '{{ addslashes($req->user->name ?? '') }}')">
                                <i class="ti ti-check"></i>
                            </button>
                            <button class="table-icon-btn" style="background:#f0faf4; color:var(--green-dark);" title="Reject"
                                    onclick="rejectDeletionPage({{ $req->id }})">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                        @else
                        <span class="chip-dash">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"><div class="empty-state"><i class="ti ti-bell-off"></i><p>No notifications found.</p></div></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} results</div>
        {{ $requests->onEachSide(1)->links() }}
    </div>
    @endif
</div>
@else
<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-package"></i> Consumable Requests ({{ $requests->total() }})</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Requester</th>
                    <th>Department</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td style="font-size:12px; font-weight:600;">{{ $req->reference_no }}</td>
                    <td>
                        <div class="cell-primary">{{ $req->requester->name ?? $req->recipient_name }}</div>
                    </td>
                    <td style="font-size:12.5px;">{{ $req->department }}</td>
                    <td><span class="chip-badge chip-type">{{ $req->items->count() }} items</span></td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @elseif(in_array($req->status, ['approved','partial']))
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> {{ ucfirst($req->status) }}</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $req->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <a href="{{ route('consumable-requests') }}" class="table-icon-btn view" title="Review in Consumables"><i class="ti ti-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7"><div class="empty-state"><i class="ti ti-package-off"></i><p>No consumable requests found.</p></div></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} results</div>
        {{ $requests->onEachSide(1)->links() }}
    </div>
    @endif
</div>
@endif

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