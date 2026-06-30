@extends('layouts.app')
@section('title', 'All Equipment')
@section('page-title', 'All Equipment Management')

@section('content')

{{-- Hero Banner --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-list-details"></i> All Equipment Management</div>
        <p class="hero-sub">Comprehensive view and management of all equipment across all categories. Filter, assign, and track equipment inventory in real-time.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total</span>{{ $stats['total'] }}</div>
            <div class="hero-chip"><span>Assigned</span>{{ $stats['assigned'] }}</div>
            <div class="hero-chip"><span>Maintenance</span>{{ $stats['maintenance'] }}</div>
        </div>
    </div>
    <div class="hero-right" style="display:flex; gap:8px;">
        <a href="#" class="btn-add" onclick="event.preventDefault(); openReportModal();"><i class="ti ti-file-text"></i> Report</a>
        <a href="#" class="btn-add" id="transfer-btn" onclick="event.preventDefault(); openTransferModal();" style="opacity:0.5; pointer-events:none;"><i class="ti ti-arrows-exchange"></i> Transfer</a>
        <a href="#" class="btn-add" onclick="event.preventDefault(); openCategoryModal();"><i class="ti ti-plus"></i> Add Equipment</a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-stack-2"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Equipment</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['assigned'] }}</div><div class="stat-label">Assigned to Rooms</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-alert-triangle"></i></div>
        <div><div class="stat-value">{{ $stats['unassigned'] }}</div><div class="stat-label">Unassigned</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-tool"></i></div>
        <div><div class="stat-value">{{ $stats['maintenance'] }}</div><div class="stat-label">Under Maintenance</div></div>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <form method="GET" action="{{ route('equipment') }}" id="equip-filter-form">

            <div style="margin-bottom:0.9rem;">
                <div class="filter-label">Equipment Type</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ $type === 'all' ? 'active' : '' }}" data-name="type" data-value="all">All</button>
                    <button type="button" class="filter-pill {{ $type === 'Computer' ? 'active' : '' }}" data-name="type" data-value="Computer">Computer</button>
                    <button type="button" class="filter-pill {{ $type === 'Kitchen' ? 'active' : '' }}" data-name="type" data-value="Kitchen">Kitchen</button>
                    <button type="button" class="filter-pill {{ $type === 'Office' ? 'active' : '' }}" data-name="type" data-value="Office">Office</button>
                    <button type="button" class="filter-pill {{ $type === 'Lab' ? 'active' : '' }}" data-name="type" data-value="Lab">Lab</button>
                    <button type="button" class="filter-pill {{ $type === 'General' ? 'active' : '' }}" data-name="type" data-value="General">General</button>
                </div>
            </div>

            <div style="margin-bottom:0.9rem;">
                <div class="filter-label">Campus</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ !$campusId ? 'active' : '' }}" data-name="campus_id" data-value="">All Campuses</button>
                    @foreach($campuses as $campus)
                    <button type="button" class="filter-pill {{ $campusId == $campus->id ? 'active' : '' }}" data-name="campus_id" data-value="{{ $campus->id }}">{{ $campus->name }}</button>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="filter-label">Status</div>
                <div class="filter-pills">
                    <button type="button" class="filter-pill {{ $status === 'all' ? 'active' : '' }}" data-name="status" data-value="all">All</button>
                    <button type="button" class="filter-pill {{ $status === 'available' ? 'active' : '' }}" data-name="status" data-value="available">Available</button>
                    <button type="button" class="filter-pill {{ $status === 'assigned' ? 'active' : '' }}" data-name="status" data-value="assigned">Assigned</button>
                    <button type="button" class="filter-pill {{ $status === 'maintenance' ? 'active' : '' }}" data-name="status" data-value="maintenance">Maintenance</button>
                    <button type="button" class="filter-pill {{ $status === 'condemned' ? 'active' : '' }}" data-name="status" data-value="condemned">Condemned</button>
                </div>
            </div>

            <input type="hidden" name="type" id="hidden-type" value="{{ $type }}">
            <input type="hidden" name="campus_id" id="hidden-campus" value="{{ $campusId }}">
            <input type="hidden" name="status" id="hidden-status" value="{{ $status }}">
            <input type="hidden" name="search" id="hidden-search" value="{{ $search }}">
        </form>
    </div>
</div>

