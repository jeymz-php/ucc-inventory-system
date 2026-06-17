@extends('layouts.app')
@section('title', 'Inventory')
@section('page-title', 'Inventory')

@section('content')

{{-- Hero Banner --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-building-warehouse"></i> Inventory Management System</div>
        <p class="hero-sub">Organize and manage all your equipment across different categories and locations. Track assignments, monitor usage, and maintain inventory control.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Categories</span>{{ $stats['categories'] }}</div>
            <div class="hero-chip"><span>Rooms</span>{{ $stats['rooms'] }}</div>
            <div class="hero-chip"><span>Equipment</span>{{ $stats['equipment'] }}</div>
        </div>
    </div>
    <div class="hero-right">
        <a href="#" class="btn-add"><i class="ti ti-plus"></i> New Category</a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-stack-2"></i></div>
        <div><div class="stat-value">{{ $stats['categories'] }}</div><div class="stat-label">Total Categories</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-door"></i></div>
        <div><div class="stat-value">{{ $stats['rooms'] }}</div><div class="stat-label">Active Rooms</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-device-desktop"></i></div>
        <div><div class="stat-value">{{ $stats['equipment'] }}</div><div class="stat-label">Equipment Items</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-chart-bar"></i></div>
        <div><div class="stat-value">{{ $stats['rooms'] > 0 ? round($stats['equipment'] / $stats['rooms'], 1) : 0 }}</div><div class="stat-label">Avg Items/Room</div></div>
    </div>
</div>

{{-- Search + Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('inventory') }}" id="inventory-filter-form"
              style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">

            <div style="flex:1; min-width:220px; position:relative;">
                <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" id="search-input" value="{{ $search }}"
                       placeholder="Search location types..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border);
                              border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>

            <select name="campus_id" id="campus-filter"
                    style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px;
                           font-size:13px; font-family:inherit; outline:none; min-width:180px;">
                <option value="">All Campuses</option>
                @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ $campusId == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn-table-action green">
                <i class="ti ti-filter"></i> Filter
            </button>

            @if($search || $campusId)
            <a href="{{ route('inventory') }}" class="btn-table-action" style="background:#f5f5f5; color:#666;">
                <i class="ti ti-x"></i> Clear
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Location Type Cards --}}
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1.25rem;">
    @forelse($locationTypes as $type)
    <div class="loc-type-card">
        <div class="loc-type-header" style="background: linear-gradient(135deg, {{ $type->color_primary }}, {{ $type->color_secondary }});">
            <div class="loc-type-icon"><i class="fa {{ $type->icon_class }}"></i></div>
            <div class="loc-type-name">{{ $type->type_name }}</div>
            <div class="loc-type-desc">{{ Str::limit($type->description, 90) }}</div>
        </div>
        <div class="loc-type-body">
            <div class="loc-type-stats">
                <div>
                    <div class="loc-stat-value">{{ $type->locations_count }}</div>
                    <div class="loc-stat-label">Rooms</div>
                </div>
                <div>
                    <div class="loc-stat-value">{{ $type->equipment_count }}</div>
                    <div class="loc-stat-label">Equipment</div>
                </div>
            </div>
            <div class="loc-type-footer">
                <span class="loc-type-chip"><i class="ti ti-map-pin" style="font-size:11px"></i> {{ $type->type_code }}</span>
                <a href="{{ route('inventory.show', $type) }}" class="btn-manage">
                    Manage <i class="ti ti-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1;">
        <div class="empty-state">
            <i class="ti ti-building-warehouse"></i>
            <p>No location types found for this campus yet.</p>
        </div>
    </div>
    @endforelse
</div>

<style>
.loc-type-card {
    background: #fff; border-radius: 14px;
    border: 1px solid var(--border);
    overflow: hidden; box-shadow: var(--card-shadow);
    transition: transform 0.18s;
}
.loc-type-card:hover { transform: translateY(-3px); }

.loc-type-header {
    padding: 1.25rem; color: #fff; min-height: 130px;
    position: relative;
}
.loc-type-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; margin-bottom: 0.75rem;
}
.loc-type-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; line-height: 1.3; }
.loc-type-desc { font-size: 12px; color: rgba(255,255,255,0.85); line-height: 1.5; }

.loc-type-body { padding: 1.1rem 1.25rem; }
.loc-type-stats { display: flex; gap: 1.5rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
.loc-stat-value { font-size: 22px; font-weight: 700; color: var(--text-primary); }
.loc-stat-label { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

.loc-type-footer { display: flex; align-items: center; justify-content: space-between; }
.loc-type-chip {
    font-size: 11px; color: var(--text-secondary);
    background: #f4f6f5; padding: 4px 10px; border-radius: 20px;
    display: flex; align-items: center; gap: 4px;
}

.btn-manage {
    display: flex; align-items: center; gap: 6px;
    background: var(--green-dark); color: #fff;
    padding: 7px 16px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    text-decoration: none; transition: background 0.2s;
}
.btn-manage:hover { background: #155a30; color: #fff; }
</style>

@endsection

@push('scripts')
<script>
// Auto-submit search with debounce
let searchTimeout;
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('inventory-filter-form').submit();
    }, 500);
});

// Auto-submit on filter change
document.getElementById('campus-filter').addEventListener('change', function() {
    document.getElementById('inventory-filter-form').submit();
});
</script>
@endpush