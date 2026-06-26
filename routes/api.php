<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NetworkController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('network')->group(function () {
    Route::get('/devices', [NetworkController::class, 'devices']);
    Route::get('/devices/{id}', [NetworkController::class, 'deviceDetail']);
    Route::get('/devices/by-ip', [NetworkController::class, 'deviceByIp']);
    Route::get('/ping/{id}', [NetworkController::class, 'ping']);
    Route::get('/status', [NetworkController::class, 'statusSummary']);
    Route::get('/discover', [NetworkController::class, 'discover']);
    Route::get('/health', [NetworkController::class, 'healthCheck']);
    Route::get('/statistics', [NetworkController::class, 'statistics']);
    Route::get('/network/devices-with-ip', [NetworkController::class, 'devicesWithIp']);
});

// Protected routes (dengan API key)
Route::middleware(['api.key'])->group(function () {
    Route::prefix('network')->group(function () {
        Route::get('/devices', [NetworkController::class, 'devices']);
        Route::get('/devices/{id}', [NetworkController::class, 'deviceDetail']);
        Route::get('/devices/by-ip', [NetworkController::class, 'deviceByIp']);
        Route::get('/ping/{id}', [NetworkController::class, 'ping']);
        Route::get('/status', [NetworkController::class, 'statusSummary']);
        Route::get('/health', [NetworkController::class, 'healthCheck']);
        Route::get('/statistics', [NetworkController::class, 'statistics']);
        Route::get('/devices-with-ip', [NetworkController::class, 'devicesWithIp']);
    });
});