{{-- Table Card --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-list"></i> Equipment Inventory ({{ $paginator->total() }} items)</div>
        <div style="position:relative; width:280px;">
            <i class="ti ti-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:14px;"></i>
            <input type="text" id="equip-search" value="{{ $search }}"
                   placeholder="Search name, serial, property no..."
                   style="width:100%; padding:8px 12px 8px 34px; border:1.5px solid var(--border);
                          border-radius:8px; font-size:12.5px; font-family:inherit; outline:none;">
        </div>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:36px;"><input type="checkbox" id="select-all-equip"></th>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>Serial / Property No.</th>
                    <th>Status</th>
                    <th>Accountable Person</th>
                    <th>Location</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paginator as $item)
                <tr>
                    <td>
                        <input type="checkbox" class="equip-row-checkbox"
                            value="{{ strtolower($item->equipment_type) }}:{{ $item->id }}"
                            data-name="{{ addslashes($item->display_name) }}"
                            data-campus="{{ $item->campus->name ?? '—' }}"
                            data-accountable="{{ $item->remarks ?? '—' }}">
                    </td>
                    <td>
                        <div class="cell-primary">{{ $item->display_name ?? '—' }}</div>
                        @if($item->brand)
                        <div class="cell-secondary">{{ $item->brand }} {{ $item->model ?? '' }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="chip-badge chip-type">{{ $item->equipment_type }}</span>
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->serial_number ?? $item->property_no ?? '—' }}
                    </td>
                    <td>
                        @php
                            $statusColors = [
                                'available'   => 'chip-status-active',
                                'assigned'    => 'chip-campus',
                                'maintenance' => 'chip-equipment-zero',
                                'damaged'     => 'chip-status-inactive',
                                'condemned'   => 'chip-status-inactive',
                                'retired'     => 'chip-status-inactive',
                            ];
                        @endphp
                        <span class="chip-badge {{ $statusColors[$item->status] ?? 'chip-equipment-zero' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->remarks ?? '—' }}
                    </td>
                    <td style="font-size:12px;">
                        {{ $item->location->location_name ?? '—' }}
                    </td>
                    <td style="font-size:11px; color:var(--text-muted);">
                        {{ $item->updated_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('equipment.show', [strtolower($item->equipment_type), $item->id]) }}" class="table-icon-btn view" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('equipment.edit', [strtolower($item->equipment_type), $item->id]) }}" class="table-icon-btn edit" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if(!$item->is_condemned)
                            <button type="button" class="table-icon-btn archive" title="Mark as Condemned"
                                    onclick="openRowCondemnModal('{{ strtolower($item->equipment_type) }}', {{ $item->id }}, '{{ addslashes($item->display_name) }}')">
                                <i class="ti ti-alert-triangle"></i>
                            </button>
                            @endif
                            <a href="{{ route('equipment.report', [strtolower($item->equipment_type), $item->id]) }}" target="_blank" class="table-icon-btn view" title="Generate Report" style="background:#f4f0ff; color:#7c3aed;">
                                <i class="ti ti-file-text"></i>
                            </a>
                            <button type="button" class="table-icon-btn delete" title="Delete"
                                    onclick="openRowDeleteModal('{{ strtolower($item->equipment_type) }}', {{ $item->id }}, '{{ addslashes($item->display_name) }}')">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-device-desktop"></i>
                            <p>No equipment found. Try adjusting your filters, or add new equipment.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paginator->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>
        {{ $paginator->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- ═══════════════════ STEP 1: CATEGORY PICKER ═══════════════════ --}}
<div class="modal-overlay" id="category-modal">
    <div class="modal-box-lg" style="max-width:720px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-plus"></i> Add New Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1.25rem;">
            Choose the type of equipment you want to add to inventory.
        </p>
        <div class="category-grid">
            <div class="category-card" onclick="selectCategory('Computer')">
                <div class="category-icon" style="background:#1a6b3a;"><i class="ti ti-device-desktop"></i></div>
                <div class="category-name">Computer</div>
                <div class="category-desc">Desktops, Laptops, All-in-One</div>
            </div>
            <div class="category-card" onclick="selectCategory('Kitchen')">
                <div class="category-icon" style="background:#ef9f27;"><i class="ti ti-tools-kitchen-2"></i></div>
                <div class="category-name">Kitchen</div>
                <div class="category-desc">Kitchen appliances & equipment</div>
            </div>
            <div class="category-card" onclick="selectCategory('Office')">
                <div class="category-icon" style="background:#3b82f6;"><i class="ti ti-briefcase"></i></div>
                <div class="category-name">Office</div>
                <div class="category-desc">Office furniture & equipment</div>
            </div>
            <div class="category-card" onclick="selectCategory('Lab')">
                <div class="category-icon" style="background:#e24b4a;"><i class="ti ti-flask"></i></div>
                <div class="category-name">Laboratory</div>
                <div class="category-desc">Lab equipment & instruments</div>
            </div>
            <div class="category-card" onclick="selectCategory('General')">
                <div class="category-icon" style="background:#7c3aed;"><i class="ti ti-package"></i></div>
                <div class="category-name">General</div>
                <div class="category-desc">Miscellaneous items</div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════ STEP 2: ADDITION METHOD ═══════════════════ --}}
<div class="modal-overlay" id="method-modal">
    <div class="modal-box-lg" style="max-width:560px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-settings"></i> Choose Addition Method</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>

        <div class="method-preview" id="method-preview-box"></div>

        <div class="method-card" onclick="openManualForm()">
            <div class="method-icon"><i class="ti ti-edit"></i></div>
            <div>
                <div class="method-title">Manual Entry</div>
                <div class="method-desc">Fill in details for a single item</div>
            </div>
        </div>

        <a href="#" onclick="backToCategoryModal(event)" class="back-to-categories">
            <i class="ti ti-arrow-left"></i> Back to Categories
        </a>
    </div>
</div>

