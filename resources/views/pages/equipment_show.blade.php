@extends('layouts.app')
@section('title', $name)
@section('page-title', 'Equipment Details')

@section('content')

<a href="{{ route('equipment') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to All Equipment
</a>

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-device-desktop"></i> {{ $name }}</div>
        <p class="hero-sub">{{ $item->description ?? 'No description provided.' }}</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Status</span>{{ ucfirst($item->status) }}</div>
            <div class="hero-chip"><span>Condition</span>{{ $item->condition_status }}</div>
            @if($item->is_condemned)
            <div class="hero-chip" style="background:rgba(226,75,74,0.3);"><span>Condemned</span>Yes</div>
            @endif
        </div>
    </div>
    <div class="hero-right" style="display:flex; gap:8px;">
        <a href="{{ route('equipment.edit', [$type, $item->id]) }}" class="btn-add"><i class="ti ti-edit"></i> Edit</a>
        <a href="{{ route('equipment.report', [$type, $item->id]) }}" class="btn-add"><i class="ti ti-file-text"></i> Report</a>
    </div>
</div>

<div class="two-col">

    {{-- Details Card --}}
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-info-circle"></i> Equipment Information</div></div>
        <div class="card-body">
            <div class="detail-grid">
                @if($type === 'computer')
                    <div class="detail-row"><span>Article</span><strong>{{ $item->article }}</strong></div>
                    <div class="detail-row"><span>Processor</span><strong>{{ $item->processor }}</strong></div>
                    <div class="detail-row"><span>RAM</span><strong>{{ $item->ram }}</strong></div>
                    <div class="detail-row"><span>Storage</span><strong>{{ $item->storage }}</strong></div>
                    <div class="detail-row"><span>Operating System</span><strong>{{ $item->operating_system ?? '—' }}</strong></div>
                    @if($item->article === 'Computer Package')
                        <div class="detail-row"><span>Serial (Monitor)</span><strong>{{ $item->serial_number_monitor ?? '—' }}</strong></div>
                        <div class="detail-row"><span>Serial (System Unit)</span><strong>{{ $item->serial_number_system ?? '—' }}</strong></div>
                    @else
                        <div class="detail-row"><span>Serial Number</span><strong>{{ $item->serial_number ?? '—' }}</strong></div>
                    @endif
                @else
                    <div class="detail-row"><span>Article</span><strong>{{ $item->article ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Brand</span><strong>{{ $item->brand ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Model</span><strong>{{ $item->model ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Serial Number</span><strong>{{ $item->serial_number ?? '—' }}</strong></div>
                @endif

                <div class="detail-row"><span>Unit</span><strong>{{ ucfirst($item->unit) }}</strong></div>
                <div class="detail-row"><span>Property No.</span><strong>{{ $item->property_no ?? '—' }}</strong></div>
                <div class="detail-row"><span>Cost</span><strong>₱{{ number_format($item->cost, 2) }}</strong></div>
                <div class="detail-row"><span>Purchase Date</span><strong>{{ $item->purchase_date?->format('M d, Y') ?? '—' }}</strong></div>

                @if($type === 'lab')
                <div class="detail-row"><span>Calibration Date</span><strong>{{ $item->calibration_date?->format('M d, Y') ?? '—' }}</strong></div>
                <div class="detail-row"><span>Next Calibration</span><strong>{{ $item->next_calibration_date?->format('M d, Y') ?? '—' }}</strong></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Location & Status Card --}}
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-map-pin"></i> Location &amp; Assignment</div></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-row"><span>Campus</span><strong>{{ $item->campus->name ?? '—' }}</strong></div>
                <div class="detail-row"><span>Location</span><strong>{{ $item->location->location_name ?? 'Unassigned / Storage' }}</strong></div>
                <div class="detail-row"><span>Status</span><strong>{{ ucfirst($item->status) }}</strong></div>
                <div class="detail-row"><span>Accountable Person</span><strong>{{ $item->remarks ?? '—' }}</strong></div>
                <div class="detail-row"><span>Date Added</span><strong>{{ $item->created_at->format('M d, Y h:i A') }}</strong></div>
                <div class="detail-row"><span>Last Updated</span><strong>{{ $item->updated_at->format('M d, Y h:i A') }}</strong></div>

                @if($item->is_condemned)
                <div class="detail-row"><span>Condemned Date</span><strong>{{ $item->condemned_date?->format('M d, Y') }}</strong></div>
                <div class="detail-row"><span>Condemned By</span><strong>{{ $item->condemnedByUser->name ?? '—' }}</strong></div>
                <div class="detail-row" style="grid-column:1/-1;"><span>Reason</span><strong>{{ $item->condemned_reason ?? '—' }}</strong></div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- Danger Zone --}}
<div class="card" style="border-color:#fdd; margin-top:1.25rem;">
    <div class="card-header"><div class="card-title" style="color:var(--red);"><i class="ti ti-alert-triangle"></i> Danger Zone</div></div>
    <div class="card-body" style="display:flex; gap:10px; flex-wrap:wrap;">
        @if(!$item->is_condemned)
        <button class="btn-table-action" style="background:#fff8f0; color:var(--orange);" onclick="openCondemnModal()">
            <i class="ti ti-alert-triangle"></i> Mark as Condemned
        </button>
        @endif
        <button class="btn-table-action" style="background:#fff5f5; color:var(--red);" onclick="openDeleteModal()">
            <i class="ti ti-trash"></i> Delete Permanently
        </button>
    </div>
</div>

{{-- CONDEMN MODAL --}}
<div class="modal-overlay" id="condemn-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-alert-triangle" style="color:var(--orange)"></i> Mark as Condemned</div>
            <button class="modal-close" onclick="closeCondemnModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">
            This will mark <strong>{{ $name }}</strong> as condemned. This action can be reviewed later but the item will no longer be available for assignment.
        </p>
        <form method="POST" action="{{ route('equipment.condemn', [$type, $item->id]) }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Reason <span style="text-transform:none; font-weight:400;">(optional but recommended)</span></div>
                <textarea name="condemned_reason" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="e.g., Unit no longer powers on, beyond repair..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--orange);">
                <i class="ti ti-alert-triangle"></i> Confirm Condemnation
            </button>
        </form>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal-overlay" id="delete-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-trash" style="color:var(--red)"></i> Delete Equipment</div>
            <button class="modal-close" onclick="closeDeleteModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            This action is <strong>permanent and cannot be undone</strong>. To confirm, type exactly:
        </p>
        <div style="background:#fff5f5; border:1.5px solid #e24b4a; border-radius:8px; padding:10px 14px; margin-bottom:1rem; font-size:13px; font-weight:600; color:#c0392b; text-align:center;">
            Delete {{ $name }}
        </div>
        <form method="POST" action="{{ route('equipment.destroy', [$type, $item->id]) }}">
            @csrf @method('DELETE')
            <div class="modal-form-group">
                <input type="text" name="confirmation_text" class="modal-input" placeholder="Type the confirmation text above" required autocomplete="off">
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--red);">
                <i class="ti ti-trash"></i> Permanently Delete
            </button>
        </form>
    </div>
</div>

<style>
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
@media(max-width:600px) { .detail-grid { grid-template-columns: 1fr; } }
.detail-row {
    display: flex; flex-direction: column; gap: 3px;
    padding-bottom: 0.6rem; border-bottom: 1px solid var(--border);
    font-size: 13px;
}
.detail-row span { font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
.detail-row strong { color: var(--text-primary); font-weight: 500; }
</style>

@endsection

@push('scripts')
<script>
function openCondemnModal()  { document.getElementById('condemn-modal').classList.add('open'); }
function closeCondemnModal() { document.getElementById('condemn-modal').classList.remove('open'); }
function openDeleteModal()   { document.getElementById('delete-modal').classList.add('open'); }
function closeDeleteModal()  { document.getElementById('delete-modal').classList.remove('open'); }

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush