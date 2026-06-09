<?php
// app/Modules/ServiceRequests/Http/Controllers/ServiceRequestController.php

namespace App\Modules\ServiceRequests\Http\Controllers;

use App\Modules\ServiceRequests\Contracts\ServiceRequestServiceInterface;
use App\Modules\ServiceRequests\Http\Requests\StoreServiceRequestRequest;
use App\Modules\ServiceRequests\Http\Requests\UpdateRequestStatusRequest;
use App\Modules\ServiceRequests\Http\Resources\ServiceRequestResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ServiceRequestController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly ServiceRequestServiceInterface $service
    ) {}

    // User: create request
    public function store(StoreServiceRequestRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('requests/images', 'public');
        }

        $serviceRequest = $this->service->createRequest($request->user()->id, $data);
        return $this->created(new ServiceRequestResource($serviceRequest));
    }

    // User: my requests
    public function userRequests(Request $request): JsonResponse
    {
        $requests = $this->service->getUserRequests($request->user()->id);
        return $this->success(ServiceRequestResource::collection($requests));
    }

    // Provider: incoming requests
    public function providerRequests(Request $request): JsonResponse
    {
        $requests = $this->service->getProviderRequests($request->user()->id);
        return $this->success(ServiceRequestResource::collection($requests));
    }

    // Provider: update request status
    public function updateStatus(UpdateRequestStatusRequest $request, int $id): JsonResponse
    {
        $serviceRequest = $this->service->updateStatus(
            $id,
            $request->user()->id,
            $request->status,
            $request->scheduled_at
        );
        return $this->success(new ServiceRequestResource($serviceRequest), 'Status updated');
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(new ServiceRequestResource($this->service->find($id)));
    }
}
