@extends('layouts.app')
@section('title', 'Edit Equipment')
@section('page-title', 'Edit Equipment')

@section('content')

<a href="{{ route('equipment.show', [$type, $item->id]) }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Details
</a>

{{-- Hero Banner --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-edit"></i> Edit Equipment</div>
        <p class="hero-sub">Update the details for <strong>{{ $name }}</strong>. The Article/Equipment Type is locked and cannot be changed once added to inventory.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Category</span>{{ ucfirst($type) }}</div>
            <div class="hero-chip"><span>Status</span>{{ ucfirst($item->status) }}</div>
            @if($item->is_condemned)
            <div class="hero-chip" style="background:rgba(226,75,74,0.3);"><span>Condemned</span>Yes</div>
            @endif
        </div>
    </div>
</div>

<form method="POST" action="{{ route('equipment.update', [$type, $item->id]) }}">
    @csrf @method('PUT')

    {{-- Locked Article Section --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-lock"></i> Equipment Identity</div>
            <span class="chip-badge" style="background:#f5f5f5; color:#999;"><i class="ti ti-lock" style="font-size:10px"></i> Locked</span>
        </div>
        <div class="card-body">
            <div class="locked-field-grid">
                <div class="locked-field">
                    <div class="locked-label">{{ $type === 'lab' ? 'Article' : ($type === 'computer' ? 'Article (Device Type)' : 'Equipment Name') }}</div>
                    <div class="locked-value"><i class="ti ti-tag" style="font-size:13px; color:var(--text-muted);"></i> {{ $item->article ?? $name }}</div>
                </div>
                @if($type === 'computer')
                <div class="locked-field">
                    <div class="locked-label">Device Type</div>
                    <div class="locked-value"><i class="ti ti-device-desktop" style="font-size:13px; color:var(--text-muted);"></i> {{ $item->device_type }}</div>
                </div>
                @endif
            </div>
            <p class="locked-note"><i class="ti ti-info-circle"></i> To change the equipment type, delete this item and create a new entry under the correct category.</p>
        </div>
    </div>

    {{-- General Information --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header"><div class="card-title"><i class="ti ti-file-description"></i> General Information</div></div>
        <div class="card-body">

            <div class="modal-form-group">
                <div class="modal-label">Description</div>
                <textarea name="description" class="modal-input" rows="3" style="padding-top:10px; resize:none;">{{ $item->description }}</textarea>
            </div>

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Brand</div>
                    <input type="text" name="brand" class="modal-input" value="{{ $item->brand }}" placeholder="Enter Brand">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Model</div>
                    <input type="text" name="model" class="modal-input" value="{{ $item->model }}" placeholder="Enter Model">
                </div>
            </div>

            @if($type === 'computer')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Processor</div>
                    <input type="text" name="processor" class="modal-input" value="{{ $item->processor }}" placeholder="Enter Processor">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">RAM</div>
                    <input type="text" name="ram" class="modal-input" value="{{ $item->ram }}" placeholder="Enter RAM">
                </div>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Storage</div>
                    <input type="text" name="storage" class="modal-input" value="{{ $item->storage }}" placeholder="Enter Storage">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Operating System</div>
                    <input type="text" name="operating_system" class="modal-input" value="{{ $item->operating_system }}" placeholder="Enter Operating System">
                </div>
            </div>

            @if($item->article === 'Computer Package')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Serial Number (Monitor)</div>
                    <input type="text" name="serial_number_monitor" class="modal-input" value="{{ $item->serial_number_monitor }}" placeholder="Enter Monitor Serial Number">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Serial Number (System Unit)</div>
                    <input type="text" name="serial_number_system" class="modal-input" value="{{ $item->serial_number_system }}" placeholder="Enter System Unit Serial Number">
                </div>
            </div>
            @else
            <div class="modal-form-group">
                <div class="modal-label">Serial Number</div>
                <input type="text" name="serial_number" class="modal-input" value="{{ $item->serial_number }}" placeholder="Enter Serial Number">
            </div>
            @endif

            @else
            <div class="modal-form-group">
                <div class="modal-label">Serial Number</div>
                <input type="text" name="serial_number" class="modal-input" value="{{ $item->serial_number }}" placeholder="Enter Serial Number">
            </div>
            @endif

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Unit</div>
                    <select name="unit" class="modal-input">
                        @foreach(['unit','box','pcs','lot'] as $u)
                        <option value="{{ $u }}" {{ $item->unit === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Property No.</div>
                    <input type="text" name="property_no" class="modal-input" value="{{ $item->property_no }}" placeholder="Enter Property No.">
                </div>
            </div>

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Cost (₱)</div>
                    <input type="number" step="0.01" name="cost" class="modal-input" value="{{ $item->cost }}" placeholder="Enter Cost (₱)">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Purchase Date</div>
                    <input type="date" name="purchase_date" class="modal-input" value="{{ $item->purchase_date?->format('Y-m-d') }}">
                </div>
            </div>

            @if($type === 'lab')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Calibration Date</div>
                    <input type="date" name="calibration_date" class="modal-input" value="{{ $item->calibration_date?->format('Y-m-d') }}">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Next Calibration Date</div>
                    <input type="date" name="next_calibration_date" class="modal-input" value="{{ $item->next_calibration_date?->format('Y-m-d') }}">
                </div>
            </div>
            @endif

            @if(in_array($type, ['general','kitchen','office','lab']))
            <div class="modal-form-group">
                <div class="modal-label">Warranty Expiry</div>
                <input type="date" name="warranty_expiry" class="modal-input" value="{{ $item->warranty_expiry?->format('Y-m-d') }}">
            </div>
            @endif

        </div>
    </div>

    {{-- Condition (still editable), Campus/Location/Accountable (locked) --}}
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header"><div class="card-title"><i class="ti ti-shield-check"></i> Condition</div></div>
        <div class="card-body">
            <div class="modal-form-group" style="max-width:300px;">
                <div class="modal-label">Condition *</div>
                <select name="condition_status" class="modal-input" required>
                    @foreach(['Excellent','Good','Fair','Poor','Damaged'] as $c)
                    <option value="{{ $c }}" {{ $item->condition_status === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-lock"></i> Location &amp; Assignment</div>
            <span class="chip-badge" style="background:#f5f5f5; color:#999;"><i class="ti ti-lock" style="font-size:10px"></i> Locked</span>
        </div>
        <div class="card-body">
            <div class="locked-field-grid">
                <div class="locked-field">
                    <div class="locked-label">Campus</div>
                    <div class="locked-value"><i class="ti ti-map-pin" style="font-size:13px; color:var(--text-muted);"></i> {{ $item->campus->name ?? '—' }}</div>
                </div>
                <div class="locked-field">
                    <div class="locked-label">Location</div>
                    <div class="locked-value"><i class="ti ti-door" style="font-size:13px; color:var(--text-muted);"></i> {{ $item->location->location_name ?? 'Unassigned / Storage' }}</div>
                </div>
                <div class="locked-field" style="grid-column:1/-1;">
                    <div class="locked-label">Accountable Person</div>
                    <div class="locked-value"><i class="ti ti-user" style="font-size:13px; color:var(--text-muted);"></i> {{ $item->remarks ?? '—' }}</div>
                </div>
            </div>
            <p class="locked-note"><i class="ti ti-info-circle"></i> Campus, Location, and Accountable Person can only be changed using the <strong>Transfer</strong> feature on the All Equipment page.</p>

            {{-- Keep hidden fields so the form still submits valid data --}}
            <input type="hidden" name="campus_id" value="{{ $item->campus_id }}">
            <input type="hidden" name="location_id" value="{{ $item->location_id }}">
            <input type="hidden" name="acc_last" value="{{ $accLast ?? '' }}">
            <input type="hidden" name="acc_first" value="{{ $accFirst ?? '' }}">
            <input type="hidden" name="acc_mi" value="{{ $accMi ?? '' }}">
        </div>
    </div>

    <div style="display:flex; gap:10px; justify-content:flex-end;">
        <a href="{{ route('equipment.show', [$type, $item->id]) }}" class="btn-back-link">
            <i class="ti ti-x"></i> Cancel
        </a>
        <button type="submit" class="btn-save-changes">
            <i class="ti ti-device-floppy"></i> Save Changes
        </button>
    </div>

</form>

<style>
.locked-field-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;
    margin-bottom: 0.85rem;
}
@media(max-width:600px) { .locked-field-grid { grid-template-columns: 1fr; } }

.locked-field {
    background: #fafafa; border: 1.5px solid var(--border);
    border-radius: 10px; padding: 0.85rem 1rem;
}
.locked-label {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 6px;
}
.locked-value {
    font-size: 14px; font-weight: 600; color: var(--text-primary);
    display: flex; align-items: center; gap: 6px;
}

.locked-note {
    font-size: 11.5px; color: var(--text-muted);
    display: flex; align-items: center; gap: 6px;
    margin-top: 0.5rem;
}
.locked-note i { color: #ef9f27; }

.btn-back-link {
    display: flex; align-items: center; gap: 6px;
    padding: 11px 22px; border-radius: 8px;
    border: 1.5px solid var(--border); background: #fff;
    color: var(--text-secondary); font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.18s;
}
.btn-back-link:hover { border-color: var(--red); color: var(--red); }

.btn-save-changes {
    display: flex; align-items: center; gap: 8px;
    padding: 11px 24px; border-radius: 8px;
    border: none; background: var(--green-dark); color: #fff;
    font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: 'Inter', sans-serif; transition: background 0.18s;
}
.btn-save-changes:hover { background: #155a30; }
</style>

@endsection

@push('scripts')
<script>
document.querySelector('.campus-select').addEventListener('change', async function() {
    const campusId = this.value;
    const locSelect = document.querySelector('.location-select');
    const currentLocId = "{{ $item->location_id }}";
    locSelect.innerHTML = '<option value="">Loading...</option>';

    const res  = await fetch(`{{ route('equipment.locations-by-campus') }}?campus_id=${campusId}`);
    const data = await res.json();

    locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>';
    data.forEach(loc => {
        const selected = String(loc.id) === currentLocId ? 'selected' : '';
        locSelect.innerHTML += `<option value="${loc.id}" ${selected}>${loc.location_name}</option>`;
    });
});
</script>
@endpush