<?php
// app/Modules/Users/Models/User.php

namespace App\Modules\Users\Models;

use App\Modules\Locations\Models\Address;
use App\Modules\ServiceRequests\Models\ServiceRequest; // 1. ضفنا مسار موديل الطلبات هنا
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name', 'phone', 'email', 'password', 'profile_image', 'role',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function primaryAddress(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable')->where('is_primary', true);
    }

    // 2. هذه هي العلاقة الجديدة التي تحتاجها لوحة التحكم لعرض طلبات هذا المستخدم
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'user_id');
    }
}
