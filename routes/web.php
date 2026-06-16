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

/// Dashboard — all authenticated users
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Admin + Super Admin only routes (scaffolded for later)
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/inventory',   fn() => view('pages.inventory'))->name('inventory');
    Route::get('/equipment',   fn() => view('pages.equipment'))->name('equipment');
    Route::get('/locations',   fn() => view('pages.locations'))->name('locations');
    Route::get('/categories',  fn() => view('pages.categories'))->name('categories');
    Route::get('/consumables', fn() => view('pages.consumables'))->name('consumables');
    Route::get('/history',     fn() => view('pages.history'))->name('history');
    Route::get('/condemned',   fn() => view('pages.condemned'))->name('condemned');
    Route::get('/users',       fn() => view('pages.users'))->name('users');
});