<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
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
        $tab    = $request->get('tab', 'deletions');
        $status = $request->get('status', 'pending');

        if ($tab === 'consumables') {
            $requests = ConsumableRequest::with(['requester', 'reviewer', 'items'])
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $stats = [
                'pending'  => ConsumableRequest::where('status', 'pending')->count(),
                'approved' => ConsumableRequest::whereIn('status', ['approved', 'partial'])->count(),
                'rejected' => ConsumableRequest::where('status', 'rejected')->count(),
            ];

            return view('pages.notifications', compact('requests', 'stats', 'status', 'tab'));
        }

        $requests = AccountDeletionRequest::with(['user', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending'  => AccountDeletionRequest::where('status', 'pending')->count(),
            'approved' => AccountDeletionRequest::where('status', 'approved')->count(),
            'rejected' => AccountDeletionRequest::where('status', 'rejected')->count(),
        ];

        return view('pages.notifications', compact('requests', 'stats', 'status', 'tab'));
    }

    public function poll()
    {
        $deletionRequests = AccountDeletionRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'type'       => 'deletion',
                    'id'         => $r->id,
                    'title'      => $r->user->name ?? 'Unknown User',
                    'subtitle'   => $r->user->email ?? '',
                    'reason'     => $r->reason,
                    'created_at' => $r->created_at->diffForHumans(),
                ];
            });

        $consumableRequests = ConsumableRequest::with('requester')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'type'       => 'consumable',
                    'id'         => $r->id,
                    'title'      => $r->reference_no,
                    'subtitle'   => $r->recipient_name . ' — ' . $r->department,
                    'reason'     => null,
                    'created_at' => $r->created_at->diffForHumans(),
                ];
            });

        $all = $deletionRequests->concat($consumableRequests)->values();

        return response()->json([
            'count'                => $all->count(),
            'deletion_count'       => $deletionRequests->count(),
            'consumable_count'     => $consumableRequests->count(),
            'requests'             => $all,
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