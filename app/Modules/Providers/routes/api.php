<?php
// app/Modules/Providers/routes/api.php

use App\Modules\Providers\Http\Controllers\Admin\AdminProviderController;
use App\Modules\Providers\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

// Public
Route::prefix('providers')->name('providers.')->group(function () {
    Route::get('/search',           [ProviderController::class, 'search'])->name('search');
    Route::get('/nearby',           [ProviderController::class, 'nearby'])->name('nearby');
    Route::get('/{id}',             [ProviderController::class, 'show'])->name('show');
});

// Provider Auth
Route::middleware('auth:sanctum')->prefix('provider')->name('provider.')->group(function () {
    Route::get('profile',           [ProviderController::class, 'profile'])->name('profile');
    Route::post('profile',          [ProviderController::class, 'update'])->name('update');
    Route::patch('toggle-status',   [ProviderController::class, 'toggleStatus'])->name('toggleStatus');
});

// روابط الأدمن لإدارة المزودين
Route::prefix('admin')->group(function () {
    Route::get('providers', [AdminProviderController::class, 'index']);
    Route::patch('providers/{id}/status', [AdminProviderController::class, 'updateStatus']);
});
