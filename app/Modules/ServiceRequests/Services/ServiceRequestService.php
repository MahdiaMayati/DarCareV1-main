<?php
// app/Modules/ServiceRequests/Services/ServiceRequestService.php

namespace App\Modules\ServiceRequests\Services;

use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use App\Modules\ServiceRequests\Contracts\ServiceRequestServiceInterface;
use App\Modules\ServiceRequests\Models\ServiceRequest;
use App\Enums\RequestStatusEnum;

class ServiceRequestService implements ServiceRequestServiceInterface
{
    public function __construct(
        private readonly NotificationServiceInterface $notificationService
    ) {}

    public function createRequest(int $userId, array $data): object
    {
        $request = ServiceRequest::create([
            'user_id'        => $userId,
            'provider_id'    => $data['provider_id'],
            'category_id'    => $data['category_id'],
            'address_id'     => $data['address_id'] ?? null,
            'description'    => $data['description'],
            'urgency'        => $data['urgency'] ?? 'normal',
            'image'          => $data['image'] ?? null,
            'status'         => 'pending',
            'temp_latitude'  => $data['temp_latitude'] ?? null,
            'temp_longitude' => $data['temp_longitude'] ?? null,
            'temp_label'     => $data['temp_label'] ?? null,
        ]);

        // Notify provider via cross-module contract
        $this->notificationService->sendToProvider(
            $data['provider_id'],
            'new_request',
            ['request_id' => $request->id, 'message' => 'You have a new service request']
        );

        return $request;
    }

    public function getUserRequests(int $userId): mixed
    {
        return ServiceRequest::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function getProviderRequests(int $providerId): mixed
    {
        return ServiceRequest::where('provider_id', $providerId)
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    public function updateStatus(int $requestId, int $providerId, string $status, ?string $scheduledAt): object
    {
        $request = ServiceRequest::where('id', $requestId)
            ->where('provider_id', $providerId)
            ->firstOrFail();

        $request->update([
            'status'       => $status,
            'scheduled_at' => $scheduledAt,
        ]);

        // Notify user
        $this->notificationService->sendToUser(
            $request->user_id,
            'request_' . $status,
            ['request_id' => $request->id, 'message' => "Your request has been {$status}"]
        );

        return $request->fresh();
    }

    public function find(int $requestId): object
    {
        return ServiceRequest::findOrFail($requestId);
    }

    //? Admin

    public function getAllRequestsForAdmin(?string $status, ?string $urgency): mixed
    {
    // جلب الطلبات مع علاقاتها كاملة للأدمن
      $query = ServiceRequest::with(['user:id,name,phone', 'provider:id,name,phone', 'category:id,name', 'address']);

      if ($status) {
        $query->where('status', $status);
      }

      if ($urgency) {
        $query->where('urgency', $urgency);
      }

      return $query->orderByDesc('created_at')->paginate(15);
    }

    public function getRequestDetailsForAdmin(int $requestId): object
    {
        return ServiceRequest::with(['user', 'provider', 'category', 'address'])->findOrFail($requestId);
    }
}
