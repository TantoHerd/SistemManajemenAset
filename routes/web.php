<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategorySpecificationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    
    // ============================================
    // ADMIN ROUTES
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // ============================================
        // ASSETS - FINAL
        // ============================================

        // Route spesifik (TANPA parameter)
        Route::get('assets/import', [AssetController::class, 'showImportForm'])->name('assets.import');
        Route::post('assets/import', [AssetController::class, 'import'])->name('assets.import.store');
        Route::get('assets/import/template', [AssetController::class, 'downloadTemplate'])->name('assets.import.template');
        Route::get('assets/export/excel', [AssetController::class, 'export'])->name('assets.export');
        Route::get('amortization/export', [AssetController::class, 'exportAmortization'])->name('amortization.export');
        Route::get('assets/create', [AssetController::class, 'create'])->name('assets.create');
        Route::post('assets', [AssetController::class, 'store'])->name('assets.store');
        Route::post('assets/print-labels', [AssetController::class, 'printLabels'])->name('assets.print-labels');
        Route::post('assets/scan', [AssetController::class, 'getAssetByBarcode'])->name('assets.scan');
        Route::get('assets/reset-filter', [AssetController::class, 'resetFilter'])->name('assets.reset-filter');
        Route::get('assets/specifications/by-category', [AssetController::class, 'getSpecificationsByCategory'])->name('assets.specifications.by-category');

        // Route dengan parameter {asset}
        Route::get('assets', [AssetController::class, 'index'])->name('assets.index');
        Route::get('assets/{asset}', [AssetController::class, 'show'])->name('assets.show');
        Route::get('assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
        Route::delete('assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
        Route::get('assets/{asset}/barcode-image', [AssetController::class, 'getBarcodeImage'])->name('assets.barcode-image');
        Route::post('assets/{asset}/generate-barcode', [AssetController::class, 'generateBarcode'])->name('assets.generate-barcode');
        Route::get('assets/{asset}/print-label', [AssetController::class, 'printLabel'])->name('assets.print-label');
        Route::post('assets/{asset}/toggle-checkinout', [AssetController::class, 'toggleCheckInOut'])->name('assets.toggle-checkinout');

        // ============================================
        // CATEGORIES
        // ============================================
        Route::middleware('permission:view categories')->group(function () {
            Route::resource('categories', CategoryController::class)->only(['index', 'show']);
        });
        Route::middleware('permission:create categories')->group(function () {
            Route::resource('categories', CategoryController::class)->only(['create', 'store']);
        });
        Route::middleware('permission:edit categories')->group(function () {
            Route::resource('categories', CategoryController::class)->only(['edit', 'update']);
        });
        Route::middleware('permission:delete categories')->group(function () {
            Route::resource('categories', CategoryController::class)->only(['destroy']);
        });
        
        // Spesifikasi Kategori
        Route::middleware('permission:manage specifications')->group(function () {
            Route::prefix('categories/{category}/specifications')
                ->name('categories.specifications.')
                ->group(function () {
                    Route::get('/', [CategorySpecificationController::class, 'index'])->name('index');
                    Route::post('/', [CategorySpecificationController::class, 'store'])->name('store');
                    Route::put('/{specification}', [CategorySpecificationController::class, 'update'])->name('update');
                    Route::delete('/{specification}', [CategorySpecificationController::class, 'destroy'])->name('destroy');
                    Route::post('/{specification}/toggle-active', [CategorySpecificationController::class, 'toggleActive'])->name('toggle-active');
                    Route::post('/update-order', [CategorySpecificationController::class, 'updateOrder'])->name('update-order');
                });
        });
        
        // ============================================
        // LOCATIONS
        // ============================================
        Route::middleware('permission:view locations')->group(function () {
            Route::resource('locations', LocationController::class)->only(['index', 'show']);
        });
        Route::middleware('permission:create locations')->group(function () {
            Route::resource('locations', LocationController::class)->only(['create', 'store']);
        });
        Route::middleware('permission:edit locations')->group(function () {
            Route::resource('locations', LocationController::class)->only(['edit', 'update']);
        });
        Route::middleware('permission:delete locations')->group(function () {
            Route::resource('locations', LocationController::class)->only(['destroy']);
        });
        
        // ============================================
        // MAINTENANCE
        // ============================================
        Route::middleware('permission:view maintenances')->group(function () {
            Route::get('maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
            Route::get('maintenances/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenances.show');
            Route::get('maintenances/schedule', [MaintenanceController::class, 'schedule'])->name('maintenances.schedule');
            Route::get('maintenances/history', [MaintenanceController::class, 'history'])->name('maintenances.history');
            Route::get('maintenances/report', [MaintenanceController::class, 'report'])->name('maintenances.report');
        });
        Route::middleware('permission:create maintenances')->group(function () {
            Route::get('maintenances/create', [MaintenanceController::class, 'create'])->name('maintenances.create');
            Route::post('maintenances', [MaintenanceController::class, 'store'])->name('maintenances.store');
        });
        Route::middleware('permission:edit maintenances')->group(function () {
            Route::get('maintenances/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenances.edit');
            Route::put('maintenances/{maintenance}', [MaintenanceController::class, 'update'])->name('maintenances.update');
        });
        Route::middleware('permission:delete maintenances')->group(function () {
            Route::delete('maintenances/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenances.destroy');
        });
        
        // ============================================
        // LOANS
        // ============================================
        Route::middleware('permission:view loans')->group(function () {
            Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
            Route::get('loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
        });
        Route::middleware('permission:create loans')->group(function () {
            Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create');
            Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
        });
        Route::middleware('permission:approve loans')->group(function () {
            Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        });
        Route::middleware('permission:reject loans')->group(function () {
            Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        });
        Route::middleware('permission:return loans')->group(function () {
            Route::post('loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
        });
        Route::middleware('permission:cancel loans')->group(function () {
            Route::post('loans/{loan}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');
        });
        
        // ============================================
        // USERS
        // ============================================
        // Route spesifik HARUS di atas resource
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::get('users/import', [UserController::class, 'showImportForm'])->name('users.import');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import.store');
        Route::get('users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
        Route::patch('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Resource (harus di bawah route spesifik)
        Route::resource('users', UserController::class);
        
        // ============================================
        // SETTINGS
        // ============================================
        Route::middleware('permission:view settings')->group(function () {
            Route::get('settings', [SettingController::class, 'index'])->name('settings');
        });
        Route::middleware('permission:edit settings')->group(function () {
            Route::patch('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
            Route::patch('settings/logo', [SettingController::class, 'updateLogo'])->name('settings.logo');
            Route::patch('settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');
            Route::get('settings/logo/remove', [SettingController::class, 'removeLogo'])->name('settings.logo.remove');
            Route::get('settings/favicon/remove', [SettingController::class, 'removeFavicon'])->name('settings.favicon.remove');
        });
        
        // ============================================
        // NOTIFICATIONS
        // ============================================
        Route::middleware('permission:view notifications')->group(function () {
            Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
            Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        });
        Route::middleware('permission:manage notifications')->group(function () {
            Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        });
    });
});

require __DIR__.'/auth.php';