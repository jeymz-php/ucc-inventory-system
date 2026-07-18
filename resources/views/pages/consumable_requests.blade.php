@extends('layouts.app')
@section('title', 'Request History')
@section('page-title', 'Consumable Request History')

@section('content')

<a href="{{ route('consumables') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Consumables
</a>

<div style="display:flex; gap:10px; margin-bottom:1.25rem; align-items:center; flex-wrap:wrap;">
    <a href="{{ route('consumable-requests') }}" class="tab-toggle-btn active"><i class="ti ti-history"></i> Request History</a>
    <a href="{{ route('consumables.reports') }}" class="tab-toggle-btn"><i class="ti ti-chart-line"></i> Consumption Reports</a>
    @if(in_array(auth()->user()->role, ['admin','superadmin']))
    <button type="button" class="tab-toggle-btn" style="margin-left:auto; border:1.5px dashed #7c3aed; color:#7c3aed; background:#f4f0ff;"
            onclick="openBlankReceiptModal()">
        <i class="ti ti-file-text"></i> Generate Blank Receipt
    </button>
    @endif
</div>

<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <div class="filter-pills">
            <a href="{{ route('consumable-requests', ['status'=>'all']) }}"     class="filter-pill {{ $status === 'all'     ? 'active' : '' }}">All</a>
            <a href="{{ route('consumable-requests', ['status'=>'pending']) }}"  class="filter-pill {{ $status === 'pending'  ? 'active' : '' }}">Pending</a>
            <a href="{{ route('consumable-requests', ['status'=>'approved']) }}" class="filter-pill {{ $status === 'approved' ? 'active' : '' }}">Approved</a>
            <a href="{{ route('consumable-requests', ['status'=>'rejected']) }}" class="filter-pill {{ $status === 'rejected' ? 'active' : '' }}">Rejected</a>
            <a href="{{ route('consumable-requests', ['status'=>'partial']) }}"  class="filter-pill {{ $status === 'partial'  ? 'active' : '' }}">Partial</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-clipboard-list"></i> Requests ({{ $requests->total() }})</div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Recipient</th>
                    <th>Source</th>
                    <th>Request Date</th>
                    <th>Items</th>
                    <th>Signatories</th>
                    <th>Status</th>
                    <th>Approved Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr id="req-row-{{ $req->id }}">
                    <td style="font-size:12px; font-weight:600;">{{ $req->reference_no }}</td>
                    <td>
                        <div class="cell-primary">{{ $req->recipient_name }}</div>
                        <div class="cell-secondary">{{ $req->department }}</div>
                    </td>
                    <td>
                        @if(($req->source ?? 'ims') === 'cs')
                            <span class="chip-badge" style="background:#eff6ff; color:#1a56db; gap:4px;">
                                <i class="ti ti-package" style="font-size:10px;"></i> CS
                            </span>
                        @else
                            <span class="chip-badge" style="background:#f0faf4; color:#1a6b3a; gap:4px;">
                                <i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS
                            </span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $req->request_date->format('M d, Y') }}</td>
                    <td>
                        <button class="chip-badge chip-type" style="cursor:pointer; border:none;"
                                onclick="viewRequestDetails({{ $req->id }})">
                            View ({{ $req->items->count() }})
                        </button>
                    </td>
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
                    {{-- Approved Date = reviewed_at --}}
                    <td style="font-size:11.5px; color:var(--text-muted); white-space:nowrap;">
                        @if($req->reviewed_at)
                            {{ $req->reviewed_at->format('M d, Y') }}<br>
                            <span style="font-size:10.5px;">{{ $req->reviewed_at->format('h:i A') }}</span>
                        @else
                            <span style="color:#ccc;">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" title="View Details"
                                    onclick="viewRequestDetails({{ $req->id }})">
                                <i class="ti ti-eye"></i>
                            </button>
                            @if($req->status === 'pending' && in_array(auth()->user()->role, ['admin','superadmin']))
                            <button class="table-icon-btn" style="background:#fff8f0; color:#ef9f27;"
                                    title="Check/Review" onclick="openCheckModal({{ $req->id }})">
                                <i class="ti ti-checkbox"></i>
                            </button>
                            @endif
                            @if(in_array($req->status, ['approved', 'partial']))
                            <a href="{{ route('consumable-requests.report', $req->id) }}" target="_blank"
                               class="table-icon-btn" style="background:#f4f0ff; color:#7c3aed;" title="Generate Report">
                                <i class="ti ti-file-text"></i>
                            </a>
                            @endif
                            @if(in_array(auth()->user()->role, ['admin','superadmin']))
                            <button class="table-icon-btn edit" title="Edit"
                                    onclick="openEditRequestModal({{ $req->id }})">
                                <i class="ti ti-edit"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ti ti-clipboard-off"></i>
                            <p>No requests found.</p>
                        </div>
                    </td>
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
    <div class="modal-box-lg" style="max-width:700px;">
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
            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;">
                <i class="ti ti-device-floppy"></i> Submit Check
            </button>
        </form>
    </div>
