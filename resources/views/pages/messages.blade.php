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

{{-- Filter --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
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
                    <td style="font-size:13px; max-width:200px;">{{ $conv->subject }}</td>
                    <td>
                        @if($conv->status === 'open')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">
                                <i class="ti ti-clock" style="font-size:10px;"></i> Open
                            </span>
                        @elseif($conv->status === 'resolved')
                            <span class="chip-badge chip-status-active">
                                <i class="ti ti-circle-check" style="font-size:10px;"></i> Resolved
                            </span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--text-muted); max-width:220px;">
                        @if($conv->lastMessage)
                            <span style="font-size:10px; font-weight:600; text-transform:uppercase;
                                         color:{{ $conv->lastMessage->sender_type === 'admin' ? 'var(--green-dark)' : '#888' }};">
                                {{ $conv->lastMessage->sender_type === 'admin' ? 'You' : ($conv->user->name ?? 'User') }}:
                            </span>
                            {{ Str::limit($conv->lastMessage->body, 45) }}
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
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-messages-off"></i>
                            <p>No conversations found.</p>
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

@endsection