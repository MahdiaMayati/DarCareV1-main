<?php
// app/Modules/Chat/routes/api.php

use App\Modules\Chat\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('requests/{requestId}/messages')->name('chat.')->group(function () {
    Route::get('/',   [ChatController::class, 'index'])->name('index');
    Route::post('/',  [ChatController::class, 'send'])->name('send');
});
