@extends('layouts.app')
@section('title', 'Condemned Equipment')
@section('page-title', 'Condemned Equipment')

@section('content')

<div class="hero-banner condemned-hero">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-alert-triangle"></i> Condemned Equipment</div>
        <p class="hero-sub">Equipment items marked as condemned. Repairable items can be restored to active inventory; items beyond recovery can be permanently transferred to waste.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total Condemned</span>{{ $totalCondemned }}</div>
        </div>
    </div>
    <div class="hero-right">
        <a href="#" class="btn-add" onclick="event.preventDefault(); document.getElementById('add-condemned-modal').classList.add('open');">
            <i class="ti ti-plus"></i> Add Condemned Item
        </a>
    </div>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <form method="GET" action="{{ route('condemned') }}" id="condemned-filter-form">

            <div style="margin-bottom:0.9rem;">
                <div class="filter-label">Equipment Type</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'all' ? 'active' : '' }}" data-name="type" data-value="all">All</button>
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'Computer' ? 'active' : '' }}" data-name="type" data-value="Computer">Computer</button>
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'Kitchen' ? 'active' : '' }}" data-name="type" data-value="Kitchen">Kitchen</button>
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'Office' ? 'active' : '' }}" data-name="type" data-value="Office">Office</button>
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'Lab' ? 'active' : '' }}" data-name="type" data-value="Lab">Lab</button>
                    <button type="button" class="filter-pill condemned-pill {{ $type === 'General' ? 'active' : '' }}" data-name="type" data-value="General">General</button>
                </div>
            </div>

            <div>
                <div class="filter-label">Campus</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill condemned-pill {{ !$campusId ? 'active' : '' }}" data-name="campus_id" data-value="">All Campuses</button>
                    @foreach($campuses as $campus)
                    <button type="button" class="filter-pill condemned-pill {{ $campusId == $campus->id ? 'active' : '' }}" data-name="campus_id" data-value="{{ $campus->id }}">{{ $campus->name }}</button>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="type" id="hidden-condemned-type" value="{{ $type }}">
            <input type="hidden" name="campus_id" id="hidden-condemned-campus" value="{{ $campusId }}">
        </form>
        <div style="position:relative; margin-top:0.9rem; max-width:320px;">
            <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:14px;"></i>
            <input type="text" id="condemned-search" value="{{ $search }}" placeholder="Search name, serial, reason..."
                   style="width:100%; padding:9px 12px 9px 34px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
        </div>
    </div>
</div>

<div class="card condemned-card">
    <div class="card-header">
        <div class="card-title" style="color:var(--red);"><i class="ti ti-alert-triangle"></i> Condemned Items ({{ $paginator->total() }})</div>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>Condemned Date</th>
                    <th>Condemned By</th>
                    <th>Reason</th>
                    <th>Location</th>
                    <th>Disposal Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $item)
                <tr class="condemned-row">
                    <td>
                        <div class="cell-primary">{{ $item->display_name ?? '—' }}</div>
                        @if($item->brand)
                        <div class="cell-secondary">{{ $item->brand }} {{ $item->model ?? '' }}</div>
                        @endif
                    </td>
                    <td><span class="chip-badge chip-type">{{ $item->equipment_type }}</span></td>
                    <td style="font-size:12px;">{{ $item->condemned_date?->format('M d, Y') ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $item->condemnedByUser->name ?? '—' }}</td>
                    <td style="font-size:12px; max-width:200px;">{{ Str::limit($item->condemned_reason, 50) ?: '—' }}</td>
                    <td style="font-size:12px;">{{ $item->location->location_name ?? 'Unassigned' }}</td>
                    <td>
                        @if($item->is_wasted)
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-trash-x" style="font-size:10px"></i> Wasted</span>
                        @else
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('equipment.show', [strtolower($item->equipment_type), $item->id]) }}" class="table-icon-btn view" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('equipment.report', [strtolower($item->equipment_type), $item->id]) }}" target="_blank" class="table-icon-btn" style="background:#f4f0ff; color:#7c3aed;" title="Generate Report">
                                <i class="ti ti-file-text"></i>
                            </a>
                            @if(!$item->is_wasted)
                            <button type="button" class="table-icon-btn" style="background:#f0faf4; color:var(--green-dark);" title="Restore to Active"
                                    onclick="openRestoreModal('{{ strtolower($item->equipment_type) }}', {{ $item->id }}, '{{ addslashes($item->display_name) }}')">
                                <i class="ti ti-rotate"></i>
                            </button>
                            <button type="button" class="table-icon-btn delete" title="Transfer to Waste (Permanent)"
                                    onclick="openWasteModal('{{ strtolower($item->equipment_type) }}', {{ $item->id }}, '{{ addslashes($item->display_name) }}')">
                                <i class="ti ti-trash-x"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-circle-check" style="color:var(--green-dark)"></i>
                            <p>No condemned equipment. Everything is in good standing!</p>
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

