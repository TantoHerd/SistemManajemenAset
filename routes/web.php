<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategorySpecificationController;

// Halaman utama redirect ke dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    
    // ============================================
    // IMPORT ASSET ROUTES (tanpa prefix admin)
    // ============================================
    Route::get('/assets/import', [AssetController::class, 'showImportForm'])->name('assets.import');
    Route::get('/assets/import/template', [AssetController::class, 'downloadTemplate'])->name('assets.import.template');
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import.store');

    // ============================================
    // USER IMPORT ROUTES (DITAMBAHKAN SEPERTI ASSET)
    // ============================================
    Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import');
    Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
    Route::post('/users/import', [UserController::class, 'import'])->name('users.import.store');

    Route::middleware(['auth'])->group(function () {
        Route::get('/users-import', [App\Http\Controllers\Admin\UserController::class, 'showImportForm'])->name('users.import.test');
    });
    
    // ============================================
    // ADMIN ROUTES
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Categories
        Route::resource('categories', CategoryController::class);

        // Spesifikasi
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
        
        // Locations
        Route::resource('locations', LocationController::class);

        // Reset Filter
        // Route::get('/reset-asset-filter', [App\Http\Controllers\Admin\AssetController::class, 'resetFilter'])->name('assets.reset.filter');
        
        // Assets
        Route::resource('assets', AssetController::class);
        Route::post('assets/scan', [AssetController::class, 'getAssetByBarcode'])->name('assets.scan');
        Route::get('assets/{asset}/print-label', [AssetController::class, 'printLabel'])->name('assets.print-label');
        Route::post('assets/print-labels', [AssetController::class, 'printLabels'])->name('assets.print-labels');
        Route::post('assets/{asset}/toggle-checkinout', [AssetController::class, 'toggleCheckInOut'])->name('assets.toggle-checkinout');
        Route::get('assets/{asset}/barcode-image', [AssetController::class, 'getBarcodeImage'])->name('assets.barcode-image');
        Route::post('assets/{asset}/generate-barcode', [AssetController::class, 'generateBarcode'])->name('assets.generate-barcode');
        Route::get('assets/export/excel', [AssetController::class, 'export'])->name('assets.export');
        Route::get('amortization/export', [AssetController::class, 'exportAmortization'])->name('amortization.export');
        Route::get('assets/reset-filter', [AssetController::class, 'resetFilter'])->name('assets.reset-filter');

        // AJAX: Get specifications by category
        Route::get('assets/specifications/by-category', [AssetController::class, 'getSpecificationsByCategory'])
         ->name('assets.specifications.by-category');

        // Users
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // User Import & Export
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        
        // MAINTENANCE - Route spesifik HARUS di atas Route::resource
        Route::get('maintenances/schedule', [MaintenanceController::class, 'schedule'])->name('maintenances.schedule');
        Route::get('maintenances/history', [MaintenanceController::class, 'history'])->name('maintenances.history');
        Route::get('maintenances/report', [MaintenanceController::class, 'report'])->name('maintenances.report');
        
        // Maintenance Resource (harus setelah route spesifik)
        Route::resource('maintenances', MaintenanceController::class);

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::patch('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
        Route::patch('settings/logo', [SettingController::class, 'updateLogo'])->name('settings.logo');
        Route::patch('settings/preferences', [SettingController::class, 'updatePreferences'])->name('settings.preferences');
        Route::get('settings/logo/remove', [SettingController::class, 'removeLogo'])->name('settings.logo.remove');
        Route::get('settings/favicon/remove', [SettingController::class, 'removeFavicon'])->name('settings.favicon.remove');
    });
});

require __DIR__.'/auth.php';