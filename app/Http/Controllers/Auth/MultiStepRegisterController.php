<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Campus;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MultiStepRegisterController extends Controller
{
    // Show register page
    public function showRegisterForm()
    {
        $campuses = Campus::where('is_active', true)->get();
        return view('auth.register', compact('campuses'));
    }

    // Step 1: Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
        ], [
            'email.unique' => 'This email is already registered. Please sign in instead.',
        ]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('email_verification_codes')->where('email', $request->email)->delete();

        DB::table('email_verification_codes')->insert([
            'email'      => $request->email,
            'code'       => $otp,
            'is_used'    => false,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($request->email)->send(new OtpMail($otp, $request->email));

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    // Step 1: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|digits:6',
        ]);

        $record = DB::table('email_verification_codes')
            ->where('email', $request->email)
            ->where('code',  $request->code)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        DB::table('email_verification_codes')
            ->where('id', $record->id)
            ->update(['is_used' => true]);

        return response()->json(['message' => 'Email verified successfully.']);
    }

    // Get all departments (no campus filter needed)
    public function getDepartments(Request $request)
    {
        $departments = Department::where('is_active', true)
            ->orderBy('department_name')
            ->get(['id', 'department_name']);

        return response()->json($departments);
    }

    // Final Step: Create Account
    public function register(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'campus_id'     => 'required|exists:campuses,id',
            'department_id' => 'required|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'campus_id'     => $request->campus_id,
            'department_id' => $request->department_id,
            'password'      => Hash::make($request->password),
            'role'          => 'user',
        ]);

        // ✅ Don't login yet — redirect to login with success message
        return response()->json([
            'message'  => 'Account created successfully.',
            'redirect' => route('login', ['registered' => true]),
        ]);
    }
}