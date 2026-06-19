<div class="modal-form-group">
    <div class="modal-label">Date of Purchase</div>
    <div class="date-toggle-row">
        <label class="date-toggle-option">
            <input type="radio" name="has_purchase_date" value="1" checked onchange="toggleDateInput(this)">
            <i class="ti ti-circle-check" style="color:var(--green-dark)"></i> Have Date of Purchase
        </label>
        <label class="date-toggle-option">
            <input type="radio" name="has_purchase_date" value="0" onchange="toggleDateInput(this)">
            <i class="ti ti-circle-x" style="color:var(--red)"></i> No Date
        </label>
    </div>
    <input type="date" name="purchase_date" class="modal-input purchase-date-input">
</div>