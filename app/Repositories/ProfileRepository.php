<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;

class ProfileRepository
{
    public function findByIdCustomer($userId)
    {
        return Customer::find($userId);
    }

    public function save($customer)
    {
        return $customer->save();
    }
}