<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MultiStepRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\SystemStatusController;

Route::middleware('auth')->group(function () {
    Route::put('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.change');
});

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

    Route::get('/users',           [UserManagementController::class, 'index'])->name('users');
    Route::post('/users',          [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}',    [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/archive', [UserManagementController::class, 'archive'])->name('users.archive');
});

// Admin login (bypasses system down)
Route::get('/admin/login',  [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout',[AdminLoginController::class, 'logout'])->name('admin.logout');

// System Status (superadmin only)
Route::middleware(['auth', 'role:superadmin'])->prefix('system')->name('system.')->group(function () {
    Route::get('/status',              [SystemStatusController::class, 'index'])->name('status');
    Route::post('/status/toggle',      [SystemStatusController::class, 'toggle'])->name('status.toggle');
    Route::patch('/logs/{log}/resolve',[SystemStatusController::class, 'resolveLog'])->name('logs.resolve');
    Route::delete('/logs/clear',       [SystemStatusController::class, 'clearLogs'])->name('logs.clear');
});