{{-- ═══════════════════ STEP 3: COMPUTER FORM ═══════════════════ --}}
<div class="modal-overlay" id="form-computer-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Manual Entry — Computer Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>

        <form method="POST" action="{{ route('equipment.store.computer') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label-row">
                        <div class="modal-label">Article (Device Type) *</div>
                        <button type="button" class="manage-articles-link" onclick="openArticleManager('Computer')">Manage</button>
                    </div>
                    <select name="article" class="modal-input" id="computer-article" required onchange="toggleComputerPackage(this)">
                        <option value="">-- Select Article --</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Description *</div>
                    <input type="text" name="description" class="modal-input" placeholder="Enter Description" required>
                </div>
            </div>

            {{-- Single serial (default) --}}
            <div class="modal-form-group" id="single-serial-group">
                <div class="modal-label">Serial Number</div>
                <input type="text" name="serial_number" class="modal-input" placeholder="Enter Serial Number">
            </div>

            {{-- Dual serial (Computer Package) --}}
            <div class="modal-grid" id="dual-serial-group" style="display:none;">
                <div class="modal-form-group">
                    <div class="modal-label">Serial Number (Monitor) *</div>
                    <input type="text" name="serial_number_monitor" class="modal-input" placeholder="Enter Monitor Serial Number">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Serial Number (System Unit) *</div>
                    <input type="text" name="serial_number_system" class="modal-input" placeholder="Enter System Unit Serial Number">
                </div>
            </div>

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Processor *</div>
                    <input type="text" name="processor" class="modal-input" placeholder="Enter Processor" required>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">RAM *</div>
                    <input type="text" name="ram" class="modal-input" placeholder="Enter RAM" required>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Storage *</div>
                    <input type="text" name="storage" class="modal-input" placeholder="Enter Storage" required>
                </div>
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

            @include('partials.equipment_date_purchase')

            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Operating System</div>
                    <input type="text" name="operating_system" class="modal-input" placeholder="Enter Operating System">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Property Number *</div>
                    <input type="text" name="property_no" class="modal-input" placeholder="Enter Property Number" required>
                </div>
            </div>

            <div class="modal-form-group">
                <div class="modal-label">Cost (₱)</div>
                <input type="number" step="0.01" name="cost" class="modal-input" placeholder="Enter Cost (₱)">
            </div>

            @include('partials.equipment_condition_accountable', ['type' => 'Computer'])

            <button type="submit" class="modal-btn-primary" style="margin-top:1rem;"><i class="ti ti-device-floppy"></i> Add to Inventory</button>
        </form>
    </div>
</div>

{{-- ═══════════════════ STEP 3: KITCHEN FORM ═══════════════════ --}}
<div class="modal-overlay" id="form-kitchen-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Manual Entry — Kitchen Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('equipment.store.kitchen') }}">
            @csrf
            @include('partials.equipment_generic_fields', ['type' => 'Kitchen'])
            <button type="submit" class="modal-btn-primary" style="margin-top:1rem;"><i class="ti ti-device-floppy"></i> Add to Inventory</button>
        </form>
    </div>
</div>

{{-- ═══════════════════ STEP 3: OFFICE FORM ═══════════════════ --}}
<div class="modal-overlay" id="form-office-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Manual Entry — Office Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('equipment.store.office') }}">
            @csrf
            @include('partials.equipment_generic_fields', ['type' => 'Office'])
            <button type="submit" class="modal-btn-primary" style="margin-top:1rem;"><i class="ti ti-device-floppy"></i> Add to Inventory</button>
        </form>
    </div>
</div>

{{-- ═══════════════════ STEP 3: LAB FORM ═══════════════════ --}}
<div class="modal-overlay" id="form-lab-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Manual Entry — Laboratory Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('equipment.store.lab') }}">
            @csrf
            @include('partials.equipment_generic_fields', ['type' => 'Lab', 'showCalibration' => true])
            <button type="submit" class="modal-btn-primary" style="margin-top:1rem;"><i class="ti ti-device-floppy"></i> Add to Inventory</button>
        </form>
    </div>
</div>

{{-- ═══════════════════ STEP 3: GENERAL FORM ═══════════════════ --}}
<div class="modal-overlay" id="form-general-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Manual Entry — General Equipment</div>
            <button class="modal-close" onclick="closeAllEquipModals()"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('equipment.store.general') }}">
            @csrf
            @include('partials.equipment_generic_fields', ['type' => 'General', 'showPropertyRequired' => true])
            <button type="submit" class="modal-btn-primary" style="margin-top:1rem;"><i class="ti ti-device-floppy"></i> Add to Inventory</button>
        </form>
    </div>
</div>

{{-- ═══════════════════ ARTICLE MANAGER MODAL ═══════════════════ --}}
<div class="modal-overlay" id="article-manager-modal">
    <div class="modal-box-sm" style="max-width:460px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-tags"></i> Manage Articles — <span id="article-manager-type"></span></div>
            <button class="modal-close" onclick="closeArticleManager()"><i class="ti ti-x"></i></button>
        </div>

        <div style="display:flex; gap:8px; margin-bottom:1rem;">
            <input type="text" id="new-article-input" class="modal-input" placeholder="New article name..." style="flex:1;">
            <button type="button" onclick="addArticle()" class="modal-btn-primary" style="width:auto; padding:0 16px; margin:0;">
                <i class="ti ti-plus"></i>
            </button>
        </div>

        <div id="article-list" style="max-height:280px; overflow-y:auto; display:flex; flex-direction:column; gap:6px;"></div>
    </div>
