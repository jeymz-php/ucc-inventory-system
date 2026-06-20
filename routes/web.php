<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\MultiStepRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SystemStatusController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentArticleController;
use App\Http\Controllers\EquipmentStoreController;
use App\Http\Controllers\EquipmentActionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CondemnedController;
use App\Http\Controllers\CondemnedStoreController;

// ──────────────────────────────────────────────
// Landing page
// ──────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ──────────────────────────────────────────────
// Multi-step registration
// ──────────────────────────────────────────────
Route::get('/register', [MultiStepRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register/send-otp',   [MultiStepRegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp', [MultiStepRegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::get('/register/departments', [MultiStepRegisterController::class, 'getDepartments'])->name('register.departments');
Route::post('/register',            [MultiStepRegisterController::class, 'register'])->name('register.submit');

// ──────────────────────────────────────────────
// Forgot password
// ──────────────────────────────────────────────
Route::get('/forgot-password',         [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password/send',   [ForgotPasswordController::class, 'sendCode'])->name('password.send');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::post('/forgot-password/reset',  [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// ──────────────────────────────────────────────
// Built-in Auth (login/logout only)
// ──────────────────────────────────────────────
Auth::routes([
    'register' => false,
    'reset'    => false,
]);

// ──────────────────────────────────────────────
// Change password (any authenticated user)
// ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::put('/password/change', [PasswordChangeController::class, 'update'])->name('password.change');
});

// ──────────────────────────────────────────────
// Dashboard — all authenticated users
// ──────────────────────────────────────────────
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// ──────────────────────────────────────────────
// Admin + Super Admin only routes
// ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {

    Route::get('/inventory',                [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/{locationType}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/location-type', [InventoryController::class, 'storeLocationType'])->name('inventory.location-type.store');
    Route::put('/inventory/location-type/{locationType}',          [InventoryController::class, 'updateLocationType'])->name('inventory.location-type.update');
    Route::patch('/inventory/location-type/{locationType}/toggle',  [InventoryController::class, 'toggleLocationType'])->name('inventory.location-type.toggle');
    Route::delete('/inventory/location-type/{locationType}',        [InventoryController::class, 'destroyLocationType'])->name('inventory.location-type.destroy');
    Route::get('/inventory/location-type/by-campus', [InventoryController::class, 'locationTypesByCampus'])->name('inventory.location-type.by-campus');

    Route::get('/locations', [LocationsController::class, 'index'])->name('locations');
    Route::get('/locations/{location}/equipment', [InventoryController::class, 'showLocation'])->name('locations.equipment');
    Route::post('/locations',                [LocationsController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}',      [LocationsController::class, 'update'])->name('locations.update');
    Route::patch('/locations/{location}/archive', [LocationsController::class, 'archive'])->name('locations.archive');

    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment');
    Route::get('/categories',  fn() => view('pages.categories'))->name('categories');
    Route::get('/consumables', fn() => view('pages.consumables'))->name('consumables');
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/condemned', [CondemnedController::class, 'index'])->name('condemned');

    Route::get('/equipment-articles',           [EquipmentArticleController::class, 'index'])->name('equipment.articles.index');
    Route::post('/equipment-articles',          [EquipmentArticleController::class, 'store'])->name('equipment.articles.store');
    Route::put('/equipment-articles/{article}', [EquipmentArticleController::class, 'update'])->name('equipment.articles.update');
    Route::delete('/equipment-articles/{article}', [EquipmentArticleController::class, 'destroy'])->name('equipment.articles.destroy');

    Route::get('/equipment/locations-by-campus', [EquipmentStoreController::class, 'locationsByCampus'])->name('equipment.locations-by-campus');

    Route::post('/equipment/computer', [EquipmentStoreController::class, 'storeComputer'])->name('equipment.store.computer');
    Route::post('/equipment/kitchen',  [EquipmentStoreController::class, 'storeKitchen'])->name('equipment.store.kitchen');
    Route::post('/equipment/office',   [EquipmentStoreController::class, 'storeOffice'])->name('equipment.store.office');
    Route::post('/equipment/lab',      [EquipmentStoreController::class, 'storeLab'])->name('equipment.store.lab');
    Route::post('/equipment/general',  [EquipmentStoreController::class, 'storeGeneral'])->name('equipment.store.general');

    Route::get('/equipment/{type}/{id}',                [EquipmentActionController::class, 'show'])->name('equipment.show');
    Route::get('/equipment/{type}/{id}/edit',            [EquipmentActionController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{type}/{id}',                 [EquipmentActionController::class, 'update'])->name('equipment.update');
    Route::post('/equipment/{type}/{id}/condemn',        [EquipmentActionController::class, 'condemn'])->name('equipment.condemn');
    Route::get('/equipment/{type}/{id}/report',          [EquipmentActionController::class, 'report'])->name('equipment.report');
    Route::delete('/equipment/{type}/{id}',              [EquipmentActionController::class, 'destroy'])->name('equipment.destroy');
    Route::post('/equipment/{type}/{id}/restore', [EquipmentActionController::class, 'restore'])->name('equipment.restore');
    Route::post('/equipment/{type}/{id}/waste',   [EquipmentActionController::class, 'transferToWaste'])->name('equipment.waste');
    Route::post('/equipment/bulk-transfer', [EquipmentActionController::class, 'bulkTransfer'])->name('equipment.bulk-transfer');
    Route::get('/equipment/bulk-report',    [EquipmentActionController::class, 'bulkReport'])->name('equipment.report.bulk');

    Route::post('/condemned', [CondemnedStoreController::class, 'store'])->name('condemned.store');

    Route::get('/users',                  [UserManagementController::class, 'index'])->name('users');
    Route::post('/users',                 [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}',           [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',        [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/archive', [UserManagementController::class, 'archive'])->name('users.archive');

});

// ──────────────────────────────────────────────
// Admin login (bypasses system down)
// ──────────────────────────────────────────────
Route::get('/admin/login',   [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login',  [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// ──────────────────────────────────────────────
// System Status (superadmin only)
// ──────────────────────────────────────────────
Route::middleware(['auth', 'role:superadmin'])->prefix('system')->name('system.')->group(function () {
    Route::get('/status',               [SystemStatusController::class, 'index'])->name('status');
    Route::post('/status/toggle',       [SystemStatusController::class, 'toggle'])->name('status.toggle');
    Route::patch('/logs/{log}/resolve', [SystemStatusController::class, 'resolveLog'])->name('logs.resolve');
    Route::delete('/logs/clear',        [SystemStatusController::class, 'clearLogs'])->name('logs.clear');
});