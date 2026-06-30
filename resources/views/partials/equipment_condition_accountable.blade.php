<div class="modal-grid">
    <div class="modal-form-group">
        <div class="modal-label">Condition *</div>
        <select name="condition_status" class="modal-input" required>
            <option value="Excellent">Excellent</option>
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
            <option value="Poor">Poor</option>
            <option value="Damaged">Damaged</option>
        </select>
    </div>
</div>

{{-- fallback to a default type name string if none is provided --}}
@php $formType = $type ?? 'default'; @endphp

<div class="modal-form-group" style="grid-column: span 2; border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 0.5rem;">
    <div class="modal-label">Accountable Person Type *</div>
    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 0.75rem;">
        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
            <input type="radio" name="accountable_type_{{ $formType }}" value="existing" checked onchange="toggleAccountableType(this)" style="accent-color: var(--green-dark);">
            Existed UCC - IMS User
        </label>
        <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
            <input type="radio" name="accountable_type_{{ $formType }}" value="manual" onchange="toggleAccountableType(this)" style="accent-color: var(--green-dark);">
            Non-existing UCC - IMS User
        </label>
    </div>

    <!-- Dropdown Selection for Existing Database Users -->
    <div class="existing-user-group">
        <div class="modal-label">Select UCC - IMS User *</div>
        <select name="user_id" class="modal-input accountable-select-field" required>
            <option value="">-- Select Registered User --</option>
            @foreach($imsUsers as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Manual Raw Entry Fields -->
    <div class="manual-user-group" style="display: none;">
        <div class="modal-label">Accountable Person Name *</div>
        <div style="display:flex; gap:6px;">
            <input type="text" name="acc_last" class="modal-input accountable-manual-input" placeholder="Last Name">
            <input type="text" name="acc_first" class="modal-input accountable-manual-input" placeholder="First Name">
            <input type="text" name="acc_mi" class="modal-input accountable-manual-input" placeholder="M.I." style="max-width:60px;">
        </div>
        <div class="modal-hint">Will be saved as "Last Name, First Name M.I."</div>
    </div>
</div>