</div>

{{-- EDIT REQUEST MODAL --}}
<div class="modal-overlay" id="edit-request-modal">
    <div class="modal-box-lg" style="max-width:800px;">
        <div class="modal-header-row" style="background:#3b82f6; margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;">
                <i class="ti ti-edit"></i> Edit Request Group: <span id="er-ref-no"></span>
            </div>
            <button class="modal-close" style="background:rgba(255,255,255,0.15); color:#fff; border-color:transparent;"
                    onclick="document.getElementById('edit-request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="edit-request-form">
            @csrf @method('PUT')

            <div class="detail-section-title"><i class="ti ti-user"></i> Recipient Details</div>
            <div class="modal-grid" style="margin-bottom:1rem;">
                <div class="modal-form-group" style="margin:0;">
                    <div class="modal-label">Recipient Name *</div>
                    <input type="text" id="er-name-display" class="modal-input" disabled style="background:#fafafa;">
                </div>
                <div class="modal-form-group" style="margin:0;">
                    <div class="modal-label">Office/Department *</div>
                    <input type="text" name="department" id="er-dept" class="modal-input" required>
                </div>
            </div>
            <input type="hidden" name="recipient_first_name" id="er-first">
            <input type="hidden" name="recipient_last_name"  id="er-last">
            <input type="hidden" name="recipient_mi"         id="er-mi">

            <div class="detail-section-title" style="margin-top:1.25rem;">
                <i class="ti ti-list"></i> Requested Items
            </div>

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
            <style>
            .select2-container .select2-selection--single { height:38px; border:1.5px solid #e0e0e0; border-radius:8px; }
            .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px; font-size:12px; padding-left:10px; }
            .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
            .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background:var(--green-dark); }
            .select2-dropdown { font-size:12px; border:1.5px solid #e0e0e0; border-radius:8px; }

            /* Remove native spinner arrows on the Qty input so the number itself
               gets the full column width instead of being squeezed by them */
            #er-items-body input[type="number"]::-webkit-outer-spin-button,
            #er-items-body input[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            #er-items-body input[type="number"] {
                -moz-appearance: textfield;
                text-align: center;
            }
            </style>

            <div style="overflow-x:auto; margin-bottom:0.5rem;">
                <table class="data-table" style="min-width:780px; table-layout:fixed;">
                    <thead>
                        <tr>
                            <th style="width:28%;">Item</th>
                            <th style="width:90px;">Qty</th>
                            <th style="width:20%;">Purpose</th>
                            <th style="width:150px;">Release Date</th>
                            <th style="width:130px;">Status</th>
                            <th style="width:44px;"></th>
                        </tr>
                    </thead>
                    <tbody id="er-items-body"></tbody>
                </table>
            </div>

            <button type="button" onclick="erAddNewRow()"
                    style="display:flex; align-items:center; gap:6px; width:100%;
                           padding:8px 14px; border-radius:8px; margin-bottom:1rem;
                           border:1.5px dashed #3b82f6; background:#eff6ff;
                           color:#3b82f6; font-size:13px; font-weight:600;
                           cursor:pointer; font-family:inherit; justify-content:center;">
                <i class="ti ti-plus"></i> Add Another Item
            </button>

            <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
            <div class="modal-grid" style="grid-template-columns:1fr 1fr 1fr; margin-top:0.5rem;">
                <div class="modal-form-group" style="margin:0;">
                    <div class="modal-label">Approved By</div>
                    <input type="text" name="approved_by" id="er-approved" class="modal-input">
                </div>
                <div class="modal-form-group" style="margin:0;">
                    <div class="modal-label">Supply Officer</div>
                    <input type="text" name="supply_officer" id="er-supply" class="modal-input">
                </div>
                <div class="modal-form-group" style="margin:0;">
                    <div class="modal-label">Group Status</div>
                    <select name="status" id="er-status" class="modal-input">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:1.5rem;">
                <button type="button" class="btn-back-link" style="flex:1;"
                        onclick="document.getElementById('edit-request-modal').classList.remove('open');">
                    Cancel
                </button>
                <button type="submit" class="modal-btn-primary" style="flex:1; margin:0; background:#3b82f6;">
                    <i class="ti ti-device-floppy"></i> Update Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- BLANK RECEIPT MODAL --}}
