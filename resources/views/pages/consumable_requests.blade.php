@extends('layouts.app')
@section('title', 'Request History')
@section('page-title', 'Consumable Request History')

@section('content')

<a href="{{ route('consumables') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Consumables
</a>

<div style="display:flex; gap:10px; margin-bottom:1.25rem;">
    <a href="{{ route('consumable-requests') }}" class="tab-toggle-btn active"><i class="ti ti-history"></i> Request History</a>
    <a href="{{ route('consumables.reports') }}" class="tab-toggle-btn"><i class="ti ti-chart-line"></i> Consumption Reports</a>
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <div class="filter-pills">
            <a href="{{ route('consumable-requests', ['status'=>'all']) }}" class="filter-pill {{ $status === 'all' ? 'active' : '' }}">All</a>
            <a href="{{ route('consumable-requests', ['status'=>'pending']) }}" class="filter-pill {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('consumable-requests', ['status'=>'approved']) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}">Approved</a>
            <a href="{{ route('consumable-requests', ['status'=>'rejected']) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}">Rejected</a>
            <a href="{{ route('consumable-requests', ['status'=>'partial']) }}" class="filter-pill {{ $status === 'partial' ? 'active' : '' }}">Partial</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-clipboard-list"></i> Requests ({{ $requests->total() }})</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Recipient</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Signatories</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td style="font-size:12px; font-weight:600;">{{ $req->reference_no }}</td>
                    <td>
                        <div class="cell-primary">{{ $req->recipient_name }}</div>
                        <div class="cell-secondary">{{ $req->department }}</div>
                    </td>
                    <td style="font-size:12px;">{{ $req->request_date->format('M d, Y') }}</td>
                    <td><button class="chip-badge chip-type" style="cursor:pointer; border:none;" onclick="viewRequestDetails({{ $req->id }})">View ({{ $req->items->count() }})</button></td>
                    <td style="font-size:11.5px;">{{ $req->approved_by ?? '—' }}</td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="chip-badge chip-status-active">Approved</span>
                        @elseif($req->status === 'partial')
                            <span class="chip-badge chip-campus">Partial</span>
                        @else
                            <span class="chip-badge chip-status-inactive">Rejected</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" title="View" onclick="viewRequestDetails({{ $req->id }})"><i class="ti ti-eye"></i></button>
                            @if($req->status === 'pending' && in_array(auth()->user()->role, ['admin','superadmin']))
                            <button class="table-icon-btn" style="background:#fff8f0; color:#ef9f27;" title="Check/Review" onclick="openCheckModal({{ $req->id }})"><i class="ti ti-checkbox"></i></button>
                            @endif
                            <button class="table-icon-btn edit" title="Edit" onclick="openEditRequestModal({{ $req->id }})"><i class="ti ti-edit"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7"><div class="empty-state"><i class="ti ti-clipboard-off"></i><p>No requests found.</p></div></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} results</div>
        {{ $requests->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- VIEW DETAILS MODAL --}}
<div class="modal-overlay" id="view-request-modal">
    <div class="modal-box-lg" style="max-width:680px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-eye"></i> Request Details</div>
            <button class="modal-close" onclick="document.getElementById('view-request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <div id="view-request-content"></div>
    </div>
</div>

{{-- CHECK/REVIEW MODAL --}}
<div class="modal-overlay" id="check-modal">
    <div class="modal-box-lg" style="max-width:600px;">
        <div class="modal-header-row" style="background:#ef9f27; margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-checkbox"></i> Check Request Items</div>
            <button class="modal-close" onclick="document.getElementById('check-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="check-form">
            @csrf
            <div id="check-form-content"></div>
            <div style="background:#fff8f0; border:1px solid #ef9f27; border-radius:8px; padding:10px 14px; margin:1rem 0; font-size:12px; color:#7a5500;">
                <i class="ti ti-info-circle"></i> Approved items will be deducted from inventory. Rejected items require a reason.
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;"><i class="ti ti-device-floppy"></i> Submit Check</button>
        </form>
    </div>
</div>

