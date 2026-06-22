@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'User Details')

@section('content')

<a href="{{ route('users') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Users
</a>

<div class="hero-banner" style="{{ !$user->is_active ? 'background: linear-gradient(135deg, #c0392b 0%, #e24b4a 100%);' : '' }}">
    <div class="hero-left">
        <div class="hero-greeting">
            <i class="ti ti-user-circle"></i> {{ $user->name }}
        </div>
        <p class="hero-sub">{{ $user->email }}</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Role</span>{{ $user->role === 'superadmin' ? 'Super Admin' : ucfirst($user->role) }}</div>
            <div class="hero-chip"><span>Status</span>{{ $user->is_active ? 'Active' : 'Deactivated' }}</div>
            @if($pendingDeletion)
            <div class="hero-chip" style="background:rgba(255,255,255,0.3);"><span>Deletion</span>Pending Review</div>
            @endif
        </div>
    </div>
</div>

@if(!$user->is_active)
<div class="alert alert-error">
    <i class="ti ti-user-pause"></i>
    <div class="alert-text"><strong>Account Deactivated</strong>This account is currently deactivated and cannot log in until it is restored or the user reactivates it themselves on next login attempt.</div>
</div>
@endif

@if($pendingDeletion)
<div class="alert" style="background:#fff8f0; border:1.5px solid #ef9f27;">
    <i class="ti ti-clock" style="color:#ef9f27;"></i>
    <div class="alert-text" style="color:#7a5500;">
        <strong>Pending Deletion Request</strong>
        Submitted {{ $pendingDeletion->created_at->diffForHumans() }}.
        @if($pendingDeletion->reason)
        Reason: "{{ $pendingDeletion->reason }}"
        @endif
        <br>
        <a href="{{ route('notifications.index') }}" style="color:#ef9f27; font-weight:600;">Review this request →</a>
    </div>
</div>
@endif

<div class="two-col">

    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-info-circle"></i> Account Information</div></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-row"><span>Full Name: </span><strong>{{ $user->name }}</strong></div>
                <div class="detail-row"><span>Email: </span><strong>{{ $user->email }}</strong></div>
                <div class="detail-row"><span>Phone: </span><strong>{{ $user->phone ?? '—' }}</strong></div>
                <div class="detail-row"><span>Role: </span><strong>{{ $user->role === 'superadmin' ? 'Super Admin' : ucfirst($user->role) }}</strong></div>
                <div class="detail-row"><span>Campus: </span><strong>{{ $user->campus->name ?? '—' }}</strong></div>
                <div class="detail-row"><span>Department: </span><strong>{{ $user->department->department_name ?? '—' }}</strong></div>
                <div class="detail-row"><span>Joined: </span><strong>{{ $user->created_at->format('M d, Y') }}</strong></div>
                <div class="detail-row"><span>Last Updated: </span><strong>{{ $user->updated_at->format('M d, Y h:i A') }}</strong></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-shield-check"></i> Account Status</div></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-row">
                    <span>Active Status</span>
                    <strong>
                        @if($user->is_active)
                            <span class="chip-badge chip-status-active"><i class="ti ti-circle-check" style="font-size:10px"></i> Active</span>
                        @else
                            <span class="chip-badge chip-status-inactive"><i class="ti ti-user-pause" style="font-size:10px"></i> Deactivated</span>
                        @endif
                    </strong>
                </div>
                <div class="detail-row">
                    <span>Deletion Request</span>
                    <strong>
                        @if($pendingDeletion)
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;"><i class="ti ti-clock" style="font-size:10px"></i> Pending</span>
                        @else
                            <span class="chip-badge chip-equipment-zero">None</span>
                        @endif
                    </strong>
                </div>
            </div>

            @if($deletionHistory->count() > 0)
            <div style="margin-top:1.25rem;">
                <div style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.6rem;">Deletion Request History</div>
                @foreach($deletionHistory as $dh)
                <div class="activity-item">
                    <div class="activity-dot {{ $dh->status === 'pending' ? 'dot-orange' : ($dh->status === 'approved' ? 'dot-red' : 'dot-green') }}"></div>
                    <div>
                        <div class="activity-title">{{ ucfirst($dh->status) }}{{ $dh->reason ? ' — ' . $dh->reason : '' }}</div>
                        <div class="activity-meta">
                            <span>{{ $dh->created_at->format('M d, Y h:i A') }}</span>
                            @if($dh->reviewer)
                            <span>by {{ $dh->reviewer->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>

@endsection