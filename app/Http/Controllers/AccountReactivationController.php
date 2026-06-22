<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AccountReactivationController extends Controller
{
    public function reactivate(Request $request)
    {
        $userId = $request->session()->get('pending_reactivation_user_id');

        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please log in again.']);
        }

        $user = User::findOrFail($userId);
        $user->update(['is_active' => true]);

        ActivityLog::record(
            'activate', 'User',
            "User self-reactivated their account: {$user->email}",
            'user', $user->id
        );

        $request->session()->forget('pending_reactivation_user_id');

        auth()->login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Your account has been reactivated. Welcome back!');
    }

    public function cancel(Request $request)
    {
        $request->session()->forget('pending_reactivation_user_id');
        return redirect()->route('login');
    }
}