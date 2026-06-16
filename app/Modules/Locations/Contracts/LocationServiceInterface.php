<?php
// app/Modules/Locations/Contracts/LocationServiceInterface.php

namespace App\Modules\Locations\Contracts;

interface LocationServiceInterface
{
    public function createAddress(object $owner, array $data, bool $isPrimary = false): object;
    public function setPrimaryAddress(object $owner, int $addressId): object;
    public function deleteAddress(object $owner, int $addressId): void;
    public function getAddresses(object $owner): \Illuminate\Database\Eloquent\Collection;

    public function getAllAddressesForAdmin();
}
