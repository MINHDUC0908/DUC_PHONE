<?php

namespace App\Repositories;
use App\Models\ShippingAddress;

class ShippingRepository
{
    public function getAddress($customerId, $addressId)
    {
        $shippingId = ShippingAddress::where('customer_id', $customerId)->where('id', $addressId)->first();
        return $shippingId;
    }
}
