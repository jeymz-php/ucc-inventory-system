@extends('layouts.app')
@section('title', 'Locations')
@section('page-title', 'Locations')

@section('content')

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('locations') }}" id="loc-filter-form"
              style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">

            <div style="flex:1; min-width:220px; position:relative;">
                <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" id="loc-search" value="{{ $search }}"
                       placeholder="Search room name or description..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border);
                              border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>

            <select name="campus_id" id="loc-campus-filter"
                    style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px;
                           font-size:13px; font-family:inherit; outline:none; min-width:170px;">
                <option value="">All Campuses</option>
                @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ $campusId == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
                @endforeach
            </select>

            <select name="location_type_id" id="loc-type-filter"
                    style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px;
                           font-size:13px; font-family:inherit; outline:none; min-width:170px;">
                <option value="">All Location Types</option>
                @foreach($locationTypes as $type)
                <option value="{{ $type->id }}" {{ $typeId == $type->id ? 'selected' : '' }}>
                    {{ $type->type_name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="btn-table-action green">
                <i class="ti ti-filter"></i> Filter
            </button>

            @if($search || $campusId || $typeId)
            <a href="{{ route('locations') }}" class="btn-table-action" style="background:#f5f5f5; color:#666;">
                <i class="ti ti-x"></i> Clear
            </a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-map-pin"></i> All Locations ({{ $locations->total() }})</div>
        <button class="btn-table-action green"><i class="ti ti-plus"></i> Add Location</button>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Campus</th>
                    <th>Location Type</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Facilitator</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $loc)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $loc->location_name }}</div>
                        @if($loc->description)
                        <div class="cell-secondary">{{ Str::limit($loc->description, 50) }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="chip-badge chip-campus"><i class="ti ti-map-pin" style="font-size:10px"></i> {{ $loc->campus->name ?? '—' }}</span>
                    </td>
                    <td>
                        @if($loc->locationType)
                            <span class="chip-badge chip-type"><i class="ti ti-building" style="font-size:10px"></i> {{ $loc->locationType->type_name }}</span>
                        @else
                            <span class="chip-dash">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="capacity-pill"><i class="ti ti-users"></i> {{ $loc->capacity }}</span>
                    </td>
                    <td>
                        @if($loc->equipment_count > 0)
                            <span class="chip-badge chip-equipment-has"><i class="ti ti-device-desktop" style="font-size:11px"></i> {{ $loc->equipment_count }} items</span>
                        @else
                            <span class="chip-badge chip-equipment-zero"><i class="ti ti-device-desktop" style="font-size:11px"></i> 0 items</span>
                        @endif
                    </td>
                    <td>
                        @if($loc->facilitator)
                            {{ $loc->facilitator->name }}
                        @else
                            <span class="chip-dash">—</span>
                        @endif
                    </td>
                    <td>
                        @if($loc->is_active)
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:11px"></i> Active</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:11px"></i> Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn edit" title="Edit"><i class="ti ti-edit"></i></button>
                            <button class="table-icon-btn archive" title="Archive"><i class="ti ti-archive"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-map-pin"></i>
                            <p>No locations found. Try adjusting your filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($locations->hasPages())
    <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
        {{ $locations->links() }}
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
let locSearchTimeout;
document.getElementById('loc-search').addEventListener('input', function() {
    clearTimeout(locSearchTimeout);
    locSearchTimeout = setTimeout(() => {
        document.getElementById('loc-filter-form').submit();
    }, 500);
});

document.getElementById('loc-campus-filter').addEventListener('change', function() {
    document.getElementById('loc-filter-form').submit();
});
document.getElementById('loc-type-filter').addEventListener('change', function() {
    document.getElementById('loc-filter-form').submit();
});
</script>
@endpush