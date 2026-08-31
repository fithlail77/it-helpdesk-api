<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IpDeviceController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SparepartController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/weekly', [DashboardController::class, 'weekly']);
    Route::get('/dashboard/team-stats', [DashboardController::class, 'teamStats']);

    // Activities
    Route::apiResource('activities', ActivityController::class)->names('api.activities');
    Route::post('/activities/{activity}/photo', [ActivityController::class, 'uploadPhoto']);

    // Spareparts
    Route::apiResource('spareparts', SparepartController::class)->names('api.spareparts');

    // IP Devices
    Route::apiResource('ip-devices', IpDeviceController::class)->names('api.ip-devices');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);

    // Assets
    Route::get('/assets/search', [\App\Http\Controllers\Api\ActivityController::class, 'searchAssets'])->name('api.assets.search');
});
