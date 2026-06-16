<?php
// app/Modules/Locations/routes/api.php

use App\Modules\Locations\Http\Controllers\Admin\AdminLocationController;
use App\Modules\Locations\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('addresses')->name('addresses.')->group(function () {
    Route::get('/',                         [LocationController::class, 'index'])->name('index');
    Route::post('/',                        [LocationController::class, 'store'])->name('store');
    Route::patch('/{addressId}/primary',    [LocationController::class, 'setPrimary'])->name('primary');
    Route::delete('/{addressId}',           [LocationController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin')->group(function () {
    Route::get('locations', [AdminLocationController::class, 'index']);
});
