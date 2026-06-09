<?php

use App\Modules\Dashboard\Http\Controllers\DashboardStatsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    Route::get('dashboard/stats', [DashboardStatsController::class, 'getSummaryStats']);
});
