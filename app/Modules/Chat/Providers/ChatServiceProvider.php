<?php
// app/Modules/Chat/Providers/ChatServiceProvider.php

namespace App\Modules\Chat\Providers;

use App\Modules\Chat\Contracts\ChatServiceInterface;
use App\Modules\Chat\Services\ChatService;
use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ChatServiceInterface::class, ChatService::class);
    }
    public function boot(): void {}
}
