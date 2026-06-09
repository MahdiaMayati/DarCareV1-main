<?php
// app/Modules/Notifications/Services/NotificationService.php

namespace App\Modules\Notifications\Services;

use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use App\Modules\Notifications\Models\Notification;
use App\Modules\Providers\Models\Provider;
use App\Modules\Users\Models\User;
use App\Modules\Notifications\Events\NotificationSent;

class NotificationService implements NotificationServiceInterface
{
    public function sendToUser(int $userId, string $type, array $data): void
    {
        $notification = Notification::create([
            'notifiable_type' => User::class,
            'notifiable_id'   => $userId,
            'type'            => $type,
            'data'            => $data,
        ]);
        
        broadcast(new NotificationSent($notification))->toOthers();
    }

    public function sendToProvider(int $providerId, string $type, array $data): void
    {
        $notification = Notification::create([
            'notifiable_type' => Provider::class,
            'notifiable_id'   => $providerId,
            'type'            => $type,
            'data'            => $data,
        ]);
        
        broadcast(new NotificationSent($notification));
    }

    public function sendNewMessageNotification(int $requestId, object $sender, string $body): void
    {
        // Determine recipient
        $isUser = $sender instanceof User;
        $type   = 'new_message';
        $data   = ['request_id' => $requestId, 'preview' => substr($body, 0, 100)];

        // Will be replaced by event sourcing / queued jobs in production
        // For now, store notification for the other party
    }

    public function getUserNotifications(int $userId): mixed
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function getProviderNotifications(int $providerId): mixed
    {
        return Notification::where('notifiable_type', Provider::class)
            ->where('notifiable_id', $providerId)
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function markAllRead(object $notifiable): void
    {
        Notification::where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