{{-- EDIT REQUEST MODAL --}}
<div class="modal-overlay" id="edit-request-modal">
    <div class="modal-box-lg" style="max-width:600px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Edit Request Group</div>
            <button class="modal-close" onclick="document.getElementById('edit-request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="edit-request-form">
            @csrf @method('PUT')
            <div class="modal-grid">
                <div class="modal-form-group"><div class="modal-label">Recipient First Name *</div><input type="text" name="recipient_first_name" id="er-first" class="modal-input" required></div>
                <div class="modal-form-group"><div class="modal-label">Recipient Last Name *</div><input type="text" name="recipient_last_name" id="er-last" class="modal-input" required></div>
            </div>
            <div class="modal-form-group"><div class="modal-label">Office/Department *</div><input type="text" name="department" id="er-dept" class="modal-input" required></div>
            <div class="modal-grid">
                <div class="modal-form-group"><div class="modal-label">Approved By</div><input type="text" name="approved_by" id="er-approved" class="modal-input"></div>
                <div class="modal-form-group"><div class="modal-label">Supply Officer</div><input type="text" name="supply_officer" id="er-supply" class="modal-input"></div>
            </div>
            <button type="submit" class="modal-btn-primary"><i class="ti ti-device-floppy"></i> Update Request</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function viewRequestDetails(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    document.getElementById('view-request-content').innerHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-user"></i> Recipient Information</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Employee Name: </span><strong>${req.recipient_first_name} ${req.recipient_mi||''} ${req.recipient_last_name}</strong></div>
                <div class="detail-row"><span>Office/Department: </span><strong>${req.department}</strong></div>
                <div class="detail-row"><span>Campus: </span><strong>${req.campus?.name ?? '—'}</strong></div>
                <div class="detail-row"><span>Request Date: </span><strong>${new Date(req.request_date).toLocaleDateString()}</strong></div>
            </div>
        </div>
        <br>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-list"></i> Requested Items</div>
            <table class="data-table"><thead><tr><th>Item</th><th>Qty</th><th>Purpose</th><th>Status</th></tr></thead><tbody>
            ${req.items.map(i => `
                <tr>
                    <td>${i.consumable?.item_name ?? '—'}</td>
                    <td>${i.quantity} ${i.consumable?.unit ?? ''}</td>
                    <td>${i.purpose ?? '—'}</td>
                    <td><span class="chip-badge ${i.status==='approved'?'chip-status-active':(i.status==='rejected'?'chip-status-inactive':'')}" style="${i.status==='pending'?'background:#fff8f0;color:#ef9f27;':''}">${i.status}</span></td>
                </tr>
            `).join('')}
            </tbody></table>
        </div>
        <br>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Requested By: </span><strong>${req.requester?.name ?? '—'}</strong></div>
                <div class="detail-row"><span>Approved By: </span><strong>${req.approved_by ?? '—'}</strong></div>
                <div class="detail-row"><span>Supply Officer: </span><strong>${req.supply_officer ?? '—'}</strong></div>
            </div>
        </div>
    `;
    document.getElementById('view-request-modal').classList.add('open');
}

async function openCheckModal(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    document.getElementById('check-form').action = `/consumable-requests/${id}/review`;
    document.getElementById('check-form-content').innerHTML = `
        <div class="detail-row" style="margin-bottom:1rem;"><span>Recipient</span><strong>${req.recipient_first_name} ${req.recipient_last_name} — ${req.department}</strong></div>
        <table class="data-table"><thead><tr><th>Item</th><th>Qty</th><th>Decision</th><th>Rejection Reason</th></tr></thead><tbody>
        ${req.items.map((i, idx) => `
            <tr>
                <td>${i.consumable?.item_name ?? '—'}<div class="cell-secondary">Available: ${i.consumable?.current_stock ?? 0} ${i.consumable?.unit ?? ''}</div></td>
                <td>${i.quantity}</td>
                <td>
                    <input type="hidden" name="items[${idx}][id]" value="${i.id}">
                    <select name="items[${idx}][decision]" class="modal-input" style="padding:6px 10px; font-size:12px;">
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </td>
                <td><input type="text" name="items[${idx}][rejection_reason]" class="modal-input" style="padding:6px 10px; font-size:12px;" placeholder="If rejected..."></td>
            </tr>
        `).join('')}
        </tbody></table>
    `;
    document.getElementById('check-modal').classList.add('open');
}

async function openEditRequestModal(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    document.getElementById('er-first').value = req.recipient_first_name;
    document.getElementById('er-last').value = req.recipient_last_name;
    document.getElementById('er-dept').value = req.department;
    document.getElementById('er-approved').value = req.approved_by ?? '';
    document.getElementById('er-supply').value = req.supply_officer ?? '';
    document.getElementById('edit-request-form').action = `/consumable-requests/${id}`;
    document.getElementById('edit-request-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush