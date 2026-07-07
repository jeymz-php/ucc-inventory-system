<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'open');

        $conversations = \App\Models\Conversation::with(['user', 'lastMessage'])
            ->where('type', 'admin')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'open'     => \App\Models\Conversation::where('type','admin')->where('status','open')->count(),
            'resolved' => \App\Models\Conversation::where('type','admin')->where('status','resolved')->count(),
            'unread'   => \App\Models\Conversation::where('type','admin')
                ->whereHas('messages', fn($q) => $q->where('sender_type','user')->where('is_read',false))
                ->count(),
        ];

        // All users admin can message (both IMS and CS)
        $allUsers = \App\Models\User::whereIn('source', ['ims','cs'])
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereNotIn('role', ['superadmin'])
            ->orderBy('name')
            ->get(['id','name','email','source','role']);

        return view('pages.messages', compact('conversations', 'stats', 'status', 'allUsers'));
    }

    // Admin initiates a new conversation with any user
    public function adminStart(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:200',
            'body'    => 'required|string|max:2000',
        ]);

        $conversation = \App\Models\Conversation::create([
            'ticket_no' => \App\Models\Conversation::generateTicketNo(),
            'user_id'   => $request->user_id,
            'type'      => 'admin',
            'status'    => 'open',
            'subject'   => $request->subject,
        ]);

        \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => auth()->id(),
            'sender_type'     => 'admin',
            'body'            => $request->body,
            'is_read'         => false,
        ]);

        return redirect()->route('messages.show', $conversation)
            ->with('success', "Conversation {$conversation->ticket_no} started.");
    }

    public function show(Conversation $conversation)
    {
        // Mark user messages as read when admin opens thread
        $conversation->messages()
            ->where('sender_type', 'user')
            ->update(['is_read' => true]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('pages.message_show', compact('conversation', 'messages'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isAdmin && $conversation->user_id !== $user->id) abort(403);

        if ($conversation->status !== 'open') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Conversation is closed.'], 403);
            }
            return back()->with('error', 'This conversation is closed.');
        }

        $message = \App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'sender_type'     => $isAdmin ? 'admin' : 'user',
            'body'            => $request->body,
            'is_read'         => false,
        ]);

        $message->load('sender');

        if ($request->expectsJson()) {
            return response()->json([
                'id'          => $message->id,
                'body'        => $message->body,
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender->name ?? 'You',
                'time'        => $message->created_at->format('M d, Y h:i A'),
            ]);
        }

        return back();
    }

    public function close(Conversation $conversation)
    {
        $conversation->update(['status' => 'resolved']);
        return back()->with('success', 'Conversation marked as resolved.');
    }

    public function reopen(Conversation $conversation)
    {
        $conversation->update(['status' => 'open']);
        return back()->with('success', 'Conversation reopened.');
    }

    public function poll(Conversation $conversation, Request $request)
    {
        $user    = auth()->user();
        $isAdmin = in_array($user->role, ['admin', 'superadmin']);

        if (!$isAdmin && $conversation->user_id !== $user->id) abort(403);

        $sinceId  = $request->get('since_id', 0);
        $messages = $conversation->messages()
            ->with('sender')
            ->where('id', '>', $sinceId)
            ->oldest()
            ->get()
            ->map(fn($m) => [
                'id'          => $m->id,
                'body'        => $m->body,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender->name ?? ($m->sender_type === 'bot' ? 'UCC-CS Bot' : 'User'),
                'time'        => $m->created_at->format('M d, Y h:i A'),
                'is_own'      => $m->sender_id === $user->id,
            ]);

        // Mark incoming messages as read
        $incomingType = $isAdmin ? 'user' : 'admin';
        $conversation->messages()
            ->where('sender_type', $incomingType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages,
            'last_id'  => $messages->last()['id'] ?? $sinceId,
        ]);
    }

    // Global poll for bell badge count
    public function pollAll()
    {
        $count = Conversation::where('type', 'admin')
            ->where('status', 'open')
            ->whereHas('messages', fn($q) => $q->where('sender_type', 'user')->where('is_read', false))
            ->count();

        return response()->json(['unread_conversations' => $count]);
    }
}