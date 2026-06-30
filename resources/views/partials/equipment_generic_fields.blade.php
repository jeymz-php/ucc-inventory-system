@php $isLab = $type === 'Lab'; $propRequired = $showPropertyRequired ?? false; @endphp

<div class="modal-form-group">
    <div class="modal-label-row">
        <div class="modal-label">{{ $isLab ? 'Article *' : 'Equipment Name *' }}</div>
        <button type="button" class="manage-articles-link" onclick="openArticleManager('{{ $type }}')">Manage</button>
    </div>
    <select name="article" class="modal-input article-select-{{ $type }}" required>
        <option value="">-- Select Article --</option>
    </select>
</div>

<div class="modal-form-group">
    <div class="modal-label">Description</div>
    <textarea name="description" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="Enter Description"></textarea>
</div>

<div class="modal-grid">
    <div class="modal-form-group">
        <div class="modal-label">Brand</div>
        <input type="text" name="brand" class="modal-input" placeholder="Enter Brand">
    </div>
    <div class="modal-form-group">
        <div class="modal-label">Model</div>
        <input type="text" name="model" class="modal-input" placeholder="Enter Model">
    </div>
</div>

<div class="modal-grid">
    <div class="modal-form-group">
        <div class="modal-label">Unit *</div>
        <select name="unit" class="modal-input" required>
            <option value="">-- Select Unit --</option>
            <option value="unit">Unit</option>
            <option value="box">Box</option>
            <option value="pcs">Pcs</option>
            <option value="lot">Lot</option>
        </select>
    </div>
    <div class="modal-form-group">
        <div class="modal-label">Serial Number</div>
        <input type="text" name="serial_number" class="modal-input" placeholder="Enter Serial Number">
    </div>
</div>

<div class="modal-grid">
    <div class="modal-form-group">
        <div class="modal-label">Property No. {{ $propRequired ? '*' : '' }}</div>
        <input type="text" name="property_no" class="modal-input" placeholder="Enter Property No." {{ $propRequired ? 'required' : '' }}>
    </div>
    <div class="modal-form-group">
        <div class="modal-label">Cost (₱)</div>
        <input type="number" step="0.01" name="cost" class="modal-input" placeholder="Enter Cost (₱)">
    </div>
</div>

@include('partials.equipment_date_purchase')

<div class="modal-grid">
    <div class="modal-form-group">
        <div class="modal-label">Campus *</div>
        <select name="campus_id" class="modal-input campus-select" required>
            <option value="">-- Select Campus --</option>
            @foreach($campuses as $campus)
            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="modal-form-group">
        <div class="modal-label">Location</div>
        <select name="location_id" class="modal-input location-select">
            <option value="">-- Unassigned / Storage --</option>
        </select>
    </div>
</div>

@if($isLab)
<div class="modal-form-group">
    <div class="modal-label">Calibration Date</div>
    <input type="date" name="calibration_date" class="modal-input">
</div>
@endif

@include('partials.equipment_condition_accountable', ['type' => $type])