<?php
// app/Modules/Locations/Http/Controllers/LocationController.php

namespace App\Modules\Locations\Http\Controllers;

use App\Modules\Locations\Contracts\LocationServiceInterface;
use App\Modules\Locations\Http\Requests\StoreAddressRequest;
use App\Modules\Locations\Http\Resources\AddressResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LocationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly LocationServiceInterface $locationService) {}

    public function index(Request $request): JsonResponse
    {
        $addresses = $this->locationService->getAddresses($request->user());
        return $this->success(AddressResource::collection($addresses));
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->locationService->createAddress($request->user(), $request->validated());
        return $this->created(new AddressResource($address));
    }

    public function setPrimary(Request $request, int $addressId): JsonResponse
    {
        $address = $this->locationService->setPrimaryAddress($request->user(), $addressId);
        return $this->success(new AddressResource($address), 'Primary address updated');
    }

    public function destroy(Request $request, int $addressId): JsonResponse
    {
        $this->locationService->deleteAddress($request->user(), $addressId);
        return $this->success(null, 'Address deleted');
    }
}
