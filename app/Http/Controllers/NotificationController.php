<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\Consumable;
use App\Models\ConsumableRequest;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $type   = $request->get('type', 'all');

        // Fetch deletion requests
        $deletions = \App\Models\AccountDeletionRequest::with(['user', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'type'        => 'deletion',
                'title'       => $r->user->name ?? 'Deleted User',
                'subtitle'    => $r->user->email ?? '—',
                'detail'      => $r->reason,
                'status'      => $r->status,
                'reviewed_by' => $r->reviewer->name ?? '—',
                'created_at'  => $r->created_at,
                'raw'         => $r,
            ]);

        // Fetch consumable requests
        $consumables = \App\Models\ConsumableRequest::with(['requester', 'reviewer', 'items'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'type'        => 'consumable',
                'title'       => $r->reference_no,
                'subtitle'    => $r->recipient_name . ' — ' . $r->department,
                'detail'      => $r->items->count() . ' item(s)',
                'status'      => $r->status,
                'reviewed_by' => $r->reviewer->name ?? '—',
                'created_at'  => $r->created_at,
                'raw'         => $r,
            ]);

        // Out-of-stock consumables — always "pending" in the sense that they need
        // attention; they have no approve/reject workflow, they just resolve
        // automatically once restocked, so they only show under pending/all.
        $stockOuts = collect();
        if ($status === 'pending' || $status === 'all') {
            $stockOuts = Consumable::with('category')
                ->where('current_stock', '<=', 0)
                ->get()
                ->map(fn($c) => [
                    'id'          => $c->id,
                    'type'        => 'stock',
                    'title'       => $c->item_name,
                    'subtitle'    => $c->category->name ?? 'Uncategorized',
                    'detail'      => 'Current stock: 0 ' . $c->unit,
                    'status'      => 'pending',
                    'reviewed_by' => '—',
                    'created_at'  => $c->updated_at,
                    'raw'         => $c,
                ]);
        }

        // Merge and sort by latest first
        if ($type === 'deletion') {
            $merged = $deletions;
        } elseif ($type === 'consumable') {
            $merged = $consumables;
        } elseif ($type === 'stock') {
            $merged = $stockOuts;
        } else {
            $merged = $deletions->concat($consumables)->concat($stockOuts);
        }

        $merged = $merged->sortByDesc('created_at')->values();

        // Manual pagination
        $perPage     = 15;
        $currentPage = $request->get('page', 1);
        $paginated   = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $merged->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'pending'  => $deletions->where('status', 'pending')->count()
                        + $consumables->where('status', 'pending')->count()
                        + Consumable::where('current_stock', '<=', 0)->count(),
            'approved' => $deletions->where('status', 'approved')->count()
                        + $consumables->whereIn('status', ['approved', 'partial'])->count(),
            'rejected' => $deletions->where('status', 'rejected')->count()
                        + $consumables->where('status', 'rejected')->count(),
        ];

        return view('pages.notifications', compact('paginated', 'stats', 'status', 'type'));
    }

    public function poll()
    {
        $deletionRequests = AccountDeletionRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(fn($r) => [
                'type'       => 'deletion',
                'id'         => $r->id,
                'title'      => $r->user->name ?? 'Unknown User',
                'subtitle'   => $r->user->email ?? '',
                'reason'     => $r->reason,
                'created_at' => $r->created_at->diffForHumans(),
            ]);

        $consumableRequests = ConsumableRequest::with('requester')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(fn($r) => [
                'type'       => 'consumable',
                'id'         => $r->id,
                'title'      => $r->reference_no,
                'subtitle'   => $r->recipient_name . ' — ' . $r->department,
                'reason'     => null,
                'created_at' => $r->created_at->diffForHumans(),
            ]);

        // CS user tickets — open conversations with unread user messages
        $tickets = \App\Models\Conversation::with(['user', 'lastMessage'])
            ->where('type', 'admin')
            ->where('status', 'open')
            ->whereHas('messages', fn($q) =>
                $q->where('sender_type', 'user')->where('is_read', false)
            )
            ->latest()
            ->get()
            ->map(fn($c) => [
                'type'       => 'ticket',
                'id'         => $c->id,
                'title'      => $c->ticket_no,
                'subtitle'   => ($c->user->name ?? 'Unknown') . ' — ' . $c->subject,
                'source'     => $c->user->source ?? 'cs',
                'reason'     => null,
                'created_at' => $c->created_at->diffForHumans(),
            ]);

        // Out-of-stock consumables — derived live from current stock levels,
        // so an item drops off this list automatically once it's restocked
        // (regardless of whether the deduction happened via IMS or CS).
        $stockOuts = Consumable::with('category')
            ->where('current_stock', '<=', 0)
            ->latest('updated_at')
            ->get()
            ->map(fn($c) => [
                'type'       => 'stock',
                'id'         => $c->id,
                'title'      => $c->item_name,
                'subtitle'   => $c->category->name ?? 'Uncategorized',
                'reason'     => null,
                'created_at' => $c->updated_at->diffForHumans(),
            ]);

        $all = $deletionRequests
            ->concat($consumableRequests)
            ->concat($tickets)
            ->concat($stockOuts)
            ->values();

        return response()->json([
            'count'            => $all->count(),
            'deletion_count'   => $deletionRequests->count(),
            'consumable_count' => $consumableRequests->count(),
            'ticket_count'     => $tickets->count(),
            'stock_count'      => $stockOuts->count(),
            'requests'         => $all,
        ]);
    }

    public function approve(Request $request, AccountDeletionRequest $deletionRequest)
    {
        $user = $deletionRequest->user;
        $userName = $user->name;

        $deletionRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'delete', 'User',
            "Approved deletion request and deleted user account: {$userName}",
            'user', $user->id
        );

        $user->delete();

        return response()->json(['message' => "Account for {$userName} has been deleted."]);
    }

    public function reject(Request $request, AccountDeletionRequest $deletionRequest)
    {
        $deletionRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        \App\Models\ActivityLog::record(
            'reject', 'User',
            "Rejected deletion request for: {$deletionRequest->user->name}",
            'user', $deletionRequest->user_id
        );

        return response()->json(['message' => 'Deletion request rejected.']);
    }
}