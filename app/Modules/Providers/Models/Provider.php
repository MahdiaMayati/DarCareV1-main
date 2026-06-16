<?php
// app/Modules/Providers/Models/Provider.php

namespace App\Modules\Providers\Models;

use App\Modules\Categories\Models\Category;         // موديول الأقسام
use App\Modules\ServiceRequests\Models\ServiceRequest; // mوديول الطلبات
use App\Modules\Ratings\Models\Rating;               // موديول التقييمات
use App\Modules\Locations\Models\Address;             // موديول العناوين
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Provider extends Authenticatable
{
    use HasApiTokens, SoftDeletes;
    use Notifiable;

    protected $table = 'providers';

    protected $fillable = [
        'name', 'phone', 'email', 'password', 'years_of_experience', 'bio', 'profile_image', 'status', 'rating_avg'
    ];

    protected $hidden = ['password', 'remember_token'];

    // 1. علاقة الأقسام/المهن (سباك، كهربائي...) Many-to-Many
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_provider', 'provider_id', 'category_id');
    }

    // 2. علاقة طلبات الخدمة التي استلمها الفني
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'provider_id');
    }

    // 3. علاقة التقييمات الخاصة بالفني
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'provider_id');
    }

    // 4. علاقة العناوين (Polymorphic) إذا الفني له موقع محدد
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
