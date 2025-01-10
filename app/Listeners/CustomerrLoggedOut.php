<?php

namespace App\Listeners;

use App\Events\CustomerLoggedOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerrLoggedOut
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
    public function handle(CustomerLoggedOut $event)
    {
        $customer = $event->customer;
        
        Log::info('Đang cập nhật trạng thái của khách hàng', ['customer_id' => $customer->id]);
    
        $customer->status = 'offline';  // Cập nhật trạng thái
        $customer->save();  // Lưu vào cơ sở dữ liệu
    
        Log::info('Trạng thái khách hàng đã được cập nhật', ['status' => $customer->status]);
    }
}
