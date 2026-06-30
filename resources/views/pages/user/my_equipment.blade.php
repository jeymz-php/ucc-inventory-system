@extends('layouts.app')
@section('title', 'My Equipment')
@section('page-title', 'My Assigned Equipment')

@section('content')

<a href="{{ route('dashboard') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Dashboard
</a>

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-device-desktop"></i> My Assigned Equipment</div>
        <p class="hero-sub">Equipment items you are accountable for in the UCC Inventory System.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total:</span>{{ $stats['total'] }}</div>
            <div class="hero-chip"><span>At Location:</span>{{ $stats['assigned'] }}</div>
            <div class="hero-chip"><span>Maintenance:</span>{{ $stats['maintenance'] }}</div>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-stack-2"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Assigned</div>
            <div class="stat-sub"><i class="ti ti-user-check" style="font-size:11px"></i> Under your accountability</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-map-pin"></i></div>
        <div>
            <div class="stat-value">{{ $stats['assigned'] }}</div>
            <div class="stat-label">Placed at Location</div>
            <div class="stat-sub"><i class="ti ti-building" style="font-size:11px"></i> Assigned to a room</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-tool"></i></div>
        <div>
            <div class="stat-value">{{ $stats['maintenance'] }}</div>
            <div class="stat-label">Under Maintenance</div>
            <div class="stat-sub"><i class="ti ti-alert-circle" style="font-size:11px"></i> Needs attention</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-category"></i></div>
        <div>
            <div class="stat-value">{{ count(array_filter($stats['by_type'])) }}</div>
            <div class="stat-label">Categories</div>
            <div class="stat-sub"><i class="ti ti-layout-grid" style="font-size:11px"></i> Equipment types</div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <form method="GET" action="{{ route('my-equipment') }}" id="my-equip-filter-form">
            <div style="position:relative; margin-bottom:0.9rem;">
                <i class="ti ti-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, serial, or property number..."
                       style="width:100%; padding:12px 16px 12px 40px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; font-family:inherit; outline:none;">
            </div>

            <div style="margin-bottom:0.75rem;">
                <div class="filter-label">Equipment Type</div>
                <div class="filter-pills">
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'type'), ['type' => 'all'])) }}" class="filter-pill {{ $type === 'all' ? 'active' : '' }}">All</a>
                    @foreach(['Computer', 'Kitchen', 'Office', 'Lab', 'General'] as $equipType)
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'type'), ['type' => $equipType])) }}" class="filter-pill {{ $type === $equipType ? 'active' : '' }}">{{ $equipType }}</a>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="filter-label">Status</div>
                <div class="filter-pills">
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}" class="filter-pill {{ $status === 'all' ? 'active' : '' }}">All</a>
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'status'), ['status' => 'available'])) }}" class="filter-pill {{ $status === 'available' ? 'active' : '' }}">Available</a>
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'status'), ['status' => 'assigned'])) }}" class="filter-pill {{ $status === 'assigned' ? 'active' : '' }}">Assigned</a>
                    <a href="{{ route('my-equipment', array_merge(request()->except('page', 'status'), ['status' => 'maintenance'])) }}" class="filter-pill {{ $status === 'maintenance' ? 'active' : '' }}">Maintenance</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list"></i> My Equipment ({{ $paginator->total() }})</div>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>Serial / Property No.</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $item)
                @php
                    $statusColors = [
                        'available'   => 'chip-status-active',
                        'assigned'    => 'chip-campus',
                        'maintenance' => 'chip-equipment-zero',
                        'damaged'     => 'chip-status-inactive',
                        'retired'     => 'chip-status-inactive',
                    ];
                @endphp
                <tr>
                    <td>
                        <div class="cell-primary">{{ $item->display_name ?? '—' }}</div>
                        @if($item->brand)
                        <div class="cell-secondary">{{ $item->brand }} {{ $item->model ?? '' }}</div>
                        @endif
                    </td>
                    <td><span class="chip-badge chip-type">{{ $item->equipment_type }}</span></td>
                    <td style="font-size:12px;">{{ $item->serial_number ?? $item->property_no ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $item->condition_status ?? '—' }}</td>
                    <td>
                        <span class="chip-badge {{ $statusColors[$item->status] ?? 'chip-equipment-zero' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td style="font-size:12px;">{{ $item->location->location_name ?? 'Unassigned / Storage' }}</td>
                    <td style="font-size:11px; color:var(--text-muted);">{{ $item->updated_at->format('M d, Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('my-equipment.show', [$item->type_slug, $item->id]) }}" class="table-icon-btn view" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-device-desktop-off"></i>
                            <p>No equipment is currently assigned to your account.</p>
                            <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">When an administrator assigns equipment to you, it will appear here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginator->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results</div>
        {{ $paginator->onEachSide(1)->links() }}
    </div>
    @endif
</div>

@endsection
