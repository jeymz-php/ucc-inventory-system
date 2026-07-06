@extends('layouts.app')
@section('title', $conversation->ticket_no)
@section('page-title', 'Messages')

{{-- Make main-content fill viewport height without outer scroll --}}
@push('styles')
<style>
    body { overflow: hidden; }
    .main-content {
        padding: 0 !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--topbar-h));
        overflow: hidden;
    }
</style>
@endpush

@section('content')

{{-- TOP BAR: back link + ticket info + action button --}}
<div style="padding:0.85rem 1.5rem; background:#fff; border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;
            gap:1rem; flex-wrap:wrap; flex-shrink:0;">

    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; min-width:0;">
        <a href="{{ route('messages.index') }}"
           style="display:inline-flex; align-items:center; gap:5px; font-size:13px;
                  color:var(--text-secondary); text-decoration:none; white-space:nowrap;">
            <i class="ti ti-arrow-left"></i> Back
        </a>

        <div style="display:flex; flex-direction:column; min-width:0;">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span style="font-family:monospace; font-size:14px; font-weight:700;
                             color:var(--green-dark); white-space:nowrap;">
                    {{ $conversation->ticket_no }}
                </span>
                @if($conversation->status === 'open')
                    <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">
                        <i class="ti ti-clock" style="font-size:10px;"></i> Open
                    </span>
                @else
                    <span class="chip-badge chip-status-active">
                        <i class="ti ti-circle-check" style="font-size:10px;"></i> Resolved
                    </span>
                @endif
                <span class="chip-badge" style="background:#eff6ff; color:#1a56db;">
                    <i class="ti ti-package" style="font-size:10px;"></i> CS User
                </span>
            </div>
            <div style="font-size:13px; font-weight:600; color:var(--text-primary);
                        margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:400px;">
                {{ $conversation->subject }}
            </div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:1px;">
                From <strong>{{ $conversation->user->name ?? '—' }}</strong>
                &bull; {{ $conversation->created_at->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>

    <div style="flex-shrink:0;">
        @if($conversation->status === 'open')
        <form method="POST" action="{{ route('messages.close', $conversation) }}" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit"
                    style="display:flex; align-items:center; gap:6px; padding:8px 16px;
                           border-radius:8px; border:1.5px solid var(--green-dark);
                           background:#fff; color:var(--green-dark); font-size:13px;
                           font-weight:600; cursor:pointer; font-family:inherit;"
                    onclick="return confirm('Mark as resolved?')">
                <i class="ti ti-circle-check"></i> Mark Resolved
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('messages.reopen', $conversation) }}" style="margin:0;">
            @csrf @method('PATCH')
            <button type="submit"
                    style="display:flex; align-items:center; gap:6px; padding:8px 16px;
                           border-radius:8px; border:1.5px solid var(--orange);
                           background:#fff; color:var(--orange); font-size:13px;
                           font-weight:600; cursor:pointer; font-family:inherit;">
                <i class="ti ti-refresh"></i> Reopen
            </button>
        </form>
        @endif
    </div>
</div>

{{-- MESSAGES THREAD — fills remaining space, scrolls independently --}}
<div style="flex:1; overflow-y:auto; padding:1.25rem;
            display:flex; flex-direction:column; gap:1rem;
            background:#f9fbf9;" id="messages-thread">

    @foreach($messages as $msg)
    @php
        $isAdmin = $msg->sender_type === 'admin';
        $isBot   = $msg->sender_type === 'bot';
    @endphp

    <div style="display:flex; flex-direction:column;
                align-items:{{ $isAdmin ? 'flex-end' : 'flex-start' }};">

        <div style="font-size:11px; color:var(--text-muted); margin-bottom:3px;
                    display:flex; align-items:center; gap:5px;">
            @if($isBot)
                <i class="ti ti-robot" style="color:var(--green-dark);"></i>
                <span>UCC-CS Bot</span>
            @elseif($isAdmin)
                <i class="ti ti-shield-check" style="color:var(--green-dark);"></i>
                <span style="font-weight:600; color:var(--green-dark);">
                    {{ $msg->sender->name ?? 'Admin' }} (You)
                </span>
            @else
                <i class="ti ti-user" style="color:#3b82f6;"></i>
                <span>{{ $msg->sender->name ?? $conversation->user->name ?? 'User' }}</span>
            @endif
            <span>&bull;</span>
            <span>{{ $msg->created_at->format('M d, Y h:i A') }}</span>
        </div>

        <div style="max-width:72%; padding:10px 14px;
                    border-radius:{{ $isAdmin ? '14px 14px 4px 14px' : '14px 14px 14px 4px' }};
                    background:{{ $isAdmin ? 'var(--green-dark)' : ($isBot ? '#fff' : '#eff6ff') }};
                    color:{{ $isAdmin ? '#fff' : 'var(--text-primary)' }};
                    border:{{ $isAdmin ? 'none' : '1px solid var(--border)' }};
                    font-size:13.5px; line-height:1.65;
                    white-space:pre-wrap; word-break:break-word;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);">{{ $msg->body }}</div>

    </div>
    @endforeach

    @if($messages->isEmpty())
    <div class="empty-state">
        <i class="ti ti-message-off"></i>
        <p>No messages yet.</p>
    </div>
    @endif

</div>

