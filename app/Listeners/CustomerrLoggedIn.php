<?php

namespace App\Listeners;

use App\Events\CustomerLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CustomerrLoggedIn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CustomerLoggedIn $event)
    {
        $customer = $event->customer;
        $customer->update(['status' => 'online']);
    }
}