</div>

{{-- ROW-LEVEL CONDEMN MODAL --}}
<div class="modal-overlay" id="row-condemn-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-alert-triangle" style="color:var(--orange)"></i> Mark as Condemned</div>
            <button class="modal-close" onclick="closeRowCondemnModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">
            This will mark <strong id="row-condemn-name"></strong> as condemned. The item will no longer be available for assignment.
        </p>
        <form method="POST" id="row-condemn-form">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Reason <span style="text-transform:none; font-weight:400;">(optional but recommended)</span></div>
                <textarea name="condemned_reason" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="e.g., Unit no longer powers on, beyond repair..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--orange);">
                <i class="ti ti-alert-triangle"></i> Confirm Condemnation
            </button>
        </form>
    </div>
</div>

{{-- STEP 1: SIMPLE YES/NO CONFIRM --}}
<div class="modal-overlay" id="row-delete-confirm-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-alert-triangle" style="color:var(--red)"></i> Delete Equipment?</div>
            <button class="modal-close" onclick="closeRowDeleteConfirmModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1.25rem; line-height:1.6;">
            Are you sure you want to delete <strong id="row-delete-confirm-name"></strong>? You'll be asked to confirm one more time.
        </p>
        <div style="display:flex; gap:10px;">
            <button type="button" class="btn-back-link" style="flex:1;" onclick="closeRowDeleteConfirmModal()">Cancel</button>
            <button type="button" class="modal-btn-primary" style="flex:1; margin:0; background:var(--red);" onclick="proceedToFinalDelete()">
                <i class="ti ti-check"></i> Yes, Continue
            </button>
        </div>
    </div>
</div>

{{-- STEP 2: TYPE-TO-CONFIRM DELETE --}}
<div class="modal-overlay" id="row-delete-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-trash" style="color:var(--red)"></i> Final Confirmation</div>
            <button class="modal-close" onclick="closeRowDeleteModal()"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            To confirm, type exactly:
        </p>
        <div style="background:#fff5f5; border:1.5px solid #e24b4a; border-radius:8px; padding:10px 14px; margin-bottom:1rem; font-size:13px; font-weight:600; color:#c0392b; text-align:center;" id="row-delete-expected">
        </div>
        <form method="POST" id="row-delete-form">
            @csrf @method('DELETE')
            <div class="modal-form-group">
                <input type="text" name="confirmation_text" class="modal-input" placeholder="Type the confirmation text above" required autocomplete="off">
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--red);">
                <i class="ti ti-trash"></i> Permanently Delete
            </button>
        </form>
    </div>
</div>

{{-- UNDO DELETE TOAST --}}
<div id="undo-toast" style="display:none; position:fixed; bottom:24px; left:50%; transform:translateX(-50%); background:#1a1a1a; color:#fff; padding:14px 20px; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.3); z-index:500; align-items:center; gap:14px; font-size:13px;">
    <span id="undo-toast-text"></span>
    <button onclick="executeUndo()" style="background:#1a6b3a; color:#fff; border:none; padding:6px 14px; border-radius:6px; font-weight:600; cursor:pointer; font-size:12.5px;">Undo</button>
    <span id="undo-countdown" style="color:#aaa; font-size:11px; min-width:20px; text-align:center;">10</span>
</div>

