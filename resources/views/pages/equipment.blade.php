@extends('layouts.app')
@section('title', 'All Equipment')
@section('page-title', 'All Equipment Management')

@section('content')

{{-- Hero Banner --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-list-details"></i> All Equipment Management</div>
        <p class="hero-sub">Comprehensive view and management of all equipment across all categories. Filter, assign, and track equipment inventory in real-time.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total</span>{{ $stats['total'] }}</div>
            <div class="hero-chip"><span>Assigned</span>{{ $stats['assigned'] }}</div>
            <div class="hero-chip"><span>Maintenance</span>{{ $stats['maintenance'] }}</div>
        </div>
    </div>
    <div class="hero-right" style="display:flex; gap:8px;">
        <a href="#" class="btn-add"><i class="ti ti-file-text"></i> Report</a>
        <a href="#" class="btn-add"><i class="ti ti-plus"></i> Add Equipment</a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-stack-2"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Equipment</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['assigned'] }}</div><div class="stat-label">Assigned to Rooms</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $stats['unassigned'] }}</div><div class="stat-label">Unassigned</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-tool"></i></div>
        <div><div class="stat-value">{{ $stats['maintenance'] }}</div><div class="stat-label">Under Maintenance</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <form method="GET" action="{{ route('equipment') }}" id="equip-filter-form">

            <div style="margin-bottom:0.9rem;">
                <div class="filter-label">Equipment Type</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ $type === 'all' ? 'active' : '' }}" data-name="type" data-value="all">All</button>
                    <button type="button" class="filter-pill {{ $type === 'Computer' ? 'active' : '' }}" data-name="type" data-value="Computer">Computer</button>
                    <button type="button" class="filter-pill {{ $type === 'Kitchen' ? 'active' : '' }}" data-name="type" data-value="Kitchen">Kitchen</button>
                    <button type="button" class="filter-pill {{ $type === 'Office' ? 'active' : '' }}" data-name="type" data-value="Office">Office</button>
                    <button type="button" class="filter-pill {{ $type === 'Lab' ? 'active' : '' }}" data-name="type" data-value="Lab">Lab</button>
                    <button type="button" class="filter-pill {{ $type === 'General' ? 'active' : '' }}" data-name="type" data-value="General">General</button>
                </div>
            </div>

            <div style="margin-bottom:0.9rem;">
                <div class="filter-label">Campus</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ !$campusId ? 'active' : '' }}" data-name="campus_id" data-value="">All Campuses</button>
                    @foreach($campuses as $campus)
                    <button type="button" class="filter-pill {{ $campusId == $campus->id ? 'active' : '' }}" data-name="campus_id" data-value="{{ $campus->id }}">{{ $campus->name }}</button>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="filter-label">Status</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ $status === 'all' ? 'active' : '' }}" data-name="status" data-value="all">All</button>
                    <button type="button" class="filter-pill {{ $status === 'available' ? 'active' : '' }}" data-name="status" data-value="available">Available</button>
                    <button type="button" class="filter-pill {{ $status === 'assigned' ? 'active' : '' }}" data-name="status" data-value="assigned">Assigned</button>
                    <button type="button" class="filter-pill {{ $status === 'maintenance' ? 'active' : '' }}" data-name="status" data-value="maintenance">Maintenance</button>
                    <button type="button" class="filter-pill {{ $status === 'condemned' ? 'active' : '' }}" data-name="status" data-value="condemned">Condemned</button>
                </div>
            </div>

            <input type="hidden" name="type" id="hidden-type" value="{{ $type }}">
            <input type="hidden" name="campus_id" id="hidden-campus" value="{{ $campusId }}">
            <input type="hidden" name="status" id="hidden-status" value="{{ $status }}">
            <input type="hidden" name="search" id="hidden-search" value="{{ $search }}">
        </form>
    </div>
</div>

{{-- Table Card --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list"></i> Equipment Inventory ({{ $paginator->total() }} items)</div>
        <div style="position:relative; width:280px;">
            <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:14px;"></i>
            <input type="text" id="equip-search" value="{{ $search }}"
                   placeholder="Search name, serial, property no..."
                   style="width:100%; padding:8px 12px 8px 34px; border:1.5px solid var(--border);
                          border-radius:8px; font-size:12.5px; font-family:inherit; outline:none;">
        </div>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>Serial / Property No.</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Location</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $item)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $item->display_name ?? '—' }}</div>
                        @if($item->brand)
                        <div class="cell-secondary">{{ $item->brand }} {{ $item->model ?? '' }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="chip-badge chip-type">{{ $item->equipment_type }}</span>
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->serial_number ?? $item->property_no ?? '—' }}
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'available'   => 'chip-status-active',
                                'assigned'    => 'chip-campus',
                                'maintenance' => 'chip-equipment-zero',
                                'damaged'     => 'chip-status-inactive',
                                'condemned'   => 'chip-status-inactive',
                                'retired'     => 'chip-status-inactive',
                            ];
                        @endphp
                        <span class="chip-badge {{ $statusColors[$item->status] ?? 'chip-equipment-zero' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->assignedUser->name ?? '—' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->location->location_name ?? '—' }}
                    </td>
                    <td style="font-size:11px; color:var(--text-muted);">
                        {{ $item->updated_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" title="View"><i class="ti ti-eye"></i></button>
                            <button class="table-icon-btn edit" title="Edit"><i class="ti ti-edit"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-device-desktop"></i>
                            <p>No equipment found. Try adjusting your filters, or add new equipment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginator->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        {{ $paginator->onEachSide(1)->links() }}
    </div>
    @endif
</div>

<style>
.filter-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    color: var(--text-muted); margin-bottom: 8px;
}

.filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }

.filter-pill {
    padding: 7px 16px; border-radius: 20px;
    border: 1.5px solid var(--border);
    background: #fff; color: var(--text-secondary);
    font-size: 12.5px; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
    font-family: 'Inter', sans-serif;
}

.filter-pill:hover { border-color: var(--green-dark); color: var(--green-dark); }

.filter-pill.active {
    background: var(--green-dark);
    border-color: var(--green-dark);
    color: #fff;
    font-weight: 600;
}
</style>

@endsection

@push('scripts')
<script>
// Filter pill clicks
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const name  = this.dataset.name;
        const value = this.dataset.value;
        document.getElementById('hidden-' + (name === 'campus_id' ? 'campus' : name)).value = value;
        document.getElementById('equip-filter-form').submit();
    });
});

// Search with debounce
let equipSearchTimeout;
document.getElementById('equip-search').addEventListener('input', function() {
    clearTimeout(equipSearchTimeout);
    const val = this.value;
    equipSearchTimeout = setTimeout(() => {
        document.getElementById('hidden-search').value = val;
        document.getElementById('equip-filter-form').submit();
    }, 500);
});
</script>
@endpush