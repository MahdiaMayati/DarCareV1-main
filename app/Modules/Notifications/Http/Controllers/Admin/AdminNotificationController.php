<?php

namespace App\Modules\Notifications\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function __construct(
        private readonly NotificationServiceInterface $notificationService
    ) {}

    public function send(Request $request): JsonResponse
    {
        // 1. التحقق من البيانات القادمة من الداشبورد
        $request->validate([
            'target'  => 'required|in:all,users,providers',
            'title'   => 'required|string|max:150',
            'message' => 'required|string'
        ]);

        // 2. استدعاء الدالة الجماعية عبر الـ Service لترسل لـ Firebase فوراً
        $this->notificationService->sendBulkNotification(
            $request->target,
            $request->title,
            $request->message
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم دفع الإشعار الجماعي إلى سيرفرات Firebase بنجاح والأجهزة تستقبله الآن فوداً!'
        ], 200);
    }
}