{{-- TRANSFER MODAL --}}
<div class="modal-overlay" id="transfer-modal">
    <div class="modal-box-lg" style="max-width:620px;">
        <div class="modal-header-row" style="background:#ef9f27; margin:-1.5rem -1.5rem 1.25rem; padding:1.1rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;"><i class="ti ti-arrows-exchange"></i> Transfer Inventory</div>
            <button class="modal-close" onclick="closeTransferModal()"><i class="ti ti-x"></i></button>
        </div>

        <div class="transfer-section-label">Selected Items (<span id="transfer-count">0</span>)</div>
        <div class="transfer-items-box" id="transfer-items-list"></div>

        <form method="POST" action="{{ route('equipment.bulk-transfer') }}" id="transfer-form">
            @csrf
            <input type="hidden" name="items" id="transfer-items-input">

            <div class="modal-grid" style="margin-top:1rem;">
                <div class="modal-form-group">
                    <div class="modal-label">Transfer to Campus *</div>
                    <select name="campus_id" class="modal-input transfer-campus-select" required>
                        <option value="">-- Select Destination --</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Assign New Room <span style="text-transform:none; font-weight:400;">(optional)</span></div>
                    <select name="location_id" class="modal-input transfer-location-select">
                        <option value="">-- Unassigned / Storage --</option>
                    </select>
                </div>
            </div>

            <div class="modal-form-group" style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem;">
                <div class="modal-label">Transfer Accountable Type</div>
                <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 0.75rem;">
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                        <!-- Added name="transfer_accountable_type" group -->
                        <input type="radio" name="transfer_accountable_type" value="existing" checked onchange="toggleAccountableType(this)" style="accent-color: #ef9f27;">
                        Existed UCC - IMS User
                    </label>
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer;">
                        <!-- Added name="transfer_accountable_type" group -->
                        <input type="radio" name="transfer_accountable_type" value="manual" onchange="toggleAccountableType(this)" style="accent-color: #ef9f27;">
                        Non-existing UCC - IMS User
                    </label>
                </div>

                <!-- Existing Transfer Dropdown Selector -->
                <div class="existing-user-group">
                    <div class="modal-label">Select Destination UCC - IMS User</div>
                    <select name="user_id" class="modal-input accountable-select-field">
                        <option value="">-- Leave blank to keep current assignment --</option>
                        @foreach($imsUsers as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Manual Transfer Entry Section -->
                <div class="manual-user-group" style="display: none;">
                    <div class="modal-label">New Accountable Person</div>
                    <div style="display:flex; gap:6px;">
                        <input type="text" name="acc_last" class="modal-input accountable-manual-input" placeholder="Last Name">
                        <input type="text" name="acc_first" class="modal-input accountable-manual-input" placeholder="First Name">
                        <input type="text" name="acc_mi" class="modal-input accountable-manual-input" placeholder="M.I." style="max-width:60px;">
                    </div>
                </div>
                <div class="modal-hint" style="margin-top:6px;">Leave blank to keep the current accountable person on record.</div>
            </div>

            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;"><i class="ti ti-arrows-exchange"></i> Execute Transfer</button>
        </form>
    </div>
</div>

<style>
.transfer-section-label {
    font-size: 12px; font-weight: 700; color: var(--text-primary);
    margin-bottom: 0.5rem;
}
.transfer-items-box {
    border: 1px solid var(--border); border-radius: 10px;
    max-height: 160px; overflow-y: auto;
}
.transfer-item-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 12px; font-size: 12.5px; border-bottom: 1px solid var(--border);
}
.transfer-item-row:last-child { border-bottom: none; }
.transfer-item-name { font-weight: 600; }
.transfer-item-meta { color: var(--text-muted); font-size: 11.5px; }
</style>

<div class="modal-overlay" id="report-modal">
    <div class="modal-box-lg" style="max-width:480px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-file-text"></i> Generate Inventory Report</div>
            <button class="modal-close" onclick="closeReportModal()"><i class="ti ti-x"></i></button>
        </div>

        <p style="font-size:13px; color:#666; text-align:center; margin-bottom:1.25rem;">
            Select filters to generate a customized inventory report.
        </p>

        <form method="GET" action="{{ route('equipment.report.bulk') }}" target="_blank">
            <div class="modal-form-group">
                <div class="modal-label">1. Report Type</div>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="report-type-btn active" data-value="campus" onclick="selectReportType(this)">
                        <i class="ti ti-building"></i> By Campus
                    </button>
                    <button type="button" class="report-type-btn" data-value="person" onclick="selectReportType(this)">
                        <i class="ti ti-user"></i> By Person
                    </button>
                </div>
                <input type="hidden" name="report_type" id="report-type-input" value="campus">
            </div>

            <div class="modal-form-group" id="report-campus-group">
                <div class="modal-label">2. Select Campus</div>
                <select name="campus_id" class="modal-input">
                    <option value="">All Campuses</option>
                    @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                    @endforeach
                </select>
                <div class="modal-hint">Select a specific campus or include all.</div>
            </div>

            <div class="modal-form-group" id="report-person-group" style="display:none;">
                <div class="modal-label">2. Select Accountable Person</div>
                <select name="accountable_person" class="modal-input">
                    <option value="">All Persons</option>
                    @foreach($accountablePersons as $person)
                    <option value="{{ $person }}">{{ $person }}</option>
                    @endforeach
                </select>
                <div class="modal-hint">Select a specific person or include all.</div>
            </div>

            <button type="submit" class="modal-btn-primary"><i class="ti ti-file-text"></i> Generate Report</button>
        </form>
    </div>
</div>

<style>
.report-type-btn {
    flex: 1; padding: 10px; border-radius: 8px;
    border: 1.5px solid var(--border); background: #fff;
    color: var(--text-secondary); font-size: 13px; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
    font-family: 'Inter', sans-serif; transition: all 0.15s;
}
.report-type-btn.active { background: var(--green-dark); border-color: var(--green-dark); color: #fff; }
</style>

<style>
.category-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
@media(max-width:600px) { .category-grid { grid-template-columns: 1fr 1fr; } }

.category-card {
    border: 1.5px solid var(--border); border-radius: 12px;
    padding: 1.5rem 1rem; text-align: center; cursor: pointer;
    transition: all 0.18s;
}
.category-card:hover { border-color: var(--green-dark); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }

.category-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 24px; margin: 0 auto 0.85rem;
}
.category-name { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
.category-desc { font-size: 11.5px; color: var(--text-muted); line-height: 1.4; }

.method-preview {
    display: flex; align-items: center; flex-direction: column; gap: 8px;
    padding: 1.5rem; background: #fafafa; border-radius: 12px; margin-bottom: 1.25rem;
}
.method-preview-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 24px;
}
.method-preview-name { font-size: 15px; font-weight: 700; color: var(--text-primary); }

