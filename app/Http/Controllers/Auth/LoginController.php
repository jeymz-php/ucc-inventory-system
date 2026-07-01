<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::attempt($this->credentials($request), $request->boolean('remember'))) {
            $user = Auth::user();

            // Block CS accounts from logging into IMS
            if ($user->source === 'cs') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account was created in the Consumable Management System. Please log in there instead.',
                ])->withInput($request->only('email', 'remember'));
            }

            // Block pending accounts
            if ($user->status === 'pending') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is pending approval by an administrator. You will be notified once approved.',
                ])->withInput($request->only('email', 'remember'))
                  ->with('show_pending', true);
            }

            // Block deactivated accounts
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->put('pending_reactivation_user_id', $user->id);
                return redirect()->route('login')->with('show_reactivate_modal', true);
            }

            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        return $this->sendFailedLoginResponse($request);
    }
}