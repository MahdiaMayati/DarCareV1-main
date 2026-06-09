<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withProviders([
        App\Modules\Auth\Providers\AuthServiceProvider::class,
        App\Modules\Users\Providers\UsersServiceProvider::class,
        App\Modules\Providers\Providers\ProvidersServiceProvider::class,
        App\Modules\Locations\Providers\LocationsServiceProvider::class,
        App\Modules\Categories\Providers\CategoriesServiceProvider::class,
        App\Modules\ServiceRequests\Providers\ServiceRequestsServiceProvider::class,
        App\Modules\Chat\Providers\ChatServiceProvider::class,
        App\Modules\Notifications\Providers\NotificationsServiceProvider::class,
        App\Modules\Favorites\Providers\FavoritesServiceProvider::class,
        App\Modules\Ratings\Providers\RatingsServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
