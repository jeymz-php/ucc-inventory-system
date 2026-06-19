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
        <button class="btn-table-action green" onclick="document.getElementById('add-location-modal').classList.add('open');"><i class="ti ti-plus"></i> Add Location</button>
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
                            <button class="table-icon-btn edit" title="Edit"
                                    onclick="openEditLocationModal({{ $loc->id }}, '{{ addslashes($loc->location_name) }}', {{ $loc->location_type_id ?? 'null' }}, {{ $loc->campus_id }}, {{ $loc->capacity }}, '{{ addslashes($loc->description) }}')">
                                <i class="ti ti-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('locations.archive', $loc) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="table-icon-btn {{ $loc->is_active ? 'archive' : 'view' }}" title="{{ $loc->is_active ? 'Archive' : 'Restore' }}"
                                        onclick="return confirm('{{ $loc->is_active ? 'Archive' : 'Restore' }} this location?')">
                                    <i class="ti ti-{{ $loc->is_active ? 'archive' : 'archive-off' }}"></i>
                                </button>
                            </form>
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
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $locations->firstItem() }} to {{ $locations->lastItem() }} of {{ $locations->total() }} results
        </div>
        {{ $locations->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- ADD LOCATION MODAL --}}
<div class="modal-overlay" id="add-location-modal">
    <div class="modal-box-lg" style="max-width:560px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-plus"></i> Add New Location</div>
            <button class="modal-close" onclick="document.getElementById('add-location-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('locations.store') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Room Name *</div>
                <input type="text" name="location_name" class="modal-input" placeholder="e.g., Computer Laboratory 4" required>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Campus *</div>
                    <select name="campus_id" class="modal-input add-campus-select" required>
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Location Type *</div>
                    <select name="location_type_id" class="modal-input add-type-select" required>
                        <option value="">-- Select Campus First --</option>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Capacity</div>
                <input type="number" name="capacity" class="modal-input" placeholder="0" min="0">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Description</div>
                <textarea name="description" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="Brief description of this room"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary"><i class="ti ti-plus"></i> Add Location</button>
        </form>
    </div>
</div>

{{-- EDIT LOCATION MODAL --}}
<div class="modal-overlay" id="edit-location-modal">
    <div class="modal-box-lg" style="max-width:560px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Edit Location</div>
            <button class="modal-close" onclick="document.getElementById('edit-location-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="edit-location-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Room Name *</div>
                <input type="text" name="location_name" id="el-name" class="modal-input" required>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Campus *</div>
                    <select name="campus_id" id="el-campus" class="modal-input edit-campus-select" required>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Location Type *</div>
                    <select name="location_type_id" id="el-type" class="modal-input edit-type-select" required>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Capacity</div>
                <input type="number" name="capacity" id="el-capacity" class="modal-input" min="0">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Description</div>
                <textarea name="description" id="el-description" class="modal-input" rows="3" style="padding-top:10px; resize:none;"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary"><i class="ti ti-device-floppy"></i> Save Changes</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function loadTypesByCampus(campusId, selectEl, selectedId = null) {
    selectEl.innerHTML = '<option value="">Loading...</option>';
    if (!campusId) {
        selectEl.innerHTML = '<option value="">-- Select Campus First --</option>';
        return;
    }
    const res  = await fetch(`/inventory/location-type/by-campus?campus_id=${campusId}`);
    const data = await res.json();

    selectEl.innerHTML = '<option value="">-- Select Location Type --</option>';
    data.forEach(t => {
        const selected = String(t.id) === String(selectedId) ? 'selected' : '';
        selectEl.innerHTML += `<option value="${t.id}" ${selected}>${t.type_name}</option>`;
    });
}

document.querySelector('.add-campus-select').addEventListener('change', function() {
    loadTypesByCampus(this.value, document.querySelector('.add-type-select'));
});

document.querySelector('.edit-campus-select').addEventListener('change', function() {
    loadTypesByCampus(this.value, document.querySelector('.edit-type-select'));
});

function openEditLocationModal(id, name, typeId, campusId, capacity, description) {
    document.getElementById('el-name').value = name;
    document.getElementById('el-campus').value = campusId;
    document.getElementById('el-capacity').value = capacity;
    document.getElementById('el-description').value = description;
    document.getElementById('edit-location-form').action = `/locations/${id}`;
    loadTypesByCampus(campusId, document.querySelector('.edit-type-select'), typeId);
    document.getElementById('edit-location-modal').classList.add('open');
}
</script>

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