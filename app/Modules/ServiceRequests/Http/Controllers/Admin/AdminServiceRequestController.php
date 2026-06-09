<?php

namespace App\Modules\ServiceRequests\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\ServiceRequests\Contracts\ServiceRequestServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminServiceRequestController extends Controller
{
        // حقن الخدمة عبر الـ Interface
    public function __construct(
        private readonly ServiceRequestServiceInterface $serviceRequestService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $requests = $this->serviceRequestService->getAllRequestsForAdmin(
            $request->query('status'),
            $request->query('urgency')
        );

        return response()->json([
            'status' => 'success',
            'data' => $requests
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $requestDetails = $this->serviceRequestService->getRequestDetailsForAdmin($id);

        return response()->json([
            'status' => 'success',
            'data' => $requestDetails
        ], 200);
    }
}