.method-card {
    border: 1.5px solid var(--border); border-radius: 12px;
    padding: 1.2rem; display: flex; align-items: center; gap: 14px;
    cursor: pointer; transition: all 0.18s; margin-bottom: 1rem;
}
.method-card:hover { border-color: var(--green-dark); background: var(--green-light); }
.method-icon {
    width: 44px; height: 44px; border-radius: 10px;
    background: var(--green-dark); color: #fff;
    display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
}
.method-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
.method-desc { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

.back-to-categories {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--green-dark); font-weight: 600;
    text-decoration: none; justify-content: center;
}

.modal-label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 5px; }
.manage-articles-link {
    font-size: 11px; color: var(--green-dark); font-weight: 600;
    background: none; border: none; cursor: pointer; text-decoration: underline;
    font-family: 'Inter', sans-serif;
}

.date-toggle-row { display: flex; gap: 1.5rem; align-items: center; margin-bottom: 8px; }
.date-toggle-option { display: flex; align-items: center; gap: 6px; font-size: 13px; cursor: pointer; }
.date-toggle-option input { accent-color: var(--green-dark); }

.article-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px;
    font-size: 13px;
}
.article-row input {
    border: none; outline: none; font-size: 13px; font-family: inherit;
    flex: 1; background: transparent;
}
.article-row-actions { display: flex; gap: 4px; }
.article-row-actions button {
    width: 26px; height: 26px; border-radius: 6px; border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center; font-size: 12px;
}

.filter-label {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px;
    color: var(--text-muted); margin-bottom: 8px;
}

.filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }

.filter-pill {
    padding: 7px 16px; border-radius: 20px;
    border: 1.5px solid var(--border);
    background: #fff; color: var(--text-secondary);
    font-size: 12.5px; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
    font-family: 'Inter', sans-serif;
}

.filter-pill:hover { border-color: var(--green-dark); color: var(--green-dark); }

.filter-pill.active {
    background: var(--green-dark);
    border-color: var(--green-dark);
    color: #fff;
    font-weight: 600;
}
</style>

@endsection

@push('scripts')
<script>
// ── ACCOUNTABLE PERSON TYPE TOGGLE ENGINE ──
function toggleAccountableType(radioElement) {
    const formGroupContainer = radioElement.closest('.modal-form-group');
    const existingSection = formGroupContainer.querySelector('.existing-user-group');
    const manualSection   = formGroupContainer.querySelector('.manual-user-group');
    
    const dropdownSelect  = formGroupContainer.querySelector('.accountable-select-field');
    const manualInputs    = formGroupContainer.querySelectorAll('.accountable-manual-input');

    if (radioElement.value === 'existing') {
        existingSection.style.display = 'block';
        manualSection.style.display   = 'none';
        
        if (dropdownSelect && dropdownSelect.closest('form').id !== 'transfer-form') {
            dropdownSelect.required = true;
        }
        manualInputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
    } else {
        existingSection.style.display = 'none';
        manualSection.style.display   = 'block';
        
        if (dropdownSelect) {
            dropdownSelect.required = false;
            dropdownSelect.value = '';
        }
        manualInputs.forEach(input => {
            if (input.closest('form').id !== 'transfer-form' && !input.name.includes('acc_mi')) {
                input.required = true;
            }
        });
    }
}

// ── CHECKBOX SELECTION ──
function updateTransferButton() {
    const checked = document.querySelectorAll('.equip-row-checkbox:checked');
    const btn = document.getElementById('transfer-btn');
    if (checked.length > 0) {
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    } else {
        btn.style.opacity = '0.5';
        btn.style.pointerEvents = 'none';
    }
}

document.getElementById('select-all-equip')?.addEventListener('change', function() {
    document.querySelectorAll('.equip-row-checkbox').forEach(cb => cb.checked = this.checked);
    updateTransferButton();
});

document.querySelectorAll('.equip-row-checkbox').forEach(cb => {
    cb.addEventListener('change', updateTransferButton);
});

// ── TRANSFER MODAL ──
function openTransferModal() {
    const checked = document.querySelectorAll('.equip-row-checkbox:checked');
    if (checked.length === 0) return;

    const items = Array.from(checked).map(cb => cb.value);
    document.getElementById('transfer-items-input').value = JSON.stringify(items);
    document.getElementById('transfer-count').textContent = checked.length;

    const list = document.getElementById('transfer-items-list');
    list.innerHTML = Array.from(checked).map(cb => `
        <div class="transfer-item-row">
            <span class="transfer-item-name">${cb.dataset.name}</span>
            <span class="transfer-item-meta">${cb.dataset.campus} • ${cb.dataset.accountable}</span>
        </div>
    `).join('');

    document.getElementById('transfer-modal').classList.add('open');
}
function closeTransferModal() {
    document.getElementById('transfer-modal').classList.remove('open');
}

