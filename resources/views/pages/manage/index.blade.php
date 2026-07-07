@extends('layouts.app')
@section('title', 'Managing System')
@section('page-title', 'Managing System')

@section('content')

<a href="{{ route('system.settings') }}"
   style="display:inline-flex; align-items:center; gap:6px; font-size:13px;
          color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to System Settings
</a>

<div class="hero-banner" style="background:linear-gradient(135deg, #ef9f27, #d4830f);">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-database-cog"></i> Managing System</div>
        <p class="hero-sub">Configure and manage the core system data that powers UCC-IMS and UCC-CS — departments, campuses, categories, and more.</p>
    </div>
</div>

<div class="settings-cards-grid" style="margin-top:1.25rem;">

    {{-- Departments --}}
    <a href="{{ route('manage.departments') }}" class="settings-tile">
        <div class="settings-tile-icon" style="background:#fff8f0; color:#ef9f27;">
            <i class="ti ti-building-community"></i>
        </div>
        <div class="settings-tile-title">Departments</div>
        <div class="settings-tile-desc">Add, edit, or deactivate departments. Used when assigning users and generating reports.</div>
        <div style="margin-top:0.75rem; font-size:12px; font-weight:600; color:#ef9f27;">
            <i class="ti ti-arrow-right" style="font-size:12px;"></i> Manage Departments
        </div>
    </a>

    {{-- Campuses --}}
    <a href="{{ route('manage.campuses') }}" class="settings-tile">
        <div class="settings-tile-icon" style="background:#eff6ff; color:#3b82f6;">
            <i class="ti ti-map-pin"></i>
        </div>
        <div class="settings-tile-title">Campuses</div>
        <div class="settings-tile-desc">Add, edit, or deactivate campus locations used when assigning users and equipment.</div>
        <div style="margin-top:0.75rem; font-size:12px; font-weight:600; color:#3b82f6;">
            <i class="ti ti-arrow-right" style="font-size:12px;"></i> Manage Campuses
        </div>
    </a>

    {{-- Coming soon placeholder --}}
    <div class="settings-tile" style="opacity:0.45; cursor:default; pointer-events:none;">
        <div class="settings-tile-icon" style="background:#f5f5f5; color:#ccc;">
            <i class="ti ti-tag"></i>
        </div>
        <div class="settings-tile-title">Consumable Categories</div>
        <div class="settings-tile-desc">Manage categories for consumable items. <em>(Coming soon)</em></div>
    </div>

</div>

<style>
.settings-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}
.settings-tile {
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; padding: 1.5rem;
    text-decoration: none; transition: all 0.18s; display: block;
}
.settings-tile:hover {
    border-color: var(--green-dark);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    transform: translateY(-2px);
}
.settings-tile-icon {
    width: 48px; height: 48px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; margin-bottom: 1rem;
}
.settings-tile-title { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 0.4rem; }
.settings-tile-desc  { font-size: 12.5px; color: var(--text-muted); line-height: 1.5; }
</style>

@endsection