@extends('layouts.app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')

<div class="card" style="margin-bottom:1.25rem; max-width:680px;">
    <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> My Account</div></div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-row"><span>Name: </span><strong>{{ auth()->user()->name }}</strong></div>
            <div class="detail-row"><span>Email: </span><strong>{{ auth()->user()->email }}</strong></div>
            <div class="detail-row"><span>Role: </span><strong>{{ ucfirst(auth()->user()->role) }}</strong></div>
            <div class="detail-row"><span>Member Since: </span><strong>{{ auth()->user()->created_at->format('M d, Y') }}</strong></div>
        </div>
    </div>
</div>

@if($pendingRequest)
<div class="card" style="margin-bottom:1.25rem; max-width:680px; border-color:#ef9f27;">
    <div class="card-body" style="display:flex; align-items:flex-start; gap:12px; background:#fff8f0;">
        <i class="ti ti-clock" style="color:#ef9f27; font-size:22px; margin-top:2px;"></i>
        <div>
            <div style="font-weight:700; color:#b87800; font-size:14px;">Deletion Request Pending</div>
            <p style="font-size:13px; color:#7a5500; margin-top:4px; line-height:1.5;">
                You requested account deletion on {{ $pendingRequest->created_at->format('M d, Y h:i A') }}.
                An administrator will review this request. Your account remains active until then.
            </p>
        </div>
    </div>
</div>
@endif

<div class="card" style="max-width:680px; border-color:#fdd;">
    <div class="card-header"><div class="card-title" style="color:var(--red);"><i class="ti ti-alert-triangle"></i> Danger Zone</div></div>
    <div class="card-body" style="display:flex; flex-direction:column; gap:1rem;">

        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem; border:1px solid var(--border); border-radius:10px;">
            <div>
                <div style="font-weight:600; font-size:13.5px;">Deactivate My Account</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Temporarily disable your account. You can reactivate it anytime by logging in again.</div>
            </div>
            <button class="btn-table-action" style="background:#fff8f0; color:#ef9f27; flex-shrink:0;" onclick="document.getElementById('deactivate-modal').classList.add('open');">
                <i class="ti ti-user-pause"></i> Deactivate
            </button>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem; border:1px solid var(--border); border-radius:10px;">
            <div>
                <div style="font-weight:600; font-size:13.5px;">Request Account Deletion</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Permanently delete your account. This requires administrator approval.</div>
            </div>
            @if(!$pendingRequest)
            <button class="btn-table-action" style="background:#fff5f5; color:var(--red); flex-shrink:0;" onclick="document.getElementById('delete-request-modal').classList.add('open');">
                <i class="ti ti-trash"></i> Request Deletion
            </button>
            @else
            <button class="btn-table-action" style="background:#f5f5f5; color:#999; flex-shrink:0; cursor:not-allowed;" disabled>
                <i class="ti ti-clock"></i> Pending
            </button>
            @endif
        </div>

    </div>
</div>

{{-- DEACTIVATE MODAL --}}
<div class="modal-overlay" id="deactivate-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-user-pause" style="color:#ef9f27"></i> Deactivate Account</div>
            <button class="modal-close" onclick="document.getElementById('deactivate-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            Your account will be deactivated and you will be logged out immediately. You can reactivate it anytime by simply logging in again.
        </p>
        <form method="POST" action="{{ route('account.deactivate') }}">
            @csrf
            <div class="modal-check" style="margin-bottom:1rem; display:flex; align-items:flex-start; gap:8px;">
                <input type="checkbox" name="confirm" id="confirm-deactivate" required style="margin-top:3px;">
                <label for="confirm-deactivate" style="font-size:13px; color:#444;">I understand and want to deactivate my account.</label>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;"><i class="ti ti-user-pause"></i> Confirm Deactivation</button>
        </form>
    </div>
</div>

{{-- DELETE REQUEST MODAL --}}
<div class="modal-overlay" id="delete-request-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-trash" style="color:var(--red)"></i> Request Account Deletion</div>
            <button class="modal-close" onclick="document.getElementById('delete-request-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            This sends a deletion request to an administrator for approval. To confirm, type exactly:
        </p>
        <div style="background:#fff5f5; border:1.5px solid #e24b4a; border-radius:8px; padding:10px 14px; margin-bottom:1rem; font-size:13px; font-weight:600; color:#c0392b; text-align:center;">
            Delete {{ auth()->user()->name }}
        </div>
        <form method="POST" action="{{ route('account.request-deletion') }}">
            @csrf
            <div class="modal-form-group">
                <input type="text" name="confirmation_text" class="modal-input" placeholder="Type the confirmation text above" required autocomplete="off">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Reason <span style="text-transform:none; font-weight:400;">(optional)</span></div>
                <textarea name="reason" class="modal-input" rows="3" style="padding-top:10px; resize:none;" placeholder="Why do you want to delete your account?"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:var(--red);"><i class="ti ti-send"></i> Submit Deletion Request</button>
        </form>
    </div>
</div>

@endsection