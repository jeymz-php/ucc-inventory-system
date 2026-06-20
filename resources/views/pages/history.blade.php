@extends('layouts.app')
@section('title', 'History')
@section('page-title', 'Activity History')

@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-history"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Logs</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-calendar"></i></div>
        <div><div class="stat-value">{{ $stats['today'] }}</div><div class="stat-label">Today's Activity</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-plus"></i></div>
        <div><div class="stat-value">{{ $stats['creates'] }}</div><div class="stat-label">Items Created</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-trash"></i></div>
        <div><div class="stat-value">{{ $stats['deletes'] }}</div><div class="stat-label">Items Deleted</div></div>
    </div>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('history') }}" id="history-filter-form" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <div style="flex:1; min-width:220px; position:relative;">
                <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" id="history-search" value="{{ $search }}"
                       placeholder="Search description..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>
            <select name="module" id="history-module" style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                <option value="">All Modules</option>
                <option value="Equipment" {{ $module === 'Equipment' ? 'selected' : '' }}>Equipment</option>
                <option value="Location" {{ $module === 'Location' ? 'selected' : '' }}>Location</option>
                <option value="Category" {{ $module === 'Category' ? 'selected' : '' }}>Category</option>
                <option value="User" {{ $module === 'User' ? 'selected' : '' }}>User</option>
            </select>
            <select name="action" id="history-action" style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                <option value="">All Actions</option>
                <option value="create" {{ $action === 'create' ? 'selected' : '' }}>Create</option>
                <option value="update" {{ $action === 'update' ? 'selected' : '' }}>Update</option>
                <option value="delete" {{ $action === 'delete' ? 'selected' : '' }}>Delete</option>
                <option value="condemn" {{ $action === 'condemn' ? 'selected' : '' }}>Condemn</option>
                <option value="archive" {{ $action === 'archive' ? 'selected' : '' }}>Archive</option>
                <option value="activate" {{ $action === 'activate' ? 'selected' : '' }}>Activate</option>
                <option value="deactivate" {{ $action === 'deactivate' ? 'selected' : '' }}>Deactivate</option>
            </select>
            <button type="submit" class="btn-table-action green"><i class="ti ti-filter"></i> Filter</button>
            @if($search || $module || $action)
            <a href="{{ route('history') }}" class="btn-table-action" style="background:#f5f5f5; color:#666;"><i class="ti ti-x"></i> Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-list"></i> Activity Log ({{ $logs->total() }})</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                    <th>Performed By</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>
                        @php
                            $actionColors = [
                                'create'     => ['bg' => '#f0faf4', 'color' => '#1a6b3a', 'icon' => 'ti-plus'],
                                'update'     => ['bg' => '#eff6ff', 'color' => '#3b82f6', 'icon' => 'ti-edit'],
                                'delete'     => ['bg' => '#fff5f5', 'color' => '#e24b4a', 'icon' => 'ti-trash'],
                                'condemn'    => ['bg' => '#fff8f0', 'color' => '#ef9f27', 'icon' => 'ti-alert-triangle'],
                                'archive'    => ['bg' => '#fff8f0', 'color' => '#ef9f27', 'icon' => 'ti-archive'],
                                'restore'    => ['bg' => '#f0faf4', 'color' => '#1a6b3a', 'icon' => 'ti-archive-off'],
                                'activate'   => ['bg' => '#f0faf4', 'color' => '#1a6b3a', 'icon' => 'ti-eye'],
                                'deactivate' => ['bg' => '#fff5f5', 'color' => '#e24b4a', 'icon' => 'ti-eye-off'],
                            ];
                            $ac = $actionColors[$log->action] ?? ['bg' => '#f5f5f5', 'color' => '#999', 'icon' => 'ti-circle'];
                        @endphp
                        <span class="chip-badge" style="background:{{ $ac['bg'] }}; color:{{ $ac['color'] }};">
                            <i class="ti {{ $ac['icon'] }}" style="font-size:11px"></i> {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td><span class="chip-badge chip-type">{{ $log->module }}</span></td>
                    <td style="font-size:13px;">{{ $log->description }}</td>
                    <td style="font-size:12px;">{{ $log->user->name ?? 'System' }}</td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="ti ti-history"></i>
                            <p>No activity recorded yet.</p>
                        </div>
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

@endsection

@push('scripts')
<script>
let historySearchTimeout;
document.getElementById('history-search').addEventListener('input', function() {
    clearTimeout(historySearchTimeout);
    historySearchTimeout = setTimeout(() => document.getElementById('history-filter-form').submit(), 500);
});
document.getElementById('history-module').addEventListener('change', () => document.getElementById('history-filter-form').submit());
document.getElementById('history-action').addEventListener('change', () => document.getElementById('history-filter-form').submit());
</script>
@endpush