@if(in_array(auth()->user()->role, ['admin','superadmin']))
<div class="modal-overlay" id="blank-receipt-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row" style="background:#7c3aed; margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-file-text"></i> Generate Blank Receipt</div>
            <button class="modal-close" style="background:rgba(255,255,255,0.15); color:#fff; border-color:transparent;"
                    onclick="document.getElementById('blank-receipt-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>

        <div class="modal-form-group" style="margin:0;">
            <div class="modal-label">Number of item rows</div>
            <div class="filter-pills" id="blank-rows-pills" style="margin-top:6px;">
                <button type="button" class="filter-pill blank-rows-pill" data-value="5">5</button>
                <button type="button" class="filter-pill blank-rows-pill active" data-value="10">10</button>
                <button type="button" class="filter-pill blank-rows-pill" data-value="15">15</button>
                <button type="button" class="filter-pill blank-rows-pill" data-value="20">20</button>
                <button type="button" class="filter-pill blank-rows-pill" data-value="custom">Custom</button>
            </div>
            <input type="number" id="blank-rows-custom" class="modal-input" placeholder="Enter number of rows (1–100)"
                   style="display:none; margin-top:10px;" min="1" max="100">
        </div>

        <button type="button" class="modal-btn-primary" style="margin-top:1.25rem; background:#7c3aed;" onclick="generateBlankReceipt()">
            <i class="ti ti-file-text"></i> Generate Blank Receipt
        </button>
    </div>
</div>
@endif