{{-- RESTORE MODAL --}}
<div class="modal-overlay" id="restore-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-rotate" style="color:var(--green-dark)"></i> Restore Equipment</div>
            <button class="modal-close" onclick="document.getElementById('restore-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">
            This will restore <strong id="restore-name"></strong> back to active inventory at its original location. Use this if the item has been repaired or fixed.
        </p>
        <form method="POST" id="restore-form">
            @csrf
            <button type="submit" class="modal-btn-primary"><i class="ti ti-rotate"></i> Confirm Restore</button>
        </form>
    </div>
</div>

{{-- WASTE MODAL --}}
<div class="modal-overlay" id="waste-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-trash-x" style="color:var(--red)"></i> Transfer to Waste</div>
            <button class="modal-close" onclick="document.getElementById('waste-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            This will permanently transfer <strong id="waste-name"></strong> to waste. <strong style="color:var(--red);">This action cannot be undone</strong> — the item will no longer be recoverable or restorable to active inventory.
        </p>
        <form method="POST" id="waste-form">
            @csrf
            <button type="submit" class="modal-btn-primary" style="background:var(--red);"><i class="ti ti-trash-x"></i> Confirm Transfer to Waste</button>
        </form>
    </div>
</div>

{{-- ADD CONDEMNED ITEM MODAL --}}
<div class="modal-overlay" id="add-condemned-modal">
    <div class="modal-box-lg" style="max-width:560px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-alert-triangle" style="color:var(--red)"></i> Add Condemned Equipment</div>
            <button class="modal-close" onclick="document.getElementById('add-condemned-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">
            Use this for equipment that is already damaged/condemned and you simply need to record it in the system — no need to fill in full specs.
        </p>
        <form method="POST" action="{{ route('condemned.store') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Equipment Type *</div>
                    <select name="equipment_type" class="modal-input" required>
                        <option value="">-- Select Type --</option>
                        <option value="Computer">Computer</option>
                        <option value="Kitchen">Kitchen</option>
                        <option value="Office">Office</option>
                        <option value="Lab">Lab</option>
                        <option value="General">General</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Equipment Name *</div>
                    <input type="text" name="name" class="modal-input" placeholder="e.g., Old Projector" required>
                </div>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Serial / Property No.</div>
                    <input type="text" name="serial_number" class="modal-input" placeholder="Optional">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Cost (₱)</div>
                    <input type="number" step="0.01" name="cost" class="modal-input" placeholder="Optional">
                </div>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Campus *</div>
                    <select name="campus_id" class="modal-input add-condemned-campus" required>
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Last Known Location</div>
                    <select name="location_id" class="modal-input add-condemned-location">
                        <option value="">-- Unassigned / Storage --</option>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Reason for Condemnation <span style="text-transform:none; font-weight:400;">(optional but recommended)</span></div>
                <textarea name="condemned_reason" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="e.g., Beyond repair, motherboard failure..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--red);"><i class="ti ti-alert-triangle"></i> Add as Condemned</button>
        </form>
    </div>
</div>

<style>
.condemned-hero {
    background: linear-gradient(135deg, #c0392b 0%, #e24b4a 100%) !important;
}
.condemned-card {
    border-color: #fdd !important;
}
.condemned-row td {
    background: #fffafa;
}
.condemned-row:hover td {
    background: #fff0f0;
}
.condemned-pill.active {
    background: var(--red) !important;
    border-color: var(--red) !important;
    color: #fff !important;
}
</style>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.condemned-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const name  = this.dataset.name;
        const value = this.dataset.value;
        document.getElementById('hidden-condemned-' + (name === 'campus_id' ? 'campus' : 'type')).value = value;
        document.getElementById('condemned-filter-form').submit();
    });
});

let condemnedSearchTimeout;
document.getElementById('condemned-search').addEventListener('input', function() {
    clearTimeout(condemnedSearchTimeout);
    const val = this.value;
    condemnedSearchTimeout = setTimeout(() => {
        const url = new URL(window.location);
        url.searchParams.set('search', val);
        window.location.href = url.toString();
    }, 500);
});

function openRestoreModal(type, id, name) {
    document.getElementById('restore-name').textContent = name;
    document.getElementById('restore-form').action = `/equipment/${type}/${id}/restore`;
    document.getElementById('restore-modal').classList.add('open');
}

function openWasteModal(type, id, name) {
    document.getElementById('waste-name').textContent = name;
    document.getElementById('waste-form').action = `/equipment/${type}/${id}/waste`;
    document.getElementById('waste-modal').classList.add('open');
}

document.querySelector('.add-condemned-campus').addEventListener('change', async function() {
    const campusId = this.value;
    const locSelect = document.querySelector('.add-condemned-location');
    locSelect.innerHTML = '<option value="">Loading...</option>';
    if (!campusId) { locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>'; return; }

    const res  = await fetch(`{{ route('equipment.locations-by-campus') }}?campus_id=${campusId}`);
    const data = await res.json();

    locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>';
    data.forEach(loc => {
        locSelect.innerHTML += `<option value="${loc.id}">${loc.location_name}</option>`;
    });
});

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush