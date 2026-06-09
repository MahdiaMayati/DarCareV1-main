<?php
// app/Modules/Notifications/routes/api.php

use App\Modules\Notifications\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/',           [NotificationController::class, 'index'])->name('index');
    Route::patch('/read-all', [NotificationController::class, 'markRead'])->name('markRead');
});
