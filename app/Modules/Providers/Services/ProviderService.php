<?php
// app/Modules/Providers/Services/ProviderService.php

namespace App\Modules\Providers\Services;

use App\Modules\Providers\Contracts\ProviderServiceInterface;
use App\Modules\Providers\Models\Provider;
use Illuminate\Support\Facades\DB;
use App\Enums\ProviderStatusEnum;

class ProviderService implements ProviderServiceInterface
{
    public function getProfile(int $providerId): object
    {
        return Provider::with(['addresses', 'categories'])->findOrFail($providerId);
    }

    public function updateProfile(int $providerId, array $data): object
    {
        $provider = Provider::findOrFail($providerId);
        $provider->update($data);
        return $provider->fresh(['addresses', 'categories']);
    }

    public function toggleStatus(int $providerId): object
    {
        $provider = Provider::findOrFail($providerId);
        $newStatus = $provider->status === 'available' ? 'busy' : 'available';
        $provider->update(['status' => $newStatus]);
        return $provider->fresh();
    }

    public function searchProviders(array $filters): mixed
    {
        $query = Provider::with(['categories', 'addresses'])
            ->where('status', 'available');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', fn($q) =>
                $q->where('categories.id', $filters['category_id'])
            );
        }

        return $query->paginate(15);
    }

    public function getNearbyProviders(float $latitude, float $longitude, float $radius = 10): mixed
    {
        // Haversine formula using raw SQL
        return Provider::with(['categories'])
            ->where('status', 'available')
            ->whereHas('addresses', function ($q) use ($latitude, $longitude, $radius) {
                $q->where('is_primary', true)
                  ->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(latitude))
                        * cos(radians(longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(latitude))
                    )) <= ?
                  ", [$latitude, $longitude, $latitude, $radius]);
            })
            ->paginate(20);
    }


         //? Admin
        public function getAllProvidersForAdmin(?string $status): mixed
        {
            $query = Provider::with('categories:id,name');

            if ($status) {
                $query->where('status', $status);
            }

            return $query->orderBy('rating_avg', 'desc')->paginate(15);
        }

        public function updateProviderStatusForAdmin(int $providerId, string $status): object
        {
            $provider = Provider::findOrFail($providerId);

            $provider->update([
                'status' => $status
            ]);

            return $provider->fresh();
        }
}