document.querySelector('.transfer-campus-select').addEventListener('change', async function() {
    const campusId = this.value;
    const locSelect = document.querySelector('.transfer-location-select');
    locSelect.innerHTML = '<option value="">Loading...</option>';
    if (!campusId) { locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>'; return; }

    const res  = await fetch(`{{ route('equipment.locations-by-campus') }}?campus_id=${campusId}`);
    const data = await res.json();

    locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>';
    data.forEach(loc => {
        locSelect.innerHTML += `<option value="${loc.id}">${loc.location_name}</option>`;
    });
});

// ── REPORT MODAL ──
function openReportModal() {
    document.getElementById('report-modal').classList.add('open');
}
function closeReportModal() {
    document.getElementById('report-modal').classList.remove('open');
}
function selectReportType(btn) {
    document.querySelectorAll('.report-type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const type = btn.dataset.value;
    document.getElementById('report-type-input').value = type;
    document.getElementById('report-campus-group').style.display = type === 'campus' ? 'block' : 'none';
    document.getElementById('report-person-group').style.display = type === 'person' ? 'block' : 'none';
}
</script>

<script>
// Filter pill clicks
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const name  = this.dataset.name;
        const value = this.dataset.value;
        document.getElementById('hidden-' + (name === 'campus_id' ? 'campus' : name)).value = value;
        document.getElementById('equip-filter-form').submit();
    });
});

// Search with debounce
let equipSearchTimeout;
document.getElementById('equip-search').addEventListener('input', function() {
    clearTimeout(equipSearchTimeout);
    const val = this.value;
    equipSearchTimeout = setTimeout(() => {
        document.getElementById('hidden-search').value = val;
        document.getElementById('equip-filter-form').submit();
    }, 500);
});
</script>

<script>
let currentCategory = '';
let currentManagerType = '';

const categoryMeta = {
    Computer: { color: '#1a6b3a', icon: 'ti-device-desktop' },
    Kitchen:  { color: '#ef9f27', icon: 'ti-tools-kitchen-2' },
    Office:   { color: '#3b82f6', icon: 'ti-briefcase' },
    Lab:      { color: '#e24b4a', icon: 'ti-flask' },
    General:  { color: '#7c3aed', icon: 'ti-package' },
};

// Open category picker when "Add Equipment" clicked
document.querySelector('.btn-add[href="#"]:last-of-type')?.addEventListener('click', function(e) {
    e.preventDefault();
    openCategoryModal();
});

function openCategoryModal() {
    document.getElementById('category-modal').classList.add('open');
}

function selectCategory(type) {
    currentCategory = type;
    document.getElementById('category-modal').classList.remove('open');

    const meta = categoryMeta[type];
    document.getElementById('method-preview-box').innerHTML = `
        <div class="method-preview-icon" style="background:${meta.color}"><i class="ti ${meta.icon}"></i></div>
        <div class="method-preview-name">${type} Equipment</div>
    `;
    document.getElementById('method-modal').classList.add('open');
}

function backToCategoryModal(e) {
    e.preventDefault();
    document.getElementById('method-modal').classList.remove('open');
    document.getElementById('category-modal').classList.add('open');
}

function openManualForm() {
    document.getElementById('method-modal').classList.remove('open');
    const modalId = 'form-' + currentCategory.toLowerCase() + '-modal';
    document.getElementById(modalId).classList.add('open');
    loadArticlesIntoForm(currentCategory, modalId);
}

function closeAllEquipModals() {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
}

// Load articles into the relevant select for the open form
async function loadArticlesIntoForm(type, modalId) {
    const res  = await fetch(`{{ route('equipment.articles.index') }}?type=${type}`);
    const data = await res.json();

    let select;
    if (type === 'Computer') {
        select = document.getElementById('computer-article');
    } else {
        select = document.querySelector(`#${modalId} .article-select-${type}`);
    }
    if (!select) return;

    select.innerHTML = '<option value="">-- Select Article --</option>';
    data.forEach(a => {
        select.innerHTML += `<option value="${a.name}">${a.name}</option>`;
    });
}

// Computer Package toggle
function toggleComputerPackage(select) {
    const isPackage = select.value === 'Computer Package';
    document.getElementById('single-serial-group').style.display = isPackage ? 'none' : 'block';
    document.getElementById('dual-serial-group').style.display   = isPackage ? 'grid' : 'none';
}

// Date toggle (have date / no date)
function toggleDateInput(radio) {
    const wrap  = radio.closest('.modal-form-group');
    const input = wrap.querySelector('.purchase-date-input');
    input.style.display = radio.value === '1' ? 'block' : 'none';
    input.disabled = radio.value === '0';
}

// Campus -> Location AJAX
document.addEventListener('change', async function(e) {
    if (e.target.classList.contains('campus-select')) {
        const campusId = e.target.value;
        const formGroup = e.target.closest('form');
        const locSelect = formGroup.querySelector('.location-select');

        locSelect.innerHTML = '<option value="">Loading...</option>';
        if (!campusId) {
            locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>';
            return;
        }

        const res  = await fetch(`{{ route('equipment.locations-by-campus') }}?campus_id=${campusId}`);
        const data = await res.json();

        locSelect.innerHTML = '<option value="">-- Unassigned / Storage --</option>';
        data.forEach(loc => {
            locSelect.innerHTML += `<option value="${loc.id}">${loc.location_name}</option>`;
        });
    }
});

// ── ARTICLE MANAGER ──
function openArticleManager(type) {
    currentManagerType = type;
    document.getElementById('article-manager-type').textContent = type;
    document.getElementById('article-manager-modal').classList.add('open');
    refreshArticleList();
}

function closeArticleManager() {
    document.getElementById('article-manager-modal').classList.remove('open');
    const openForm = document.querySelector('.modal-overlay.open[id^="form-"]');
    if (openForm) loadArticlesIntoForm(currentManagerType, openForm.id);
}

async function refreshArticleList() {
    const res  = await fetch(`{{ route('equipment.articles.index') }}?type=${currentManagerType}`);
    const data = await res.json();
    const list = document.getElementById('article-list');

    list.innerHTML = data.map(a => `
        <div class="article-row" data-id="${a.id}">
            <input type="text" value="${a.name}" onblur="updateArticle(${a.id}, this.value)">
            <div class="article-row-actions">
                <button onclick="deleteArticle(${a.id})" style="background:#fff5f5; color:var(--red);"><i class="ti ti-trash"></i></button>
            </div>
        </div>
    `).join('') || '<p style="font-size:12px; color:#999; text-align:center; padding:1rem;">No articles yet.</p>';
}

async function addArticle() {
    const input = document.getElementById('new-article-input');
    if (!input.value.trim()) return;

    await fetch('{{ route("equipment.articles.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ equipment_type: currentManagerType, name: input.value.trim() })
    });

    input.value = '';
    refreshArticleList();
}

async function updateArticle(id, name) {
    if (!name.trim()) return;
    await fetch(`/equipment-articles/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ name: name.trim() })
    });
}

async function deleteArticle(id) {
    if (!confirm('Remove this article?')) return;
    await fetch(`/equipment-articles/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    refreshArticleList();
}

// Close modals on overlay click (except nested article manager closing properly)
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
</script>

<script>
function openRowCondemnModal(type, id, name) {
    document.getElementById('row-condemn-name').textContent = name;
    document.getElementById('row-condemn-form').action = `/equipment/${type}/${id}/condemn`;
    document.getElementById('row-condemn-modal').classList.add('open');
}
function closeRowCondemnModal() {
    document.getElementById('row-condemn-modal').classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
<script>
let pendingDeleteType = null;
let pendingDeleteId = null;
let pendingDeleteName = null;

function openRowDeleteModal(type, id, name) {
    pendingDeleteType = type;
    pendingDeleteId = id;
    pendingDeleteName = name;
    document.getElementById('row-delete-confirm-name').textContent = name;
    document.getElementById('row-delete-confirm-modal').classList.add('open');
}
function closeRowDeleteConfirmModal() {
    document.getElementById('row-delete-confirm-modal').classList.remove('open');
}

function proceedToFinalDelete() {
    closeRowDeleteConfirmModal();
    document.getElementById('row-delete-expected').textContent = 'Delete ' + pendingDeleteName;
    document.getElementById('row-delete-form').action = `/equipment/${pendingDeleteType}/${pendingDeleteId}`;
    document.getElementById('row-delete-modal').classList.add('open');
}
function closeRowDeleteModal() {
    document.getElementById('row-delete-modal').classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ── UNDO TOAST ──
let undoTimer = null;
let undoSecondsLeft = 10;
let undoType = null;
let undoId = null;

function showUndoToast(type, id, name) {
    undoType = type;
    undoId = id;
    undoSecondsLeft = 10;

    document.getElementById('undo-toast-text').textContent = `"${name}" was deleted.`;
    document.getElementById('undo-countdown').textContent = undoSecondsLeft;
    document.getElementById('undo-toast').style.display = 'flex';

    clearInterval(undoTimer);
    undoTimer = setInterval(() => {
        undoSecondsLeft--;
        document.getElementById('undo-countdown').textContent = undoSecondsLeft;
        if (undoSecondsLeft <= 0) {
            clearInterval(undoTimer);
            document.getElementById('undo-toast').style.display = 'none';
        }
    }, 1000);
}

async function executeUndo() {
    clearInterval(undoTimer);
    document.getElementById('undo-toast').style.display = 'none';

    const res = await fetch(`/equipment/${undoType}/${undoId}/undo-delete`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    if (res.ok) {
        window.location.reload();
    } else {
        const data = await res.json();
        alert(data.message || 'Could not undo — the item may have already been permanently removed.');
    }
}

window.addEventListener('pageshow', function(event) {
    // event.persisted is true when the page is restored from bfcache (back/forward navigation)
    if (event.persisted) {
        document.getElementById('undo-toast').style.display = 'none';
        return;
    }

    @if(session('undo_delete'))
    showUndoToast('{{ session('undo_type') }}', {{ session('undo_id') }}, '{{ addslashes(session('undo_name')) }}');
    @endif
});
</script>
@endpush