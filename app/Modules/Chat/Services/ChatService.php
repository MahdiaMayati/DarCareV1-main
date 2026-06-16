<?php
// app/Modules/Chat/Services/ChatService.php

namespace App\Modules\Chat\Services;

use App\Modules\Chat\Contracts\ChatServiceInterface;
use App\Modules\Chat\Models\Message;
use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use App\Modules\Chat\Events\MessageSent;

class ChatService implements ChatServiceInterface
{
    public function __construct(
        private readonly NotificationServiceInterface $notificationService
    ) {}

    public function getMessages(int $requestId): mixed
    {
        return Message::where('service_request_id', $requestId)
            ->orderBy('created_at')
            ->paginate(50);
    }

    public function sendMessage(int $requestId, object $sender, string $body): object
    {
        $message = Message::create([
            'service_request_id' => $requestId,
            'sender_type'        => get_class($sender),
            'sender_id'          => $sender->id,
            'body'               => $body,
        ]);

         broadcast(new MessageSent($message));

        $this->notificationService->sendNewMessageNotification($requestId, $sender, $body);

        return $message;
    }
}
