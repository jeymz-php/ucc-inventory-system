@extends('layouts.app')
@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-settings"></i> System Settings</div>
        <p class="hero-sub">Manage your account security, monitor system health, publish version updates, and configure system data.</p>
    </div>
</div>

<div class="settings-cards-grid">

    {{-- Change Password --}}
    <a href="#" onclick="event.preventDefault(); document.getElementById('change-password-modal').classList.add('open');" class="settings-tile">
        <div class="settings-tile-icon" style="background:#eff6ff; color:#3b82f6;"><i class="ti ti-lock-password"></i></div>
        <div class="settings-tile-title">Change Password</div>
        <div class="settings-tile-desc">Update your account password for better security.</div>
    </a>

    @if(auth()->user()->role === 'superadmin')

    {{-- System Status --}}
    <a href="{{ route('system.status') }}" class="settings-tile">
        <div class="settings-tile-icon" style="background:#f0faf4; color:#1a6b3a;"><i class="ti ti-activity"></i></div>
        <div class="settings-tile-title">System Status</div>
        <div class="settings-tile-desc">Monitor uptime, toggle maintenance mode for IMS & CS, and review system logs.</div>
    </a>

    {{-- Version History & Updates --}}
    <a href="{{ route('system.updates') }}" class="settings-tile">
        <div class="settings-tile-icon" style="background:#faf0ff; color:#7c3aed;"><i class="ti ti-git-branch"></i></div>
        <div class="settings-tile-title">Version History & Updates</div>
        <div class="settings-tile-desc">Publish system update notes for IMS and CS. Toggle the login modal to notify users.</div>
    </a>

    {{-- Managing System --}}
    <a href="{{ route('manage.index') }}" class="settings-tile">
        <div class="settings-tile-icon" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-database-cog"></i></div>
        <div class="settings-tile-title">Managing System</div>
        <div class="settings-tile-desc">Manage system data — departments, campuses, and other configurations.</div>
    </a>

    @endif

</div>

<style>
.settings-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}
.settings-tile {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.5rem;
    text-decoration: none;
    transition: all 0.18s;
    display: block;
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