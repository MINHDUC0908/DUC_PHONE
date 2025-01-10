<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class CustomerLoggedOut
{
    use SerializesModels;

    public $customer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }
}
