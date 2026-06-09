<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Users\Models\User;
use App\Modules\Providers\Models\Provider;
use App\Modules\ServiceRequests\Models\ServiceRequest;
use App\Enums\RoleEnum;
use App\Enums\ProviderStatusEnum;
use App\Enums\RequestStatusEnum;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends Controller
{
    /**
     * جلب أرقام الإحصائيات السريعة للوحة التحكم
     */
    public function getSummaryStats(): JsonResponse
    {
       return response()->json([
            'status' => 'success',
            'data' => [
                'total_customers'         => User::where('role', RoleEnum::User->value)->count(),
                'total_providers'         => Provider::count(),
                'active_now_providers'    => Provider::where('status', ProviderStatusEnum::Available->value)->count(),
                'total_requests'          => ServiceRequest::count(),
                'pending_requests'        => ServiceRequest::where('status', RequestStatusEnum::Pending->value)->count(),
                'completed_requests'      => ServiceRequest::where('status', RequestStatusEnum::Completed->value)->count(),
                'urgent_pending_requests' => ServiceRequest::where('urgency', 'urgent')
                    ->where('status', RequestStatusEnum::Pending->value)
                    ->count(),
            ]
        ], 200);
    }
}
