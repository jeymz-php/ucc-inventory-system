<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pendingRequest = AccountDeletionRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        return view('pages.account_settings', compact('pendingRequest'));
    }

    public function deactivate(Request $request)
    {
        $request->validate(['confirm' => 'required|accepted']);

        $user = auth()->user();

        ActivityLog::record(
            'deactivate', 'User',
            "User self-deactivated their account: {$user->email}",
            'user', $user->id
        );

        $user->update(['is_active' => false]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deactivated. You can reactivate it anytime by logging in again.');
    }

    public function requestDeletion(Request $request)
    {
        $request->validate([
            'confirmation_text' => 'required|string',
            'reason'            => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $expected = 'Delete ' . $user->name;

        if ($request->confirmation_text !== $expected) {
            return back()->with('error', 'Confirmation text did not match. Deletion request was not submitted.');
        }

        $existing = AccountDeletionRequest::where('user_id', $user->id)->where('status', 'pending')->first();
        if ($existing) {
            return back()->with('error', 'You already have a pending deletion request.');
        }

        AccountDeletionRequest::create([
            'user_id' => $user->id,
            'reason'  => $request->reason,
        ]);

        ActivityLog::record(
            'request_delete', 'User',
            "User requested account deletion: {$user->email}" . ($request->reason ? " — Reason: {$request->reason}" : ''),
            'user', $user->id
        );

        return back()->with('success', 'Your account deletion request has been submitted and is pending review by an administrator.');
    }
}