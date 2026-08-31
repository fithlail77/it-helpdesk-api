<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ActivityController;
use App\Http\Controllers\Web\SparepartController;
use App\Http\Controllers\Web\IpDeviceController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('web.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::resource('activities', ActivityController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
    Route::patch('/activities/{activity}/status', [ActivityController::class, 'updateStatus'])->name('activities.status');

    // Spareparts
    Route::resource('spareparts', SparepartController::class)->except(['show']);

    // IP Devices
    Route::resource('ip-devices', IpDeviceController::class)->except(['show']);

    // Assets lookup
    Route::get('/assets/search', [ActivityController::class, 'searchAssets'])->name('assets.search');
});
