<?php

namespace App\Modules\Dashboard\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الخدمات (Register bindings)
     */
    public function register(): void
    {
        // إذا كان لدينا كونتراكت وسيرفيس مستقبلاً نسجلهم هنا
    }

    /**
     * تفعيل الموديول وتحميل الروابط (Bootstrapping)
     */
    public function boot(): void
    {
        // إجبار لارافل على قراءة ملف الروابط الخاص بهذا الموديول تلقائياً
        if (file_exists(__DIR__ . '/../routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__ . '/../routes/api.php');
        }
    }
}