<style>
@keyframes highlightFlash {
    0%   { background: #fff3cd; }
    50%  { background: #fff3cd; }
    100% { background: transparent; }
}
.row-highlight-flash td { animation: highlightFlash 3.5s ease-out; }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ── VIEW DETAILS ──
async function viewRequestDetails(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    const sourceBadge = (req.source === 'cs')
        ? '<span class="chip-badge" style="background:#eff6ff;color:#1a56db;"><i class="ti ti-package" style="font-size:10px;"></i> CS</span>'
        : '<span class="chip-badge" style="background:#f0faf4;color:#1a6b3a;"><i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS</span>';

    const mi           = req.recipient_mi ? req.recipient_mi.trim() : '';
    const fullName     = [req.recipient_first_name, mi, req.recipient_last_name].filter(Boolean).join(' ');
    const approvedDate = req.reviewed_at
        ? new Date(req.reviewed_at).toLocaleString('en-PH', {year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'})
        : '—';

    document.getElementById('view-request-content').innerHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-user"></i> Recipient Information</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Employee Name</span><strong>${fullName}</strong></div>
                <div class="detail-row"><span>Office/Department</span><strong>${req.department}</strong></div>
                <div class="detail-row"><span>Campus</span><strong>${req.campus?.name ?? '—'}</strong></div>
                <div class="detail-row"><span>Request Date</span><strong>${new Date(req.request_date).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</strong></div>
                <div class="detail-row"><span>Source</span><strong>${sourceBadge}</strong></div>
                <div class="detail-row"><span>Requested By</span><strong>${req.requester?.name ?? '—'}</strong></div>
            </div>
        </div>
        <br>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-circle-check"></i> Status & Dates</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Status</span><strong>
                    <span class="chip-badge ${req.status==='approved'?'chip-status-active':(req.status==='rejected'?'chip-status-inactive':'')}"
                          style="${req.status==='pending'?'background:#fff8f0;color:#ef9f27;':req.status==='partial'?'background:#eff6ff;color:#2563eb;':''}">${req.status}</span>
                </strong></div>
                <div class="detail-row"><span>Approved Date</span><strong>${approvedDate}</strong></div>
                <div class="detail-row"><span>Approved By</span><strong>${req.approved_by ?? '—'}</strong></div>
                <div class="detail-row"><span>Supply Officer</span><strong>${req.supply_officer ?? '—'}</strong></div>
            </div>
        </div>
        <br>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-list"></i> Requested Items</div>
            <div style="overflow-x:auto;">
                <table class="data-table">
                    <thead><tr><th>Item</th><th>Qty</th><th>Purpose</th><th>Release Date</th><th>Status</th></tr></thead>
                    <tbody>
                    ${req.items.map(i => `
                        <tr>
                            <td>${i.consumable?.item_name ?? '—'}</td>
                            <td>${i.quantity} ${i.consumable?.unit ?? ''}</td>
                            <td style="font-size:12px;">${i.purpose ?? '—'}</td>
                            <td style="font-size:12px;">${i.release_date ? new Date(i.release_date.substring(0,10)+'T00:00:00').toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'}) : '—'}</td>
                            <td><span class="chip-badge ${i.status==='approved'?'chip-status-active':(i.status==='rejected'?'chip-status-inactive':'')}"
                                      style="${i.status==='pending'?'background:#fff8f0;color:#ef9f27;':''}">${i.status}</span></td>
                        </tr>
                    `).join('')}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    document.getElementById('view-request-modal').classList.add('open');
}

// ── CHECK MODAL ──
async function openCheckModal(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    const mi       = req.recipient_mi ? req.recipient_mi.trim() : '';
    const fullName = [req.recipient_first_name, mi, req.recipient_last_name].filter(Boolean).join(' ');

    document.getElementById('check-form').action = `/consumable-requests/${id}/review`;
    document.getElementById('check-form-content').innerHTML = `
        <div style="margin-bottom:1rem; padding:10px 14px; background:#fafafa; border-radius:8px;">
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:3px;">Recipient</div>
            <div style="font-size:13px; font-weight:600;">${fullName}</div>
            <div style="font-size:12px; color:var(--text-muted);">${req.department}</div>
        </div>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead><tr><th>Item</th><th>Qty</th><th>Decision</th><th>Release Date / Reason</th></tr></thead>
                <tbody>
                ${req.items.map((i, idx) => `
                    <tr>
                        <td>
                            ${i.consumable?.item_name ?? '—'}
                            <div class="cell-secondary">Available: ${i.consumable?.current_stock ?? 0} ${i.consumable?.unit ?? ''}</div>
                        </td>
                        <td>${i.quantity}</td>
                        <td>
                            <input type="hidden" name="items[${idx}][id]" value="${i.id}">
                            <select name="items[${idx}][decision]" class="modal-input" style="padding:6px 10px; font-size:12px;"
                                    onchange="toggleCheckRowFields(this, ${idx})">
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </td>
                        <td>
                            <div id="check-release-wrap-${idx}">
                                <input type="date" name="items[${idx}][release_date]"
                                       class="modal-input" style="padding:6px 10px; font-size:12px;">
                            </div>
                            <div id="check-reason-wrap-${idx}" style="display:none;">
                                <input type="text" name="items[${idx}][rejection_reason]"
                                       class="modal-input" style="padding:6px 10px; font-size:12px;"
                                       placeholder="Reason for rejection...">
                            </div>
                        </td>
                    </tr>
                `).join('')}
                </tbody>
            </table>
        </div>
    `;
    document.getElementById('check-modal').classList.add('open');
}

function toggleCheckRowFields(selectEl, idx) {
    const isApproved   = selectEl.value === 'approved';
    const releaseWrap  = document.getElementById(`check-release-wrap-${idx}`);
    const reasonWrap   = document.getElementById(`check-reason-wrap-${idx}`);
    if (releaseWrap) releaseWrap.style.display = isApproved ? 'block' : 'none';
    if (reasonWrap)  reasonWrap.style.display  = isApproved ? 'none'  : 'block';
}

// ── EDIT REQUEST MODAL ──
let availableConsumablesCache = null;
let erItemIdx = 0;
let erAllItems = [];

async function getAvailableConsumables() {
    if (availableConsumablesCache) return availableConsumablesCache;
    const res = await fetch('{{ route("consumable-requests.available-items") }}');
    availableConsumablesCache = await res.json();
    return availableConsumablesCache;
}

function buildErRow(idx, item, allItems) {
    const options = allItems.map(it =>
        `<option value="${it.id}" ${item && it.id == item.consumable_id ? 'selected' : ''}>
            ${it.item_name} (Stock: ${it.current_stock} ${it.unit})
         </option>`
    ).join('');

    const releaseVal = item?.release_date ? item.release_date.substring(0, 10) : '';

    return `<tr id="er-row-${idx}">
        <td style="overflow:visible;">
            <input type="hidden" name="items[${idx}][id]" value="${item ? item.id : ''}">
            <select name="items[${idx}][consumable_id]" class="modal-input er-select"
                    id="er-select-${idx}" style="width:100%; box-sizing:border-box;">
                <option value="">-- Search item --</option>
                ${options}
            </select>
        </td>
        <td>
            <input type="number" min="1" name="items[${idx}][quantity]"
                   value="${item ? item.quantity : 1}"
                   class="modal-input" style="padding:6px 8px; font-size:12px; width:100%; box-sizing:border-box;">
        </td>
        <td>
            <input type="text" name="items[${idx}][purpose]"
                   value="${item ? (item.purpose ?? '') : ''}"
                   class="modal-input" style="padding:6px 8px; font-size:12px; width:100%; box-sizing:border-box;"
                   placeholder="Office use">
        </td>
        <td>
            <input type="date" name="items[${idx}][release_date]"
                   value="${releaseVal}"
                   class="modal-input" style="padding:6px 8px; font-size:12px; width:100%; box-sizing:border-box;">
        </td>
        <td>
            <select name="items[${idx}][status]" class="modal-input" style="padding:6px 8px; font-size:12px; width:100%; box-sizing:border-box;">
                <option value="pending"  ${item && item.status === 'pending'  ? 'selected' : ''}>Pending</option>
                <option value="approved" ${item && item.status === 'approved' ? 'selected' : ''}>Approved</option>
                <option value="rejected" ${item && item.status === 'rejected' ? 'selected' : ''}>Rejected</option>
            </select>
        </td>
        <td>
            <button type="button" class="table-icon-btn delete"
                    onclick="erRemoveRow(${idx})"
                    title="Remove row">
                <i class="ti ti-trash"></i>
            </button>
        </td>
    </tr>`;
}

function erRemoveRow(idx) {
    if (window.jQuery) {
        const el = jQuery(`#er-select-${idx}`);
        if (el.length && el.data('select2')) el.select2('destroy');
    }
    const row = document.getElementById(`er-row-${idx}`);
    if (row) row.remove();
}

// Init Select2 only on a specific select element (avoids re-init conflicts)
function initSelect2OnRow(idx) {
    if (!window.jQuery) return;
    const el = jQuery(`#er-select-${idx}`);
    if (el.length && !el.data('select2')) {
        el.select2({
            dropdownParent: jQuery('#edit-request-modal'),
            placeholder: 'Search item...',
            allowClear: true,
            width: '100%',
        });
    }
}

// Init Select2 on ALL rows (used when opening the modal fresh)
function initAllSelect2() {
    if (!window.jQuery) return;
    setTimeout(() => {
        jQuery('.er-select').each(function() {
            if (!jQuery(this).data('select2')) {
                jQuery(this).select2({
                    dropdownParent: jQuery('#edit-request-modal'),
                    placeholder: 'Search item...',
                    allowClear: true,
                    width: '100%',
                });
            }
        });
    }, 80);
}

async function erAddNewRow() {
    // Make sure the item catalog is loaded before adding a row (avoids a silent no-op
    // if this is clicked before openEditRequestModal's fetch has resolved)
    if (!erAllItems.length) {
        erAllItems = await getAvailableConsumables();
    }
    const tbody = document.getElementById('er-items-body');
    const idx   = erItemIdx++;
    tbody.insertAdjacentHTML('beforeend', buildErRow(idx, null, erAllItems));
    // Only init the new row's select — don't touch existing rows
    setTimeout(() => initSelect2OnRow(idx), 80);
}

async function openEditRequestModal(id) {
    const [req, items] = await Promise.all([
        fetch(`/consumable-requests/${id}`).then(r => r.json()),
        getAvailableConsumables()
    ]);

    erAllItems = items;
    erItemIdx  = req.items.length;

    const mi       = req.recipient_mi ? req.recipient_mi.trim() : '';
    const fullName = [req.recipient_first_name, mi, req.recipient_last_name].filter(Boolean).join(' ');

    document.getElementById('er-ref-no').textContent        = req.reference_no;
    document.getElementById('er-name-display').value        = fullName;
    document.getElementById('er-first').value               = req.recipient_first_name;
    document.getElementById('er-last').value                = req.recipient_last_name;
    document.getElementById('er-mi').value                  = req.recipient_mi ?? '';
    document.getElementById('er-dept').value                = req.department;
    document.getElementById('er-approved').value            = req.approved_by ?? '';
    document.getElementById('er-supply').value              = req.supply_officer ?? '';
    document.getElementById('er-status').value              = req.status;
    document.getElementById('edit-request-form').action     = `/consumable-requests/${id}`;

    // Destroy all existing Select2 instances cleanly before rebuilding rows
    if (window.jQuery) {
        jQuery('.er-select').each(function() {
            if (jQuery(this).data('select2')) jQuery(this).select2('destroy');
        });
    }

    document.getElementById('er-items-body').innerHTML = req.items
        .map((item, idx) => buildErRow(idx, item, items))
        .join('');

    document.getElementById('edit-request-modal').classList.add('open');
    initAllSelect2();
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ── HIGHLIGHT ROW ──
document.addEventListener('DOMContentLoaded', function() {
    const params      = new URLSearchParams(window.location.search);
    const highlightId = params.get('highlight');
    if (highlightId) {
        const row = document.getElementById('req-row-' + highlightId);
        if (row) {
            row.classList.add('row-highlight-flash');
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => row.classList.remove('row-highlight-flash'), 3500);
        }
    }
});

// ── BLANK RECEIPT ──
function openBlankReceiptModal() {
    document.getElementById('blank-receipt-modal').classList.add('open');
}

document.querySelectorAll('.blank-rows-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        document.querySelectorAll('.blank-rows-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        const customInput = document.getElementById('blank-rows-custom');
        if (this.dataset.value === 'custom') {
            customInput.style.display = 'block';
            customInput.focus();
        } else {
            customInput.style.display = 'none';
        }
    });
});

function generateBlankReceipt() {
    const active = document.querySelector('.blank-rows-pill.active');
    if (!active) return;

    let rows = active.dataset.value;
    if (rows === 'custom') {
        rows = document.getElementById('blank-rows-custom').value;
        if (!rows || rows < 1) {
            alert('Please enter a valid number of rows.');
            return;
        }
    }

    window.open(`{{ route('consumable-requests.blank-report') }}?rows=${rows}`, '_blank');
    document.getElementById('blank-receipt-modal').classList.remove('open');
}
</script>
@endpush