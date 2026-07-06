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
use App\Http\Controllers\AccountReactivationController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\ConsumablesController;
use App\Http\Controllers\ConsumableRequestController;
use App\Http\Controllers\BackupRestoreController;
use App\Http\Controllers\ConsumableReportController;
use App\Http\Controllers\MyEquipmentController;
use App\Http\Controllers\SystemUpdateController;
use App\Http\Controllers\ConversationController;

// ──────────────────────────────────────────────
// Landing page
// ──────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ──────────────────────────────────────────────
// Multi-step registration
// ──────────────────────────────────────────────
Route::get('/register',              [MultiStepRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register/send-otp',    [MultiStepRegisterController::class, 'sendOtp'])->name('register.send-otp');
Route::post('/register/verify-otp',  [MultiStepRegisterController::class, 'verifyOtp'])->name('register.verify-otp');
Route::get('/register/departments',  [MultiStepRegisterController::class, 'getDepartments'])->name('register.departments');
Route::post('/register',             [MultiStepRegisterController::class, 'register'])->name('register.submit');

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
// Admin login (bypasses system down)
// ──────────────────────────────────────────────
Route::get('/admin/login',   [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login',  [AdminLoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// ──────────────────────────────────────────────
// Account reactivation (unauthenticated)
// ──────────────────────────────────────────────
Route::post('/account/reactivate',        [AccountReactivationController::class, 'reactivate'])->name('account.reactivate');
Route::post('/account/cancel-reactivate', [AccountReactivationController::class, 'cancel'])->name('account.cancel-reactivate');

// ──────────────────────────────────────────────
// Authenticated users (all roles)
// ──────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::put('/password/change',           [PasswordChangeController::class, 'update'])->name('password.change');
    Route::get('/account/settings',          [AccountSettingsController::class, 'index'])->name('account.settings');
    Route::post('/account/deactivate',       [AccountSettingsController::class, 'deactivate'])->name('account.deactivate');
    Route::post('/account/request-deletion', [AccountSettingsController::class, 'requestDeletion'])->name('account.request-deletion');

    // Consumable Reports (must be before {consumable} wildcard)
    Route::get('/consumables/reports',        [ConsumableReportController::class, 'index'])->name('consumables.reports');
    Route::get('/consumables/reports/excel',  [ConsumableReportController::class, 'exportExcel'])->name('consumables.reports.excel');
    Route::get('/consumables/reports/pdf',    [ConsumableReportController::class, 'exportPdf'])->name('consumables.reports.pdf');

    // Consumables (read + request — all users)
    Route::get('/consumables',                                        [ConsumablesController::class, 'index'])->name('consumables');
    Route::get('/consumables/{consumable}',                           [ConsumablesController::class, 'show'])->name('consumables.show');
    Route::post('/consumables/{consumable}/refill',                   [ConsumablesController::class, 'refill'])->name('consumables.refill');

    // Consumable requests (all users)
    Route::get('/consumable-requests',                                [ConsumableRequestController::class, 'index'])->name('consumable-requests');
    // Constrain consumableRequest to numeric IDs so static admin routes (e.g. blank-report) are not shadowed
    Route::get('/consumable-requests/{consumableRequest}',            [ConsumableRequestController::class, 'show'])
        ->where('consumableRequest', '[0-9]+')
        ->name('consumable-requests.show');
    Route::post('/consumable-requests',                               [ConsumableRequestController::class, 'store'])->name('consumable-requests.store');
    Route::put('/consumable-requests/{consumableRequest}',            [ConsumableRequestController::class, 'update'])->name('consumable-requests.update');
    Route::get('/consumable-requests/{consumableRequest}/report', [ConsumableRequestController::class, 'report'])->name('consumable-requests.report');

    // Temporary debug route for blank report (auth-only) to help diagnose 404s
    Route::get('/consumable-requests/blank-report-debug', [ConsumableRequestController::class, 'blankReport'])->name('consumable-requests.blank-report-debug');

    // User assigned equipment (regular users only)
    Route::get('/my-equipment',                              [MyEquipmentController::class, 'index'])->name('my-equipment');
    Route::get('/my-equipment/{type}/{id}',                  [MyEquipmentController::class, 'show'])->name('my-equipment.show');
});

// ──────────────────────────────────────────────
// Admin + Super Admin only
// ──────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {

    // Inventory
    Route::get('/inventory',                                          [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/{locationType}',                           [InventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/location-type',                           [InventoryController::class, 'storeLocationType'])->name('inventory.location-type.store');
    Route::put('/inventory/location-type/{locationType}',             [InventoryController::class, 'updateLocationType'])->name('inventory.location-type.update');
    Route::patch('/inventory/location-type/{locationType}/toggle',    [InventoryController::class, 'toggleLocationType'])->name('inventory.location-type.toggle');
    Route::delete('/inventory/location-type/{locationType}',          [InventoryController::class, 'destroyLocationType'])->name('inventory.location-type.destroy');
    Route::get('/inventory/location-type/by-campus',                  [InventoryController::class, 'locationTypesByCampus'])->name('inventory.location-type.by-campus');

    // Locations
    Route::get('/locations',                                          [LocationsController::class, 'index'])->name('locations');
    Route::get('/locations/{location}/equipment',                     [InventoryController::class, 'showLocation'])->name('locations.equipment');
    Route::post('/locations',                                         [LocationsController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}',                               [LocationsController::class, 'update'])->name('locations.update');
    Route::patch('/locations/{location}/archive',                     [LocationsController::class, 'archive'])->name('locations.archive');

    // Equipment
    Route::get('/equipment',                                          [EquipmentController::class, 'index'])->name('equipment');
    Route::get('/equipment/locations-by-campus',                      [EquipmentStoreController::class, 'locationsByCampus'])->name('equipment.locations-by-campus');
    Route::get('/equipment/bulk-report',                              [EquipmentActionController::class, 'bulkReport'])->name('equipment.report.bulk');
    Route::post('/equipment/bulk-transfer',                           [EquipmentActionController::class, 'bulkTransfer'])->name('equipment.bulk-transfer');
    Route::post('/equipment/computer',                                [EquipmentStoreController::class, 'storeComputer'])->name('equipment.store.computer');
    Route::post('/equipment/kitchen',                                 [EquipmentStoreController::class, 'storeKitchen'])->name('equipment.store.kitchen');
    Route::post('/equipment/office',                                  [EquipmentStoreController::class, 'storeOffice'])->name('equipment.store.office');
    Route::post('/equipment/lab',                                     [EquipmentStoreController::class, 'storeLab'])->name('equipment.store.lab');
    Route::post('/equipment/general',                                 [EquipmentStoreController::class, 'storeGeneral'])->name('equipment.store.general');
    Route::get('/equipment/{type}/{id}',                              [EquipmentActionController::class, 'show'])->name('equipment.show');
    Route::get('/equipment/{type}/{id}/edit',                         [EquipmentActionController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{type}/{id}',                              [EquipmentActionController::class, 'update'])->name('equipment.update');
    Route::post('/equipment/{type}/{id}/condemn',                     [EquipmentActionController::class, 'condemn'])->name('equipment.condemn');
    Route::get('/equipment/{type}/{id}/report',                       [EquipmentActionController::class, 'report'])->name('equipment.report');
    Route::delete('/equipment/{type}/{id}',                           [EquipmentActionController::class, 'destroy'])->name('equipment.destroy');
    Route::post('/equipment/{type}/{id}/restore',                     [EquipmentActionController::class, 'restore'])->name('equipment.restore');
    Route::post('/equipment/{type}/{id}/waste',                       [EquipmentActionController::class, 'transferToWaste'])->name('equipment.waste');
    Route::post('/equipment/{type}/{id}/undo-delete', [EquipmentActionController::class, 'undoDelete'])->name('equipment.undo-delete');
    Route::get('/equipment/par-report', [EquipmentActionController::class, 'parReport'])->name('equipment.par-report');

    // Equipment articles
    Route::get('/equipment-articles',                                 [EquipmentArticleController::class, 'index'])->name('equipment.articles.index');
    Route::post('/equipment-articles',                                [EquipmentArticleController::class, 'store'])->name('equipment.articles.store');
    Route::put('/equipment-articles/{article}',                       [EquipmentArticleController::class, 'update'])->name('equipment.articles.update');
    Route::delete('/equipment-articles/{article}',                    [EquipmentArticleController::class, 'destroy'])->name('equipment.articles.destroy');

    // ── Messages (CS User Tickets) ──
    Route::get('/messages',                        [ConversationController::class, 'index'])->name('messages.index');
    Route::get('/messages/poll-all',               [ConversationController::class, 'pollAll'])->name('messages.poll-all');
    Route::get('/messages/{conversation}',         [ConversationController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}/reply',  [ConversationController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{conversation}/close', [ConversationController::class, 'close'])->name('messages.close');
    Route::patch('/messages/{conversation}/reopen',[ConversationController::class, 'reopen'])->name('messages.reopen');
    Route::get('/messages/{conversation}/poll',    [ConversationController::class, 'poll'])->name('messages.poll');

    // Other pages
    Route::get('/categories',  fn() => view('pages.categories'))->name('categories');
    Route::get('/history',     [HistoryController::class, 'index'])->name('history');
    Route::get('/condemned',   [CondemnedController::class, 'index'])->name('condemned');
    Route::post('/condemned',  [CondemnedStoreController::class, 'store'])->name('condemned.store');

    // Consumables (admin write operations)
    Route::post('/consumables',                                       [ConsumablesController::class, 'store'])->name('consumables.store');
    Route::put('/consumables/{consumable}',                           [ConsumablesController::class, 'update'])->name('consumables.update');
    Route::delete('/consumables/{consumable}',                        [ConsumablesController::class, 'destroy'])->name('consumables.destroy');
    Route::post('/consumable-requests/{consumableRequest}/review',    [ConsumableRequestController::class, 'review'])->name('consumable-requests.review');
    Route::get('/consumable-requests/blank-report',                   [ConsumableRequestController::class, 'blankReport'])->name('consumable-requests.blank-report');
    Route::get('/consumable-requests-available-items',                [ConsumableRequestController::class, 'availableItems'])->name('consumable-requests.available-items');

    // Users
    Route::get('/users',                                              [UserManagementController::class, 'index'])->name('users');
    Route::get('/users/{user}',                                       [UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users',                                             [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}',                                       [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',                                    [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/archive',                             [UserManagementController::class, 'archive'])->name('users.archive');

    // Notifications
    Route::get('/notifications',                                      [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/poll',                                 [NotificationController::class, 'poll'])->name('notifications.poll');
    Route::post('/notifications/{deletionRequest}/approve',           [NotificationController::class, 'approve'])->name('notifications.approve');
    Route::post('/notifications/{deletionRequest}/reject',            [NotificationController::class, 'reject'])->name('notifications.reject');

    // System settings
    Route::get('/settings', [SystemSettingsController::class, 'index'])->name('system.settings');

    // System Updates (Version History)
    Route::get('/settings/updates',                        [SystemUpdateController::class, 'index'])->name('system.updates');
    Route::post('/settings/updates',                       [SystemUpdateController::class, 'store'])->name('system.updates.store');
    Route::put('/settings/updates/{systemUpdate}',         [SystemUpdateController::class, 'update'])->name('system.updates.update');
    Route::delete('/settings/updates/{systemUpdate}',      [SystemUpdateController::class, 'destroy'])->name('system.updates.destroy');
    Route::patch('/settings/updates/{systemUpdate}/toggle',[SystemUpdateController::class, 'toggleModal'])->name('system.updates.toggle');
    Route::post('/settings/updates/dismiss',               [SystemUpdateController::class, 'dismissModal'])->name('system.updates.dismiss');

});

// ──────────────────────────────────────────────
// Super Admin only
// ──────────────────────────────────────────────
Route::middleware(['auth', 'role:superadmin'])->prefix('system')->name('system.')->group(function () {
    Route::get('/status',                [SystemStatusController::class, 'index'])->name('status');
    Route::post('/status/toggle',        [SystemStatusController::class, 'toggle'])->name('status.toggle');
    Route::patch('/logs/{log}/resolve',  [SystemStatusController::class, 'resolveLog'])->name('logs.resolve');
    Route::delete('/logs/clear',         [SystemStatusController::class, 'clearLogs'])->name('logs.clear');

    // Backup & Restore
    Route::get('/backup',                [BackupRestoreController::class, 'index'])->name('backup.index');
    Route::post('/backup/full',          [BackupRestoreController::class, 'backupFull'])->name('backup.full');
    Route::post('/backup/selective',     [BackupRestoreController::class, 'backupSelective'])->name('backup.selective');
    Route::get('/backup/download/{file}',[BackupRestoreController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{file}',      [BackupRestoreController::class, 'deleteBackup'])->name('backup.delete');
    Route::post('/restore',              [BackupRestoreController::class, 'restore'])->name('backup.restore');
    Route::post('/import',               [BackupRestoreController::class, 'importSql'])->name('backup.import');
});