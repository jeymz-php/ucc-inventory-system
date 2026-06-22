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

        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                // Don't fully log out yet — keep them authenticated only long enough to confirm reactivation
                $request->session()->put('pending_reactivation_user_id', $user->id);
                Auth::logout();

                return redirect()->route('login')->with('show_reactivate_modal', true);
            }

            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        return $this->sendFailedLoginResponse($request);
    }
}