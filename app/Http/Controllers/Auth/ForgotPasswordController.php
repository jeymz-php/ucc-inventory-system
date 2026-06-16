<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot_password');
    }

    public function sendCode(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'No account found with this email address.',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        DB::table('password_reset_codes')->insert([
            'email'      => $request->email,
            'code'       => $otp,
            'is_used'    => false,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($request->email)->send(new ForgotPasswordMail($otp, $request->email));

        return response()->json(['message' => 'Reset code sent successfully.']);
    }

    public function verifyCode(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email',  $request->email)
            ->where('code',   $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        return response()->json(['message' => 'Code verified.']);
    }

    public function resetPassword(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'code'     => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email',  $request->email)
            ->where('code',   $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_codes')->where('id', $record->id)->update(['is_used' => true]);

        return response()->json([
            'message'  => 'Password reset successfully.',
            'redirect' => route('login', ['reset' => true]),
        ]);
    }
}