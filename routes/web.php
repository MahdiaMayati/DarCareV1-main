<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Chat\Models\Message;
use App\Modules\Chat\Events\MessageSent;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-broadcast', function () {
    $message = Message::latest()->first();

    broadcast(new MessageSent($message));

    return 'Broadcast sent!';
});