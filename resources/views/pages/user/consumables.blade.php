@extends('layouts.app')
@section('title', 'Consumables')
@section('page-title', 'Consumables')

@section('content')

@if($errors->any())
<div class="alert alert-error">
    <i class="ti ti-alert-circle"></i>
    <div class="alert-text">
        <strong>Request Failed</strong>
        <ul style="margin:4px 0 0; padding-left:18px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@php
    $firstName = explode(' ', auth()->user()->name)[0];
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
@endphp

{{-- Hero Banner --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-hand-stop"></i> Welcome back, {{ $firstName }}!</div>
        <p class="hero-sub">Browse available consumable items and submit your requests.</p>
        <div class="hero-chips">
            <div class="hero-chip"><i class="ti ti-calendar" style="font-size:12px; margin-right:4px;"></i>{{ now()->format('l, F d, Y') }}</div>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-package"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Items</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $stats['low'] }}</div><div class="stat-label">Low Stock</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-circle"></i></div>
        <div><div class="stat-value">{{ $stats['critical'] }}</div><div class="stat-label">Critical</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f5f5f5; color:#999;"><i class="ti ti-ban"></i></div>
        <div><div class="stat-value">{{ $stats['out_of_stock'] }}</div><div class="stat-label">Out of Stock</div></div>
    </div>
</div>

{{-- Search & Filter Pills --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <div style="position:relative; margin-bottom:1rem;">
            <i class="ti ti-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#aaa; font-size:16px;"></i>
            <input type="text" id="user-cons-search" placeholder="Search items by name, brand, or category..."
                   style="width:100%; padding:12px 16px 12px 40px; border:1.5px solid var(--border); border-radius:10px; font-size:14px; font-family:inherit; outline:none;">
        </div>
        <div class="filter-pills">
            <button type="button" class="filter-pill user-stock-pill active" data-value="all">All</button>
            <button type="button" class="filter-pill user-stock-pill" data-value="available">Available</button>
            <button type="button" class="filter-pill user-stock-pill" data-value="low">Low Stock</button>
            <button type="button" class="filter-pill user-stock-pill" data-value="critical">Critical</button>
            <button type="button" class="filter-pill user-stock-pill" data-value="out">Out of Stock</button>
        </div>
    </div>
</div>

{{-- Item Cards Grid --}}
<div class="user-items-grid" id="user-items-grid">
    @foreach($items as $item)
    @php
        $isOut = $item->current_stock <= 0;
        $statusLabel = $isOut ? 'Out of Stock' : ucfirst($item->status);
        $canRequest = !$isOut;
    @endphp
    <div class="user-item-card status-{{ $isOut ? 'out' : $item->status }}"
         data-name="{{ strtolower($item->item_name) }}"
         data-brand="{{ strtolower($item->brand ?? '') }}"
         data-category="{{ strtolower($item->category->name ?? '') }}"
         data-status="{{ $isOut ? 'out' : $item->status }}">
        <div class="user-item-top">
            <span class="user-item-category"><i class="ti ti-tag" style="font-size:11px;"></i> {{ $item->category->name ?? 'Uncategorized' }}</span>
            <span class="user-item-status-badge status-badge-{{ $isOut ? 'out' : $item->status }}">
                <i class="ti ti-{{ $isOut ? 'ban' : ($item->status === 'critical' ? 'x' : ($item->status === 'low' ? 'alert-triangle' : 'circle-check')) }}" style="font-size:10px;"></i>
                {{ $statusLabel }}
            </span>
        </div>
        <div class="user-item-name">{{ $item->item_name }}</div>
        @if($item->brand)
        <div class="user-item-brand"><i class="ti ti-bookmark" style="font-size:11px;"></i> {{ $item->brand }}</div>
        @else
        <div class="user-item-brand">&nbsp;</div>
        @endif

        <div class="user-item-meta">
            <div class="user-item-meta-col">
                <div class="user-item-meta-label">Stock</div>
                <div class="user-item-meta-value">{{ $item->current_stock }} {{ $item->unit }}</div>
            </div>
            <div class="user-item-meta-col" style="text-align:right;">
                <div class="user-item-meta-label">ID</div>
                <div class="user-item-meta-value">#{{ $item->id }}</div>
            </div>
        </div>

        @if($canRequest)
        <button class="user-item-btn btn-can-request" onclick="addToCart({{ $item->id }}, '{{ addslashes($item->item_name) }}', '{{ $item->unit }}', {{ $item->current_stock }})">
            <i class="ti ti-shopping-cart-plus"></i> Add to Request
        </button>
        @else
        <button class="user-item-btn btn-cannot-request" disabled>
            <i class="ti ti-ban"></i> Cannot Request
        </button>
        @endif
    </div>
    @endforeach
</div>

<div class="empty-state" id="user-no-results" style="display:none;">
    <i class="ti ti-search-off"></i>
    <p>No items match your search or filter.</p>
</div>

{{-- Floating Cart Button --}}
<button class="floating-cart-btn" id="floating-cart-btn" onclick="openCartModal()" style="display:none;">
    <i class="ti ti-shopping-cart"></i>
    <span class="cart-count-badge" id="cart-count-badge">0</span>
</button>

{{-- CART / REQUEST MODAL --}}
<div class="modal-overlay" id="cart-modal">
    <div class="modal-box-lg" style="max-width:620px;">
        <div class="modal-header-row" style="background:var(--green-dark); margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-shopping-cart"></i> My Request Cart</div>
            <button class="modal-close" onclick="document.getElementById('cart-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>

        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-user"></i> Your Information</div>
            <br>
            <div class="detail-grid">
                <div class="detail-row"><span>Full Name: </span><strong>{{ auth()->user()->name }}</strong></div>
                <div class="detail-row"><span>Office/Department: </span><strong>{{ auth()->user()->department->department_name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Campus: </span><strong>{{ auth()->user()->campus->name ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Contact Number: </span><strong>{{ auth()->user()->phone ?? 'N/A' }}</strong></div>
                <div class="detail-row"><span>Request Date: </span><strong>{{ now()->format('M d, Y') }}</strong></div>
            </div>
        </div>

        <br>

        <div id="cart-items-list" style="margin-bottom:1.25rem;"></div>

        <div class="detail-section">
            <div class="detail-section-title"><i class="ti ti-signature"></i> Signatories</div>
            <br>
            <div class="detail-grid">
                <div class="detail-row"><span>Approved By: </span><strong>REYNALDO H. CARANDANG JR.</strong></div>
                <div class="detail-row"><span>Supply Officer: </span><strong>MARVIN Z. GERVACIO</strong></div>
            </div>
        </div>

        <form method="POST" action="{{ route('consumable-requests.store') }}" id="cart-submit-form">
            @csrf
            <div id="cart-hidden-items"></div>
            <button type="submit" class="modal-btn-primary" id="cart-submit-btn"><i class="ti ti-send"></i> Submit Request</button>
        </form>
    </div>
</div>

<style>
.user-items-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.1rem;
}
@media(max-width:1100px) { .user-items-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:680px)  { .user-items-grid { grid-template-columns: 1fr; } }

.user-item-card {
    background: #fff;
    border: 1px solid var(--border);
    border-left: 4px solid var(--green-dark);
    border-radius: 12px;
    padding: 1.1rem;
    transition: transform 0.15s, box-shadow 0.15s;
}
.user-item-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.06); }
.user-item-card.status-critical { border-left-color: var(--red); }
.user-item-card.status-low      { border-left-color: #ef9f27; }
.user-item-card.status-available{ border-left-color: var(--green-dark); }
.user-item-card.status-out      { border-left-color: #999; opacity: 0.75; }

.user-item-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.6rem; }
.user-item-category {
    font-size: 10.5px; font-weight: 600; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.4px;
    display: flex; align-items: center; gap: 4px;
}
.user-item-status-badge {
    font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 20px;
    display: flex; align-items: center; gap: 4px; flex-shrink: 0;
}
.status-badge-critical { background: #fff5f5; color: var(--red); }
.status-badge-low { background: #fff8f0; color: #ef9f27; }
.status-badge-available { background: #f0faf4; color: var(--green-dark); }
.status-badge-out { background: #f5f5f5; color: #999; }

.user-item-name { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; line-height: 1.3; }
.user-item-brand { font-size: 11.5px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-bottom: 0.9rem; }

.user-item-meta {
    display: flex; justify-content: space-between;
    padding: 0.75rem 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
    margin-bottom: 0.9rem;
}
.user-item-meta-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
.user-item-meta-value { font-size: 14px; font-weight: 700; color: var(--text-primary); }

.user-item-btn {
    width: 100%; padding: 11px; border-radius: 8px; border: none;
    font-size: 12.5px; font-weight: 600; cursor: pointer;
    font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: opacity 0.15s;
}
.btn-can-request { background: var(--green-dark); color: #fff; }
.btn-can-request:hover { opacity: 0.9; }
.btn-cannot-request { background: #f5f5f5; color: #999; cursor: not-allowed; }
.btn-cannot-request.in-cart { background: #eff6ff; color: #3b82f6; cursor: default; }

.floating-cart-btn {
    position: fixed; bottom: 24px; right: 24px;
    width: 56px; height: 56px; border-radius: 50%;
    background: var(--green-dark); color: #fff; border: none;
    font-size: 22px; cursor: pointer;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    display: flex; align-items: center; justify-content: center;
    z-index: 60; transition: transform 0.2s;
}
.floating-cart-btn:hover { transform: scale(1.08); }
.cart-count-badge {
    position: absolute; top: -4px; right: -4px;
    background: var(--red); color: #fff;
    font-size: 11px; font-weight: 700;
    width: 22px; height: 22px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}

.cart-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px;
    margin-bottom: 8px; gap: 10px;
}
.cart-row-name { font-size: 13px; font-weight: 600; flex: 1; }
.cart-row-qty input { width: 70px; padding: 6px 8px; border:1.5px solid var(--border); border-radius:6px; font-size:13px; text-align:center; }
.cart-row-remove { width: 28px; height: 28px; border-radius: 6px; border: none; background: #fff5f5; color: var(--red); cursor: pointer; flex-shrink: 0; }
</style>

@endsection

@push('scripts')

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    // Can't restore cart contents after a redirect, but at least surface the error clearly
    window.scrollTo(0, 0);
});
@endif
<script>
let cart = {}; // { id: { name, unit, max, qty } }

function updateCartUI() {
    const count = Object.keys(cart).length;
    const btn = document.getElementById('floating-cart-btn');
    document.getElementById('cart-count-badge').textContent = count;
    btn.style.display = count > 0 ? 'flex' : 'none';

    document.querySelectorAll('.user-item-card').forEach(card => {
        // re-evaluated when modal closes via refresh logic below if needed
    });
}

function addToCart(id, name, unit, maxStock) {
    if (cart[id]) {
        if (cart[id].qty < maxStock) cart[id].qty++;
    } else {
        cart[id] = { name, unit, max: maxStock, qty: 1, purpose: '' };
    }
    updateCartUI();

    const btn = event.target.closest('button');
    btn.innerHTML = '<i class="ti ti-check"></i> Added!';
    btn.classList.add('in-cart');
    setTimeout(() => {
        btn.innerHTML = `<i class="ti ti-shopping-cart-plus"></i> Add Another (${cart[id].qty} in cart)`;
    }, 700);
}

function openCartModal() {
    const list = document.getElementById('cart-items-list');
    const hiddenWrap = document.getElementById('cart-hidden-items');

    if (Object.keys(cart).length === 0) {
        list.innerHTML = '<div class="empty-state"><i class="ti ti-shopping-cart-off"></i><p>Your cart is empty.</p></div>';
        hiddenWrap.innerHTML = '';
        document.getElementById('cart-submit-btn').disabled = true;
    } else {
        document.getElementById('cart-submit-btn').disabled = false;
        list.innerHTML = `
            <div class="detail-section-title" style="margin-bottom:0.75rem;"><i class="ti ti-list"></i> Items Requested</div>
            ${Object.entries(cart).map(([id, item]) => `
                <div class="cart-row" style="flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:10px; width:100%; margin-bottom:6px;">
                        <div class="cart-row-name" style="flex:1;">${item.name}</div>
                        <div class="cart-row-qty"><input type="number" min="1" max="${item.max}" value="${item.qty}" onchange="updateCartQty(${id}, this.value)"></div>
                        <div style="font-size:11px; color:#888;">${item.unit}</div>
                        <button type="button" class="cart-row-remove" onclick="removeFromCart(${id})"><i class="ti ti-trash"></i></button>
                    </div>
                    <input type="text" id="purpose-input-${id}" placeholder="Purpose (e.g. Office use, Clinic use...)" required
                           class="modal-input" style="width:100%; padding:8px 10px; font-size:12.5px;"
                           value="${item.purpose || ''}"
                           oninput="cart[${id}].purpose = this.value; syncHiddenInputs();">
                </div>
            `).join('')}
        `;

        syncHiddenInputs();
    }

    document.getElementById('cart-modal').classList.add('open');
}

function syncHiddenInputs() {
    const hiddenWrap = document.getElementById('cart-hidden-items');
    hiddenWrap.innerHTML = Object.entries(cart).map(([id, item], idx) => `
        <input type="hidden" name="items[${idx}][consumable_id]" value="${id}">
        <input type="hidden" name="items[${idx}][quantity]" value="${item.qty}">
        <input type="hidden" name="items[${idx}][purpose]" value="${(item.purpose || '').replace(/"/g, '&quot;')}">
    `).join('');
}

function updateCartQty(id, val) {
    val = parseInt(val);
    if (val < 1) val = 1;
    if (val > cart[id].max) val = cart[id].max;
    cart[id].qty = val;
    openCartModal();
}

function removeFromCart(id) {
    delete cart[id];
    updateCartUI();
    openCartModal();
}

// ── SEARCH & FILTER ──
function filterItems() {
    const search = document.getElementById('user-cons-search').value.toLowerCase();
    const activePill = document.querySelector('.user-stock-pill.active').dataset.value;
    let visibleCount = 0;

    document.querySelectorAll('.user-item-card').forEach(card => {
        const matchesSearch = card.dataset.name.includes(search) || card.dataset.brand.includes(search) || card.dataset.category.includes(search);
        const matchesStatus = activePill === 'all' || card.dataset.status === activePill;
        const show = matchesSearch && matchesStatus;
        card.style.display = show ? 'block' : 'none';
        if (show) visibleCount++;
    });

    document.getElementById('user-no-results').style.display = visibleCount === 0 ? 'block' : 'none';
}

document.getElementById('user-cons-search').addEventListener('input', filterItems);
document.querySelectorAll('.user-stock-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        document.querySelectorAll('.user-stock-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        filterItems();
    });
});

document.getElementById('cart-submit-form').addEventListener('submit', function() {
    syncHiddenInputs();
});

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush