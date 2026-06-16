@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@php
    $role      = auth()->user()->role;
    $firstName = explode(' ', auth()->user()->name)[0];
    $hour      = now()->hour;
    $greeting  = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
@endphp

{{-- ── HERO BANNER ── --}}
<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting">
            <i class="ti ti-chart-line"></i>
            {{ $greeting }}, {{ $firstName }}!
        </div>
        <p class="hero-sub">
            @if($role === 'user')
                Welcome to the UCC Inventory System. You can view equipment assigned to your department.
            @elseif($role === 'admin')
                Here's what's happening with your inventory today. Track equipment status, monitor locations, and manage resources efficiently.
            @else
                Full system overview. Monitor all campuses, manage users, and control all inventory operations.
            @endif
        </p>
        <div class="hero-chips">
            <div class="hero-chip">
                <span>Date:</span>{{ now()->format('M d, Y') }}
            </div>
            <div class="hero-chip">
                <span>Time:</span><span id="hero-clock">{{ now()->format('h:i A') }}</span>
            </div>
            @if(auth()->user()->campus)
            <div class="hero-chip">
                <span>Campus:</span>{{ auth()->user()->campus->name }}
            </div>
            @endif
        </div>
    </div>

    @if(in_array($role, ['admin', 'superadmin']))
    <div class="hero-right">
        <a href="{{ route('equipment') }}" class="btn-add">
            <i class="ti ti-plus"></i> Add Equipment
        </a>
    </div>
    @endif
</div>

{{-- ── USER NOTICE (user role only) ── --}}
@if($role === 'user')
<div class="user-notice">
    <i class="ti ti-info-circle"></i>
    <div class="user-notice-text">
        <h4>Limited Access Account</h4>
        <p>Your account has view-only access. You can see equipment assigned to your department. Contact your administrator to request additional permissions.</p>
    </div>
</div>
@endif

{{-- ── STATS GRID ── --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-device-desktop"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total_equipment'] }}</div>
            <div class="stat-label">Total Equipment</div>
            <div class="stat-sub"><i class="ti ti-category" style="font-size:11px"></i> All categories</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-map-pin"></i></div>
        <div>
            <div class="stat-value">{{ $stats['active_locations'] }}</div>
            <div class="stat-label">Active Locations</div>
            <div class="stat-sub"><i class="ti ti-building" style="font-size:11px"></i> Lab rooms &amp; offices</div>
        </div>
    </div>

    @if(in_array($role, ['admin', 'superadmin']))
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-users"></i></div>
        <div>
            <div class="stat-value">{{ $stats['active_users'] }}</div>
            <div class="stat-label">Active Users</div>
            <div class="stat-sub"><i class="ti ti-shield" style="font-size:11px"></i> All roles</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-alert-triangle"></i></div>
        <div>
            <div class="stat-value">{{ $stats['condemned'] }}</div>
            <div class="stat-label">Condemned Items</div>
            <div class="stat-sub"><i class="ti ti-clock" style="font-size:11px"></i> Pending disposal</div>
        </div>
    </div>
    @else
    {{-- User sees department & personal stats instead --}}
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-building"></i></div>
        <div>
            <div class="stat-value">—</div>
            <div class="stat-label">My Department</div>
            <div class="stat-sub">
                {{ auth()->user()->department->department_name ?? 'Not assigned' }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-user-check"></i></div>
        <div>
            <div class="stat-value">—</div>
            <div class="stat-label">Assigned to Me</div>
            <div class="stat-sub"><i class="ti ti-package" style="font-size:11px"></i> Equipment items</div>
        </div>
    </div>
    @endif

</div>

{{-- ── BOTTOM SECTION ── --}}
@if(in_array($role, ['admin', 'superadmin']))

<div class="three-col">

    {{-- Recent Activity --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-history"></i> Recent Assignment Activity</div>
            <a href="{{ route('history') }}" style="font-size:12px; color:var(--green-dark); text-decoration:none; font-weight:600;">
                View All →
            </a>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <i class="ti ti-clipboard-list"></i>
                <p>No recent activity yet.<br>Activity will appear once equipment is assigned.</p>
            </div>
        </div>
    </div>

    {{-- Condemned Summary (super admin) / Quick info (admin) --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                @if($role === 'superadmin')
                    <i class="ti ti-alert-triangle" style="color:var(--red)"></i> Condemned Equipment
                @else
                    <i class="ti ti-chart-pie"></i> System Overview
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <i class="ti ti-{{ $role === 'superadmin' ? 'alert-triangle' : 'chart-bar' }}"></i>
                <p>Data will populate once inventory is set up.</p>
            </div>
        </div>
    </div>

</div>

{{-- Quick Actions --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-bolt"></i> Quick Actions</div>
    </div>
    <div class="card-body">
        <div class="quick-actions">
            <a href="{{ route('equipment') }}" class="quick-action">
                <i class="ti ti-device-desktop"></i> All Equipment
            </a>
            <a href="{{ route('locations') }}" class="quick-action">
                <i class="ti ti-map-pin"></i> Locations
            </a>
            <a href="{{ route('users') }}" class="quick-action">
                <i class="ti ti-users"></i> Users
            </a>
            <a href="{{ route('consumables') }}" class="quick-action">
                <i class="ti ti-droplet"></i> Consumables
            </a>
            @if($role === 'superadmin')
            <a href="{{ route('condemned') }}" class="quick-action">
                <i class="ti ti-alert-triangle"></i> Condemned
            </a>
            @endif
            <a href="{{ route('history') }}" class="quick-action">
                <i class="ti ti-history"></i> History
            </a>
            <a href="{{ route('categories') }}" class="quick-action">
                <i class="ti ti-tag"></i> Categories
            </a>
            <a href="{{ route('inventory') }}" class="quick-action">
                <i class="ti ti-clipboard-list"></i> Inventory
            </a>
        </div>
    </div>
</div>

@else
{{-- USER DASHBOARD BOTTOM --}}
<div class="two-col">

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-package"></i> Equipment Assigned to Me</div>
        </div>
        <div class="card-body">
            <div class="empty-state">
                <i class="ti ti-device-desktop"></i>
                <p>No equipment currently assigned to you.</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="ti ti-user"></i> My Profile</div>
        </div>
        <div class="card-body">
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                <div style="display:flex; justify-content:space-between; font-size:13px; padding-bottom:0.6rem; border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-muted)">Full Name</span>
                    <span style="font-weight:500">{{ auth()->user()->name }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px; padding-bottom:0.6rem; border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-muted)">Email</span>
                    <span style="font-weight:500">{{ auth()->user()->email }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px; padding-bottom:0.6rem; border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-muted)">Department</span>
                    <span style="font-weight:500">{{ auth()->user()->department->department_name ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px; padding-bottom:0.6rem; border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-muted)">Campus</span>
                    <span style="font-weight:500">{{ auth()->user()->campus->name ?? '—' }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:13px;">
                    <span style="color:var(--text-muted)">Phone</span>
                    <span style="font-weight:500">{{ auth()->user()->phone ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endif

@endsection

@push('scripts')
<script>
// Sync hero clock
function updateHeroClock() {
    const now  = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    const el   = document.getElementById('hero-clock');
    if (el) el.textContent = time;
}
setInterval(updateHeroClock, 1000);
</script>
@endpush