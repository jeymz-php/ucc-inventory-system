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
    <div class="modal-form-group">
        <div class="modal-label">Accountable Person</div>
        <div style="display:flex; gap:6px;">
            <input type="text" name="acc_last" class="modal-input" placeholder="Last Name">
            <input type="text" name="acc_first" class="modal-input" placeholder="First Name">
            <input type="text" name="acc_mi" class="modal-input" placeholder="M.I." style="max-width:60px;">
        </div>
        <div class="modal-hint">Will be saved as "Last Name, First Name M.I."</div>
    </div>
</div>