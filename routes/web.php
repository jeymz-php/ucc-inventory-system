<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MultiStepRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Multi-step registration
Route::get('/register', [MultiStepRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register/send-otp',   [MultiStepRegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [MultiStepRegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::get('/register/departments', [MultiStepRegisterController::class, 'getDepartments'])->name('register.departments');
Route::post('/register',            [MultiStepRegisterController::class, 'register'])->name('register.submit');

// Forgot password
Route::get('/forgot-password',          [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password/send',    [ForgotPasswordController::class, 'sendCode'])->name('password.send');
Route::post('/forgot-password/verify',  [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::post('/forgot-password/reset',   [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// Auth (login/logout only — no built-in register or default password reset)
Auth::routes([
    'register' => false,
    'reset' => false,
]);

// Dashboard (protected)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');