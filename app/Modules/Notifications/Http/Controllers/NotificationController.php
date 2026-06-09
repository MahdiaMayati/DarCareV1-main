<?php
// app/Modules/Notifications/Http/Controllers/NotificationController.php

namespace App\Modules\Notifications\Http\Controllers;

use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use App\Modules\Notifications\Http\Resources\NotificationResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly NotificationServiceInterface $service) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $isProvider = method_exists($user, 'categories'); // simple duck typing check

        $notifications = $isProvider
            ? $this->service->getProviderNotifications($user->id)
            : $this->service->getUserNotifications($user->id);

        return $this->success(NotificationResource::collection($notifications));
    }

    public function markRead(Request $request): JsonResponse
    {
        $this->service->markAllRead($request->user());
        return $this->success(null, 'Notifications marked as read');
    }
}
