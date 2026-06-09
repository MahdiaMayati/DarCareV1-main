<?php
// app/Modules/ServiceRequests/Models/ServiceRequest.php

namespace App\Modules\ServiceRequests\Models;

use App\Modules\Users\Models\User;                 // موديول المستخدمين
use App\Modules\Providers\Models\Provider;           // موديول المزودين
use App\Modules\Categories\Models\Category;         // موديول الأقسام
use App\Modules\Locations\Models\Address;           // موديول العناوين
use App\Modules\Ratings\Models\Rating;               // موديول التقييمات
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $table = 'service_requests';

    protected $fillable = [
        'user_id', 'provider_id', 'category_id', 'address_id',
        'description', 'urgency', 'image', 'status',
        'temp_latitude', 'temp_longitude', 'temp_label', 'scheduled_at'
    ];

    // 1. الطلب ينتمي لزبون محدد
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 2. الطلب ينتمي لمزود خدمة (حرفي) محدد
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    // 3. الطلب تابع لقسم/تصنيف محدد (سباكة، كهرباء...)
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // 4. الطلب يرتبط بعنوان محدد من جدول العناوين
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'addressable_id');
    }

    // 5. الطلب قد يمتلك تقييم واحد بعد اكتماله
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'service_request_id');
    }
}