{{-- REPLY BOX — fixed at bottom --}}
@if($conversation->status === 'open')
<div style="flex-shrink:0; padding:0.85rem 1.25rem;
            background:#fff; border-top:1px solid var(--border);">
    <form method="POST" action="{{ route('messages.reply', $conversation) }}" id="reply-form">
        @csrf
        <div style="display:flex; gap:10px; align-items:flex-end;">
            <textarea name="body" id="reply-body" class="modal-input" rows="2" required
                      style="flex:1; padding-top:10px; resize:none; font-size:13.5px;
                             border-radius:10px; min-height:44px; max-height:120px;
                             overflow-y:auto;"
                      placeholder="Type your reply and press Enter to send..."></textarea>
            {{-- No send button for IMS — Enter key sends --}}
        </div>
        <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">
            <i class="ti ti-corner-down-left" style="font-size:11px;"></i>
            Press <strong>Enter</strong> to send &nbsp;&bull;&nbsp;
            <strong>Shift+Enter</strong> for a new line
        </div>
    </form>
</div>
@else
<div style="flex-shrink:0; padding:1rem 1.25rem; background:#fafafa;
            border-top:1px solid var(--border); text-align:center;
            font-size:13px; color:var(--text-muted);">
    <i class="ti ti-lock" style="margin-right:6px;"></i>
    Conversation resolved — reopen to send replies.
</div>
@endif

@endsection

@push('scripts')
<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const REPLY_URL  = '{{ route("messages.reply", $conversation) }}';
const POLL_URL   = '{{ route("messages.poll", $conversation) }}';
const MY_ID      = {{ auth()->id() }};
const IS_ADMIN   = true;

let lastId = {{ $messages->last()?->id ?? 0 }};

// ── Scroll to bottom ──
function scrollBottom(smooth) {
    const t = document.getElementById('messages-thread');
    if (!t) return;
    t.scrollTo({ top: t.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

// ── Render a single message bubble ──
function renderBubble(msg) {
    const isOwn   = msg.is_own;
    const isBot   = msg.sender_type === 'bot';
    const isAdmin = msg.sender_type === 'admin';

    const wrap = document.createElement('div');
    wrap.style.cssText = `display:flex; flex-direction:column; align-items:${isOwn ? 'flex-end' : 'flex-start'};`;
    wrap.dataset.msgId = msg.id;

    const icon  = isBot    ? 'ti-robot'        : (isAdmin ? 'ti-shield-check' : 'ti-user');
    const color = isBot    ? 'var(--green-dark)': (isAdmin ? 'var(--green-dark)' : '#3b82f6');
    const name  = isOwn   ? `${msg.sender_name} (You)` : msg.sender_name;

    wrap.innerHTML = `
        <div style="font-size:11px; color:var(--text-muted); margin-bottom:3px;
                    display:flex; align-items:center; gap:5px;">
            <i class="ti ${icon}" style="color:${color};"></i>
            <span style="${isOwn ? 'font-weight:600;color:var(--green-dark);' : ''}">${name}</span>
            <span>&bull;</span>
            <span>${msg.time}</span>
        </div>
        <div style="max-width:72%; padding:10px 14px;
                    border-radius:${isOwn ? '14px 14px 4px 14px' : '14px 14px 14px 4px'};
                    background:${isOwn ? 'var(--green-dark)' : (isBot ? '#fff' : '#eff6ff')};
                    color:${isOwn ? '#fff' : 'var(--text-primary)'};
                    border:${isOwn ? 'none' : '1px solid var(--border)'};
                    font-size:13.5px; line-height:1.65;
                    white-space:pre-wrap; word-break:break-word;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06);">${escHtml(msg.body)}</div>
    `;
    return wrap;
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── AJAX send ──
async function sendReply() {
    const ta  = document.getElementById('reply-body');
    const val = ta.value.trim();
    if (!val) return;

    // Optimistic: append immediately
    const optimistic = {
        id: Date.now(), body: val, sender_type: 'admin',
        sender_name: '{{ addslashes(auth()->user()->name) }}',
        time: new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit',hour12:true}),
        is_own: true,
    };
    const thread = document.getElementById('messages-thread');
    thread.appendChild(renderBubble(optimistic));
    ta.value = '';
    ta.style.height = 'auto';
    scrollBottom(true);

    try {
        const res  = await fetch(REPLY_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body:    JSON.stringify({ body: val }),
        });
        const data = await res.json();
        if (data.id) lastId = Math.max(lastId, data.id);
    } catch(e) {
        console.error('Send failed', e);
    }
}

// ── Polling for new messages ──
async function pollMessages() {
    try {
        const res  = await fetch(`${POLL_URL}?since_id=${lastId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        const thread = document.getElementById('messages-thread');
        data.messages.forEach(msg => {
            // Skip if already rendered (own optimistic message)
            if (msg.is_own) return;
            if (document.querySelector(`[data-msg-id="${msg.id}"]`)) return;
            thread.appendChild(renderBubble(msg));
            lastId = Math.max(lastId, msg.id);
            scrollBottom(true);
        });

        if (data.last_id > lastId) lastId = data.last_id;
    } catch(e) {}
}

// ── Init ──
document.addEventListener('DOMContentLoaded', function() {
    scrollBottom(false);

    const ta = document.getElementById('reply-body');
    if (ta) {
        ta.focus();
        ta.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendReply();
            }
        });
        ta.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }

    // Poll every 2 seconds
    setInterval(pollMessages, 2000);
});
</script>
@endpush