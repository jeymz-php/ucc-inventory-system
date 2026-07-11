@extends('layouts.app')
@section('title', 'My Requests')
@section('page-title', 'My Consumable Requests')

@section('content')

<a href="{{ route('consumables') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Consumables
</a>

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-clipboard-list"></i> My Requests</div>
        <p class="hero-sub">Track the status of all your submitted consumable requests.</p>
    </div>
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
    <div class="card-header"><div class="card-title"><i class="ti ti-clipboard-list"></i> My Requests ({{ $requests->total() }})</div></div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Reviewed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                <tr>
                    <td style="font-size:12px; font-weight:600;">{{ $req->reference_no }}</td>
                    <td style="font-size:12px;">{{ $req->request_date->format('M d, Y') }}</td>
                    <td><button class="chip-badge chip-type" style="cursor:pointer; border:none;" onclick="viewMyRequestDetails({{ $req->id }})">View ({{ $req->items->count() }})</button></td>
                    <td>
                        @if($req->status === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Approved</span>
                        @elseif($req->status === 'partial')
                            <span class="chip-badge chip-campus"><i class="ti ti-circle-half-2" style="font-size:10px"></i> Partial</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-circle-x" style="font-size:10px"></i> Rejected</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $req->reviewer->name ?? '—' }}</td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" title="View Details" onclick="viewMyRequestDetails({{ $req->id }})"><i class="ti ti-eye"></i></button>
                            @if(in_array($req->status, ['approved', 'partial']))
                            <a href="{{ route('consumable-requests.report', $req->id) }}" target="_blank" class="table-icon-btn" style="background:#f4f0ff; color:#7c3aed;" title="Generate Report">
                                <i class="ti ti-file-text"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ti ti-clipboard-off"></i>
                            <p>You haven't submitted any requests yet.</p>
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
<div class="modal-overlay" id="my-view-request-modal">
    <div class="modal-box-lg" style="max-width:600px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-eye"></i> Request Details</div>
            <button class="modal-close" onclick="document.getElementById('my-view-request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <div id="my-view-request-content"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function viewMyRequestDetails(id) {
    const res = await fetch(`/consumable-requests/${id}`);
    const req = await res.json();

    const statusColor = (s) => s === 'approved' ? 'chip-status-active' : (s === 'rejected' ? 'chip-status-inactive' : '');
    const statusStyle = (s) => s === 'pending' ? 'background:#fff8f0;color:#ef9f27;' : '';

    document.getElementById('my-view-request-content').innerHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-info-circle"></i> Request Information</div>
            <br>
            <div class="detail-grid">
                <div class="detail-row"><span>Reference No.: </span><strong>${req.reference_no}</strong></div>
                <div class="detail-row"><span>Request Date: </span><strong>${new Date(req.request_date).toLocaleDateString()}</strong></div>
                <div class="detail-row"><span>Campus: </span><strong>${req.campus?.name ?? '—'}</strong></div>
                <div class="detail-row"><span>Department: </span><strong>${req.department}</strong></div>
            </div>
        </div>
        <div class="detail-section">
        <br>
            <div class="detail-section-title"><i class="ti ti-list"></i> Requested Items</div>
            <table class="data-table"><thead><tr><th>Item</th><th>Qty</th><th>Purpose</th><th>Release Date</th><th>Status</th><th>Reason (if rejected)</th></tr></thead><tbody>
            ${req.items.map(i => `
                <tr>
                    <td>${i.consumable?.item_name ?? '—'}</td>
                    <td>${i.quantity} ${i.consumable?.unit ?? ''}</td>
                    <td>${i.purpose ?? '—'}</td>
                    <td style="font-size:11.5px;">${i.release_date ? new Date(i.release_date.substring(0,10)+'T00:00:00').toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'}) : '—'}</td>
                    <td><span class="chip-badge ${statusColor(i.status)}" style="${statusStyle(i.status)}">${i.status}</span></td>
                    <td style="font-size:11.5px; color:#888;">${i.rejection_reason ?? '—'}</td>
                </tr>
            `).join('')}
            </tbody></table>
        </div>
        <div class="detail-section">
        <br>
            <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Approved By: </span><strong>${req.approved_by ?? '—'}</strong></div>
                <div class="detail-row"><span>Supply Officer: </span><strong>${req.supply_officer ?? '—'}</strong></div>
                <div class="detail-row"><span>Reviewed By: </span><strong>${req.reviewer?.name ?? '—'}</strong></div>
            </div>
        </div>
        ${(req.status === 'approved' || req.status === 'partial') ? `
        <div style="text-align:center; margin-top:1rem;">
            <a href="/consumable-requests/${req.id}/report" target="_blank" class="modal-btn-primary" style="display:inline-flex; width:auto; padding:10px 24px; text-decoration:none;">
                <i class="ti ti-file-text"></i> Generate Report
            </a>
        </div>` : ''}
    `;
    document.getElementById('my-view-request-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush