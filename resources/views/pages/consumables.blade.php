@extends('layouts.app')
@section('title', 'Consumables')
@section('page-title', 'Consumables Management')

@section('content')

@php $role = auth()->user()->role; @endphp

{{-- Hero Banner --}}
<div class="hero-banner" style="background: linear-gradient(135deg, #2563eb, #3b82f6);">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-package"></i> Consumables Management</div>
        <p class="hero-sub">Track and manage office supplies, laboratory consumables, and inventory levels. Monitor stock status, process requests, and generate reports.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total Items</span>{{ $stats['total'] }}</div>
            <div class="hero-chip"><span>Categories</span>{{ $stats['categories'] }}</div>
            <div class="hero-chip"><span>Pending Requests</span>{{ $stats['pending_requests'] }}</div>
        </div>
    </div>
    <div class="hero-right" style="display:flex; gap:8px;">
        <a href="#" class="btn-add" onclick="event.preventDefault(); openRequestModal();"><i class="ti ti-shopping-cart"></i> Request</a>
        @if(in_array($role, ['admin','superadmin']))
        <a href="#" class="btn-add" onclick="event.preventDefault(); openAddItemModal();"><i class="ti ti-plus"></i> Add Item</a>
        @endif
        <a href="{{ route('consumables.reports') }}" class="btn-add"><i class="ti ti-file-text"></i> Report</a>
    </div>
</div>

{{-- Tab Toggle --}}
<div style="display:flex; gap:10px; margin-bottom:1.25rem;">
    <a href="{{ route('consumable-requests') }}" class="tab-toggle-btn"><i class="ti ti-history"></i> View Request History</a>
    <a href="{{ route('consumables.reports') }}" class="tab-toggle-btn active"><i class="ti ti-chart-line"></i> View Consumption Reports</a>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-package"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Items</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['available'] }}</div><div class="stat-label">Available Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $stats['low'] }}</div><div class="stat-label">Low Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div><div class="stat-value">{{ $stats['critical'] }}</div><div class="stat-label">Critical Stock</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-box"></i> Current Inventory <span class="chip-badge chip-type">{{ $items->count() }} items</span></div>
    </div>
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('consumables') }}" id="consumables-filter-form" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <div style="flex:1; min-width:200px; position:relative;">
                <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" id="cons-search" value="{{ $search }}" placeholder="Search items..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>
            <select name="category_id" id="cons-category" style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:160px;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stock_status" id="cons-status" style="padding:9px 14px; border:1.5px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:140px;">
                <option value="">All Status</option>
                <option value="available" {{ $stockStatus === 'available' ? 'selected' : '' }}>Available</option>
                <option value="low" {{ $stockStatus === 'low' ? 'selected' : '' }}>Low</option>
                <option value="critical" {{ $stockStatus === 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            @if($search || $categoryId || $stockStatus)
            <a href="{{ route('consumables') }}" class="btn-table-action" style="background:#f5f5f5; color:#666;"><i class="ti ti-x"></i> Clear</a>
            @endif
        </form>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Category</th>
                    <th>ID Code</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $item->item_name }}</div>
                        @if($item->brand)<div class="cell-secondary">{{ $item->brand }}</div>@endif
                    </td>
                    <td><span class="chip-badge chip-type">{{ $item->category->name ?? 'Uncategorized' }}</span></td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $item->id_code }}</td>
                    <td>
                        <button class="stock-pill stock-{{ $item->status }}" onclick="viewItemDetails({{ $item->id }})">
                            {{ $item->current_stock }} {{ $item->unit }}
                        </button>
                    </td>
                    <td>
                        @if($item->status === 'critical')
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-alert-circle" style="font-size:10px"></i> Critical</span>
                        @elseif($item->status === 'low')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-alert-triangle" style="font-size:10px"></i> Low</span>
                        @else
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Available</span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $item->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="table-actions">
                            @if(in_array($role, ['admin','superadmin']))
                            <button class="table-icon-btn" style="background:#f0faf4; color:var(--green-dark);" title="Refill" onclick="openRefillModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ $item->unit }}')">
                                <i class="ti ti-plus"></i>
                            </button>
                            @endif
                            <button class="table-icon-btn view" title="View" onclick="viewItemDetails({{ $item->id }})">
                                <i class="ti ti-eye"></i>
                            </button>
                            @if(in_array($role, ['admin','superadmin']))
                            <button class="table-icon-btn edit" title="Edit" onclick="openEditItemModal({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ addslashes($item->category->name ?? '') }}', '{{ $item->max_stock }}', '{{ addslashes($item->brand) }}', '{{ $item->unit }}', '{{ $item->id_code }}')">
                                <i class="ti ti-edit"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-package-off"></i>
                            <p>No consumable items found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ITEM DETAILS MODAL --}}
