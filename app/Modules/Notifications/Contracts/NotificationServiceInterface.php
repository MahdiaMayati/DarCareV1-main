<?php
// app/Modules/Notifications/Contracts/NotificationServiceInterface.php

namespace App\Modules\Notifications\Contracts;

interface NotificationServiceInterface
{
    public function sendToUser(int $userId, string $type, array $data): void;
    public function sendToProvider(int $providerId, string $type, array $data): void;
    public function sendNewMessageNotification(int $requestId, object $sender, string $body): void;
    public function getUserNotifications(int $userId): mixed;
    public function getProviderNotifications(int $providerId): mixed;
    public function markAllRead(object $notifiable): void;
}
    