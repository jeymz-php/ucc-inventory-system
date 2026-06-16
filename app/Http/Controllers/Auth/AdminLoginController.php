<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect()->route('dashboard');
        }
        return view('auth.admin_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!in_array($user->role, ['admin', 'superadmin'])) {
                Auth::logout();
                SystemLog::create([
                    'type'       => 'warning',
                    'title'      => 'Unauthorized Admin Login Attempt',
                    'message'    => "User {$user->email} (role: {$user->role}) attempted to access admin login.",
                    'url'        => request()->fullUrl(),
                    'method'     => 'POST',
                    'user_id'    => $user->id,
                    'user_role'  => $user->role,
                    'ip_address' => request()->ip(),
                ]);
                return back()->withErrors(['email' => 'Access denied. This login is for administrators only.']);
            }

            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}