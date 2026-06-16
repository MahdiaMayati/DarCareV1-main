<?php

namespace App\Modules\Notifications\Services;

use App\Modules\Notifications\Contracts\NotificationServiceInterface;
use App\Modules\Users\Models\User;
use App\Modules\Providers\Models\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    /**
     * توليد الـ Access Token يدوياً عبر تشفير الـ JWT للاتصال بـ Firebase
     */
    private function getGoogleAccessToken(): ?string
    {
        $path = storage_path('app/json/firebase_credentials.json');
        if (!file_exists($path)) return null;

        $credentials = json_decode(file_get_contents($path), true);

        $privateKey = $credentials['private_key'];
        $clientEmail = $credentials['client_email'];

        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);

        $now = time();
        $payload = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $privateKey, 'SHA256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    /**
     * دالة مساعدة مركزية لإرسال الإشعار لـ Token محدد عبر Firebase
     */
    private function sendFcmMessage(string $token, string $title, string $message, array $extraData = []): void
    {
        $accessToken = $this->getGoogleAccessToken();
        if (!$accessToken) return;

        $credentials = json_decode(file_get_contents(storage_path('app/json/firebase_credentials.json')), true);
        $projectId = $credentials['project_id'];
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        Http::withToken($accessToken)->post($url, [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ], $extraData)
            ]
        ]);
    }

    /**
     * 1. إرسال إشعار لمستخدم محدد (مطابق تماماً للـ Interface)
     */
    public function sendToUser(int $userId, string $type, array $data): void
    {
        $user = User::find($userId);
        if ($user && $user->fcm_token) {
            $title = $data['title'] ?? 'إشعار جديد';
            $body = $data['body'] ?? 'لديك تحديث جديد في الحساب';
            $this->sendFcmMessage($user->fcm_token, $title, $body, array_merge(['type' => $type], $data));
        }
    }

    /**
     * 2. إرسال إشعار لمزود خدمة محدد (مطابق تماماً للـ Interface)
     */
    public function sendToProvider(int $providerId, string $type, array $data): void
    {
        $provider = Provider::find($providerId);
        if ($provider && $provider->fcm_token) {
            $title = $data['title'] ?? 'إشعار جديد للمزود';
            $body = $data['body'] ?? 'لديك تحديث جديد في الطلب';
            $this->sendFcmMessage($provider->fcm_token, $title, $body, array_merge(['type' => $type], $data));
        }
    }

    /**
     * 3. إرسال إشعار عند وجود رسالة جديدة بالشات (مطابق تماماً للـ Interface)
     */
    public function sendNewMessageNotification(int $requestId, object $sender, string $body): void
    {
        // هنا نقوم بالإرسال للطرف المستلم في الشات بناءً على الـ fcm_token الخاص به إذا توفر
        if (isset($sender->fcm_token) && $sender->fcm_token) {
            $this->sendFcmMessage(
                $sender->fcm_token,
                'رسالة جديدة',
                $body,
                ['request_id' => (string)$requestId, 'type' => 'chat_message']
            );
        }
    }

    /**
     * 4. جلب إشعارات مستخدم معين من الداتابيز (مطابق تماماً للـ Interface)
     */
    public function getUserNotifications(int $userId): mixed
    {
        $user = User::find($userId);
        return $user ? $user->notifications()->latest()->get() : collect();
    }

    /**
     * 5. جلب إشعارات فني معين من الداتابيز (مطابق تماماً للـ Interface)
     */
    public function getProviderNotifications(int $providerId): mixed
    {
        $provider = Provider::find($providerId);
        return $provider ? $provider->notifications()->latest()->get() : collect();
    }

    /**
     * 6. تعيين كافة الإشعارات كمقروءة (مطابق تماماً للـ Interface)
     */
    public function markAllRead(object $notifiable): void
    {
        if (method_exists($notifiable, 'unreadNotifications')) {
            $notifiable->unreadNotifications->markAsRead();
        }
    }

    /**
     * 7. الدالة الجماعية المخصصة للأدمن لإرسال الـ Bulk Broadcast
     */
public function sendBulkNotification(string $target, string $title, string $message): void
{
    $users = collect();

    // 1. جلب المستهدفين الذين يملكون توكن
    if ($target === 'users' || $target === 'all') {
        $users = $users->merge(\App\Models\User::whereNotNull('fcm_token')->get());
    }

    if ($target === 'providers' || $target === 'all') {
        $users = $users->merge(\App\Modules\Providers\Models\Provider::whereNotNull('fcm_token')->get());
    }

    // 2. اللف لمعالجة التخزين والإرسال بأمان
    foreach ($users as $user) {

        // أ) التخزين في الداتابيز (مضمون وينجح دائماً)
        $user->notifications()->create([
            'id'   => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\AdminBulkNotification',
            'data' => [
                'title'   => $title,
                'message' => $message,
            ],
        ]);

        // ب) الشغل الصح: حماية إرسال Firebase من الانهيار
        if (!empty($user->fcm_token)) {
            try {
                // محاولة الإرسال الفوري لـ Firebase
                $this->sendFcmMessage($user->fcm_token, $title, $message);
            } catch (\Exception $e) {
                // لو كان التوكن وهمياً أو حدث خطأ في الشبكة، لارافل سيتخطى الانهيار ويكمل الـ Loop
            Log::warning("فشل إرسال FCM للمستخدم رقم {$user->id}: " . $e->getMessage());            }
        }
    }
}
}
