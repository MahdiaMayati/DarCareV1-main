<?php
// app/Modules/Chat/Http/Controllers/ChatController.php

namespace App\Modules\Chat\Http\Controllers;

use App\Modules\Chat\Contracts\ChatServiceInterface;
use App\Modules\Chat\Http\Requests\SendMessageRequest;
use App\Modules\Chat\Http\Resources\MessageResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ChatController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly ChatServiceInterface $chatService) {}

    public function index(Request $request, int $requestId): JsonResponse
    {
        $messages = $this->chatService->getMessages($requestId);
        return $this->success(MessageResource::collection($messages));
    }

    public function send(SendMessageRequest $request, int $requestId): JsonResponse
    {
        $message = $this->chatService->sendMessage(
            $requestId,
            $request->user(),
            $request->body
        );
        return $this->created(new MessageResource($message));
    }
}