<div class="modal-overlay" id="item-details-modal">
    <div class="modal-box-lg" style="max-width:600px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-eye"></i> Consumable Item Details</div>
            <button class="modal-close" onclick="document.getElementById('item-details-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <div id="item-details-content"></div>
    </div>
</div>

{{-- EDIT ITEM MODAL --}}
@if(in_array($role, ['admin','superadmin']))
<div class="modal-overlay" id="edit-item-modal">
    <div class="modal-box-lg" style="max-width:500px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Edit Consumable Item</div>
            <button class="modal-close" onclick="document.getElementById('edit-item-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="edit-item-form">
            @csrf @method('PUT')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Item Name *</div>
                    <input type="text" name="item_name" id="ei-name" class="modal-input" required>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Category</div>
                    <input type="text" name="category" id="ei-category" class="modal-input" placeholder="e.g., Office Supplies">
                </div>
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Max Stock</div>
                    <input type="number" name="max_stock" id="ei-maxstock" class="modal-input">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Unit</div>
                    <input type="text" id="ei-unit" class="modal-input" disabled style="background:#fafafa;">
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Brand</div>
                <input type="text" name="brand" id="ei-brand" class="modal-input">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">ID Code</div>
                <input type="text" id="ei-idcode" class="modal-input" disabled style="background:#fafafa;">
                <div class="modal-hint">Unique identifier (read-only)</div>
            </div>
            <div style="display:flex; gap:10px; margin-top:1rem;">
                <button type="button" class="btn-back-link" style="flex:1;" onclick="confirmDeleteItem()"><i class="ti ti-trash"></i> Delete Item</button>
                <button type="submit" class="modal-btn-primary" style="flex:1; margin:0;"><i class="ti ti-device-floppy"></i> Update Item</button>
            </div>
        </form>
    </div>
</div>

{{-- REFILL MODAL --}}
<div class="modal-overlay" id="refill-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-plus" style="color:var(--green-dark)"></i> Refill Item</div>
            <button class="modal-close" onclick="document.getElementById('refill-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">Refilling <strong id="refill-item-name"></strong>.</p>
        <form method="POST" id="refill-form">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Amount to Add (<span id="refill-unit"></span>) *</div>
                <input type="number" name="amount" class="modal-input" min="1" required>
            </div>
            <button type="submit" class="modal-btn-primary"><i class="ti ti-plus"></i> Confirm Refill</button>
        </form>
    </div>
</div>

{{-- ADD ITEM MODAL --}}
<div class="modal-overlay" id="add-item-modal">
    <div class="modal-box-lg" style="max-width:640px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-plus"></i> Add New Consumable Items</div>
            <button class="modal-close" onclick="document.getElementById('add-item-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('consumables.store') }}">
            @csrf
            <div id="add-item-rows"></div>
            <button type="button" class="btn-back-link" style="margin-bottom:1rem;" onclick="addItemRow()"><i class="ti ti-plus"></i> Add Another Item</button>
            <button type="submit" class="modal-btn-primary"><i class="ti ti-device-floppy"></i> Save All Items</button>
        </form>
    </div>
</div>
@endif

{{-- REQUEST MODAL --}}
<div class="modal-overlay" id="request-modal">
    <div class="modal-box-lg" style="max-width:680px;">
        <div class="modal-header-row" style="background:#ef9f27; margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-shopping-cart"></i> Request Multiple Items</div>
            <button class="modal-close" onclick="document.getElementById('request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('consumable-requests.store') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Last Name *</div>
                    <input type="text" name="recipient_last_name" class="modal-input" placeholder="e.g. Dela Cruz" required value="{{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] ?? '' }}">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">First Name *</div>
                    <input type="text" name="recipient_first_name" class="modal-input" placeholder="e.g. Juan" required value="{{ explode(' ', auth()->user()->name)[0] ?? '' }}">
                </div>
            </div>
            <div class="modal-form-group" style="max-width:140px;">
                <div class="modal-label">M.I.</div>
                <input type="text" name="recipient_mi" class="modal-input" placeholder="e.g. M">
            </div>
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Campus *</div>
                    <select name="campus_id" class="modal-input" required>
                        <option value="">-- Select Campus --</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ auth()->user()->campus_id == $campus->id ? 'selected' : '' }}>{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Office/Department *</div>
                    <input type="text" name="department" class="modal-input" placeholder="e.g. LabTech" required value="{{ auth()->user()->department->department_name ?? '' }}">
                </div>
            </div>

            <div class="modal-label" style="margin-top:0.5rem;">Items Requested</div>
            <div id="request-item-rows"></div>
            <button type="button" class="btn-back-link" style="margin:0.5rem 0 1rem;" onclick="addRequestItemRow()"><i class="ti ti-plus"></i> Add Another Item</button>

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Approved By</div>
                    <input type="text" name="approved_by" class="modal-input" placeholder="Name of approver">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Supply Officer</div>
                    <input type="text" name="supply_officer" class="modal-input" placeholder="Name of supply officer">
                </div>
            </div>

            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;"><i class="ti ti-send"></i> Submit Request</button>
        </form>
    </div>
</div>

{{-- DELETE CONFIRM --}}
<form method="POST" id="delete-item-form" style="display:none;">
    @csrf @method('DELETE')
</form>

<style>
.tab-toggle-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 11px 20px; border-radius: 10px;
    border: 1.5px solid var(--border); background: #fff;
    color: var(--text-secondary); font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.18s;
}
.tab-toggle-btn:hover { border-color: var(--green-dark); color: var(--green-dark); }
.tab-toggle-btn.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }

