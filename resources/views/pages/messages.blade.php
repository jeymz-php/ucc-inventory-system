@extends('layouts.app')
@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')

{{-- Stats --}}
<div class="stats-grid" style="margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-ticket"></i></div>
        <div><div class="stat-value">{{ $stats['open'] }}</div><div class="stat-label">Open Tickets</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['resolved'] }}</div><div class="stat-label">Resolved</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-message-exclamation"></i></div>
        <div><div class="stat-value">{{ $stats['unread'] }}</div><div class="stat-label">Needs Reply</div></div>
    </div>
</div>

{{-- Filter + New Message --}}
<div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem;">
    <div class="card" style="flex:1; margin:0;">
        <div class="card-body" style="padding:0.85rem 1.25rem;">
            <div class="filter-pills">
                <a href="{{ route('messages.index', ['status'=>'open']) }}"
                   class="filter-pill {{ $status === 'open' ? 'active' : '' }}" style="text-decoration:none;">
                    Open
                </a>
                <a href="{{ route('messages.index', ['status'=>'resolved']) }}"
                   class="filter-pill {{ $status === 'resolved' ? 'active' : '' }}" style="text-decoration:none;">
                    Resolved
                </a>
                <a href="{{ route('messages.index', ['status'=>'all']) }}"
                   class="filter-pill {{ $status === 'all' ? 'active' : '' }}" style="text-decoration:none;">
                    All
                </a>
            </div>
        </div>
    </div>
    <button class="modal-btn-primary" style="width:auto; margin:0; padding:10px 20px; white-space:nowrap; flex-shrink:0;"
            onclick="document.getElementById('admin-message-modal').classList.add('open');">
        <i class="ti ti-message-plus"></i> New Message
    </button>
</div>

{{-- Conversations Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-messages"></i> Conversations ({{ $conversations->total() }})</div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ticket No.</th>
                    <th>User</th>
                    <th>Source</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Last Message</th>
                    <th>Opened</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conv)
                @php
                    $unread = $conv->messages()
                        ->where('sender_type', 'user')
                        ->where('is_read', false)
                        ->count();
                    $userSource = $conv->user->source ?? 'ims';
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:7px;">
                            @if($unread > 0)
                            <span style="width:9px; height:9px; border-radius:50%;
                                         background:var(--red); flex-shrink:0;"
                                  title="{{ $unread }} unread message(s)"></span>
                            @endif
                            <span style="font-family:monospace; font-size:12px; font-weight:700;
                                         color:var(--green-dark);">{{ $conv->ticket_no }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $conv->user->name ?? '—' }}</div>
                        <div class="cell-secondary">{{ $conv->user->email ?? '' }}</div>
                    </td>
                    <td>
                        @if($userSource === 'cs')
                            <span class="chip-badge" style="background:#eff6ff; color:#1a56db; gap:4px;">
                                <i class="ti ti-package" style="font-size:10px;"></i> CS
                            </span>
                        @else
                            <span class="chip-badge" style="background:#f0faf4; color:#1a6b3a; gap:4px;">
                                <i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS
                            </span>
                        @endif
                    </td>
                    <td style="font-size:13px; max-width:180px;">{{ $conv->subject }}</td>
                    <td>
                        @if($conv->status === 'open')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">
                                <i class="ti ti-clock" style="font-size:10px;"></i> Open
                            </span>
                        @else
                            <span class="chip-badge chip-status-active">
                                <i class="ti ti-circle-check" style="font-size:10px;"></i> Resolved
                            </span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--text-muted); max-width:200px;">
                        @if($conv->lastMessage)
                            <span style="font-size:10px; font-weight:600; text-transform:uppercase;
                                         color:{{ $conv->lastMessage->sender_type === 'admin' ? 'var(--green-dark)' : '#888' }};">
                                {{ $conv->lastMessage->sender_type === 'admin' ? 'You' : ($conv->user->name ?? 'User') }}:
                            </span>
                            {{ Str::limit($conv->lastMessage->body, 40) }}
                        @else
                            <span style="color:#ccc;">No messages</span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">
                        {{ $conv->created_at->format('M d, Y') }}<br>
                        <span style="font-size:10.5px;">{{ $conv->created_at->format('h:i A') }}</span>
                    </td>
                    <td>
                        <a href="{{ route('messages.show', $conv) }}"
                           class="table-icon-btn view" title="Open Conversation"
                           style="{{ $unread > 0 ? 'background:#fff5f5; color:var(--red);' : '' }}">
                            <i class="ti ti-message-circle"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ti ti-messages-off"></i>
                            <p>No conversations found. Click <strong>New Message</strong> to start one.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($conversations->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $conversations->firstItem() }} to {{ $conversations->lastItem() }}
            of {{ $conversations->total() }} results
        </div>
        {{ $conversations->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- NEW MESSAGE MODAL (Admin → IMS or CS user) --}}
<div class="modal-overlay" id="admin-message-modal">
    <div class="modal-box-sm" style="max-width:520px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-message-plus"></i> Start New Conversation</div>
            <button class="modal-close" onclick="document.getElementById('admin-message-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:1.25rem; line-height:1.5;">
            You can message any IMS or CS user. They will receive a notification in their respective system.
        </p>
        <form method="POST" action="{{ route('messages.admin-start') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Send To *</div>
                <select name="user_id" class="modal-input" required id="admin-msg-user-select">
                    <option value="">-- Select User --</option>
                    @if($allUsers->where('source','ims')->count())
                    <optgroup label="── IMS Users (Inventory System) ──">
                        @foreach($allUsers->where('source','ims') as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                    @if($allUsers->where('source','cs')->count())
                    <optgroup label="── CS Users (Consumable System) ──">
                        @foreach($allUsers->where('source','cs') as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
                        @endforeach
                    </optgroup>
                    @endif
                </select>
                <div class="modal-hint" style="color:#888;">CS users will see this message in the Consumable System.</div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Subject *</div>
                <input type="text" name="subject" class="modal-input" required
                       placeholder="e.g. Regarding your consumable request">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Message *</div>
                <textarea name="body" class="modal-input" rows="4" required
                          style="padding-top:10px; resize:vertical;"
                          placeholder="Type your message here..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-send"></i> Send Message
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush