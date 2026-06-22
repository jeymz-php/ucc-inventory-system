<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
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

        $requests = \App\Models\AccountDeletionRequest::with(['user', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending'  => \App\Models\AccountDeletionRequest::where('status', 'pending')->count(),
            'approved' => \App\Models\AccountDeletionRequest::where('status', 'approved')->count(),
            'rejected' => \App\Models\AccountDeletionRequest::where('status', 'rejected')->count(),
        ];

        return view('pages.notifications', compact('requests', 'stats', 'status'));
    }

    public function poll()
    {
        $requests = AccountDeletionRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id'         => $r->id,
                    'user_name'  => $r->user->name,
                    'user_email' => $r->user->email,
                    'reason'     => $r->reason,
                    'created_at' => $r->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'count'    => $requests->count(),
            'requests' => $requests,
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