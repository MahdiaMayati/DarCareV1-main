<?php
// app/Modules/Locations/Services/LocationService.php

namespace App\Modules\Locations\Services;

use App\Modules\Locations\Contracts\LocationServiceInterface;
use App\Modules\Locations\Models\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LocationService implements LocationServiceInterface
{
    public function createAddress(object $owner, array $data, bool $isPrimary = false): object
    {
        return DB::transaction(function () use ($owner, $data, $isPrimary) {
            if ($isPrimary) {
                $owner->addresses()->update(['is_primary' => false]);
            }

            return $owner->addresses()->create([
                'latitude'   => $data['latitude'],
                'longitude'  => $data['longitude'],
                'label'      => $data['label'] ?? 'home',
                'is_primary' => $isPrimary,
            ]);
        });
    }

    public function setPrimaryAddress(object $owner, int $addressId): object
    {
        return DB::transaction(function () use ($owner, $addressId) {
            $address = $owner->addresses()->findOrFail($addressId);
            $owner->addresses()->update(['is_primary' => false]);
            $address->update(['is_primary' => true]);
            return $address->fresh();
        });
    }

    public function deleteAddress(object $owner, int $addressId): void
    {
        $address = $owner->addresses()->findOrFail($addressId);

        if ($address->is_primary) {
            throw ValidationException::withMessages([
                'address' => ['Cannot delete the primary address.'],
            ]);
        }

        $address->delete();
    }

    public function getAddresses(object $owner): \Illuminate\Database\Eloquent\Collection
    {
        return $owner->addresses()->orderByDesc('is_primary')->get();
    }
}
