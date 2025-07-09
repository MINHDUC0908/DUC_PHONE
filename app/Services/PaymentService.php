<?php

namespace App\Services;

use App\Http\Controllers\Api\PayPalController;
use App\Http\Controllers\Api\VNPayController;
use App\Http\Controllers\Api\ZaloPayController;

class PaymentService
{
    public function handle($method, $order, $total, $customer)
    {
        switch ($method) {
            case 'cod':
                return $this->success($order);

            case 'Online':
                $vnpay = new VNPayController();
                return $vnpay->processVNPay($order, $total);

            case 'ZaloPay':
                $zalopay = new ZaloPayController();
                return $zalopay->processZaloPay($order, $total, $customer);

            case 'PayPal':
                $paypal = new PayPalController();
                return $paypal->createOrder($order, $total);

            default:
                return $this->error("Phương thức thanh toán không hợp lệ");
        }
    }


    protected function success($order)
    {
        return [
            'data' => [
                'status' => 'success',
                'message' => 'Đặt hàng thành công',
                'order_number' => $order->order_number,
                'total_price' => $order->total_price,
            ],
            'status' => 200
        ];
    }

    protected function error($msg)
    {
        return [
            'data' => [
                'status' => 'error',
                'message' => $msg
            ],
            'status' => 400
        ];
    }
}