.stock-pill {
    border: none; cursor: pointer;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12.5px; font-weight: 700;
    font-family: 'Inter', sans-serif;
}
.stock-critical { background: #fff5f5; color: var(--red); }
.stock-low      { background: #fff8f0; color: #ef9f27; }
.stock-available{ background: #f0faf4; color: var(--green-dark); }

.detail-section { margin-bottom: 1.25rem; }
.detail-section-title { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.6rem; display:flex; align-items:center; gap:6px; }
.threshold-bar { display:flex; height:8px; border-radius:4px; overflow:hidden; margin-top:8px; }
.threshold-seg-critical { background:#e24b4a; }
.threshold-seg-low { background:#ef9f27; }
.threshold-seg-available { background:#1a6b3a; }

.item-row {
    display: grid; grid-template-columns: 2fr 1.2fr 1fr 1fr 1fr auto;
    gap: 8px; margin-bottom: 0.6rem; align-items: end;
}
@media(max-width:700px) { .item-row { grid-template-columns: 1fr 1fr; } }
.item-row-remove {
    width: 32px; height: 38px; border-radius: 8px; border: none;
    background: #fff5f5; color: var(--red); cursor: pointer; flex-shrink: 0;
}

.req-item-row {
    display: grid; grid-template-columns: 2fr 1fr 1.5fr auto;
    gap: 8px; margin-bottom: 0.6rem; align-items: end;
    padding: 0.75rem; background: #fafafa; border-radius: 8px;
}
@media(max-width:700px) { .req-item-row { grid-template-columns: 1fr; } }
</style>

@endsection

@push('scripts')
<script>
const consumablesData = {!! $items->map(fn($i) => [
    'id' => $i->id, 'name' => $i->item_name, 'unit' => $i->unit, 'stock' => $i->current_stock
])->values() !!};

// ── FILTERS ──
let consSearchTimeout;
document.getElementById('cons-search').addEventListener('input', function() {
    clearTimeout(consSearchTimeout);
    consSearchTimeout = setTimeout(() => document.getElementById('consumables-filter-form').submit(), 500);
});
document.getElementById('cons-category').addEventListener('change', () => document.getElementById('consumables-filter-form').submit());
document.getElementById('cons-status').addEventListener('change', () => document.getElementById('consumables-filter-form').submit());

// ── ITEM DETAILS ──
async function viewItemDetails(id) {
    const res  = await fetch(`/consumables/${id}`);
    const data = await res.json();
    const item = data.item;

    const critPct = Math.min(100, (item.critical_threshold / (item.max_stock || item.critical_threshold * 4)) * 100);
    const lowPct  = Math.min(100, (item.low_threshold / (item.max_stock || item.low_threshold * 3)) * 100) - critPct;

    document.getElementById('item-details-content').innerHTML = `
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-info-circle"></i> Item Information</div>
            <div class="detail-grid">
                <div class="detail-row"><span>Item Name: </span><strong>${item.item_name}</strong></div>
                <div class="detail-row"><span>Category: </span><strong>${item.category?.name ?? 'Uncategorized'}</strong></div>
                <div class="detail-row"><span>Brand: </span><strong>${item.brand ?? '—'}</strong></div>
                <div class="detail-row"><span>Unit: </span><strong>${item.unit}</strong></div>
                <div class="detail-row"><span>ID Code: </span><strong>${item.id_code}</strong></div>
                <div class="detail-row"><span>Date Added: </span><strong>${new Date(item.created_at).toLocaleDateString()}</strong></div>
            </div>
        </div>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-chart-bar"></i> Stock Status</div>
            <div style="text-align:center; padding:1rem; background:#fafafa; border-radius:10px;">
                <div style="font-size:32px; font-weight:700;">${item.current_stock}</div>
                <div style="font-size:12px; color:#888;">${item.unit}</div>
            </div>
            <div class="threshold-bar">
                <div class="threshold-seg-critical" style="width:${critPct}%"></div>
                <div class="threshold-seg-low" style="width:${lowPct}%"></div>
                <div class="threshold-seg-available" style="width:${100-critPct-lowPct}%"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:11px; color:#888; margin-top:4px;">
                <span>Critical ≤${item.critical_threshold}</span>
                <span>Low ≤${item.low_threshold}</span>
                <span>Available</span>
            </div>
        </div>
        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-history"></i> Recent History</div>
            <div style="max-height:160px; overflow-y:auto;">
                ${data.logs.map(l => `
                    <div class="activity-item">
                        <div class="activity-dot dot-${l.change > 0 ? 'green' : 'red'}"></div>
                        <div>
                            <div class="activity-title">${l.action.charAt(0).toUpperCase()+l.action.slice(1)}: ${l.change > 0 ? '+' : ''}${l.change} (${l.previous} → ${l.new})</div>
                            <div class="activity-meta"><span>${l.by}</span><span>${l.date}</span></div>
                        </div>
                    </div>
                `).join('') || '<p style="font-size:12px; color:#999; text-align:center; padding:1rem;">No history yet.</p>'}
            </div>
        </div>
    `;
    document.getElementById('item-details-modal').classList.add('open');
}

// ── EDIT ITEM ──
function openEditItemModal(id, name, category, maxStock, brand, unit, idCode) {
    document.getElementById('ei-name').value = name;
    document.getElementById('ei-category').value = category;
    document.getElementById('ei-maxstock').value = maxStock !== 'null' ? maxStock : '';
    document.getElementById('ei-brand').value = brand !== 'null' ? brand : '';
    document.getElementById('ei-unit').value = unit;
    document.getElementById('ei-idcode').value = idCode;
    document.getElementById('edit-item-form').action = `/consumables/${id}`;
    document.getElementById('delete-item-form').action = `/consumables/${id}`;
    document.getElementById('edit-item-modal').classList.add('open');
}
function confirmDeleteItem() {
    if (confirm('Delete this consumable item permanently? This cannot be undone.')) {
        document.getElementById('delete-item-form').submit();
    }
}

// ── REFILL ──
function openRefillModal(id, name, unit) {
    document.getElementById('refill-item-name').textContent = name;
    document.getElementById('refill-unit').textContent = unit;
    document.getElementById('refill-form').action = `/consumables/${id}/refill`;
    document.getElementById('refill-modal').classList.add('open');
}

// ── ADD ITEM ROWS ──
let addItemRowCount = 0;
function addItemRow() {
    const idx = addItemRowCount++;
    const wrap = document.createElement('div');
    wrap.className = 'item-row';
    wrap.innerHTML = `
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Item Name *</div><input type="text" name="items[${idx}][item_name]" class="modal-input" required></div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Category</div><input type="text" name="items[${idx}][category]" class="modal-input" placeholder="e.g. Office Supplies"></div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Qty *</div><input type="number" name="items[${idx}][quantity]" class="modal-input" min="0" required></div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Unit *</div><input type="text" name="items[${idx}][unit]" class="modal-input" placeholder="pcs" required></div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Brand</div><input type="text" name="items[${idx}][brand]" class="modal-input"></div>
        <button type="button" class="item-row-remove" onclick="this.closest('.item-row').remove()"><i class="ti ti-trash"></i></button>
    `;
    document.getElementById('add-item-rows').appendChild(wrap);
}
document.querySelector('[onclick="event.preventDefault(); openAddItemModal();"]') // no-op safeguard
function openAddItemModal() {
    document.getElementById('add-item-rows').innerHTML = '';
    addItemRowCount = 0;
    addItemRow();
    document.getElementById('add-item-modal').classList.add('open');
}

// ── REQUEST ROWS ──
let reqItemRowCount = 0;
function addRequestItemRow() {
    const idx = reqItemRowCount++;
    const options = consumablesData.map(c => `<option value="${c.id}">${c.name} (Available: ${c.stock} ${c.unit})</option>`).join('');
    const wrap = document.createElement('div');
    wrap.className = 'req-item-row';
    wrap.innerHTML = `
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Item *</div>
            <select name="items[${idx}][consumable_id]" class="modal-input" required>
                <option value="">-- Choose Item --</option>${options}
            </select>
        </div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Qty *</div><input type="number" name="items[${idx}][quantity]" class="modal-input" min="1" required></div>
        <div class="modal-form-group" style="margin:0;"><div class="modal-label">Purpose</div><input type="text" name="items[${idx}][purpose]" class="modal-input" placeholder="e.g. Office use"></div>
        <button type="button" class="item-row-remove" onclick="this.closest('.req-item-row').remove()"><i class="ti ti-trash"></i></button>
    `;
    document.getElementById('request-item-rows').appendChild(wrap);
}
function openRequestModal() {
    document.getElementById('request-item-rows').innerHTML = '';
    reqItemRowCount = 0;
    addRequestItemRow();
    document.getElementById('request-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush