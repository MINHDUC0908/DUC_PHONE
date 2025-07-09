<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Models\UsedCoupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VNPayController extends Controller
{
    public function processVNPay($order, $totalPrice)
    {
        // Thông tin cấu hình VNPay
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');        
        $vnp_ReturnUrl = route('vnpay.return');  // URL trả về sau khi thanh toán
    
        // Lưu thời gian tạo đơn hàng
        $order->created_at = now();
        $order->save();

        // Tạo dữ liệu cho request thanh toán
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $totalPrice * 100,  // Số tiền thanh toán (VND)
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toán đơn hàng: " . $order->order_number,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $order->order_number,  // Mã đơn hàng
        );
    
        // Sắp xếp lại các tham số theo thứ tự alphabe
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
    
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
    
        // Tạo URL thanh toán
        $vnp_Url = $vnp_Url . "?" . $query;
    
        // Tính toán vnp_SecureHash
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
    
        // Lưu thông tin thanh toán vào cơ sở dữ liệu
        Payment::create([
            'order_id' => $order->id,
            'payment_gateway' => 'VNPay',
            'transaction_id' => $order->order_number,
            'amount' => $totalPrice,
            'status' => 'pending'  // Trạng thái chờ xử lý
        ]);
    
        // Trả về URL của VNPay để khách hàng thanh toán
        // return response()->json([
        //     'status' => 'success',
        //     'vnpay_url' => $vnp_Url
        // ]);
        return [
            'data' => [
                'status' => 'success',
                'vnpay_url' => $vnp_Url
            ],
            'status' => 200
        ];
    }
    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $vnp_HashSecret = "G6RRX221335F3YUNDITPW1UO6BIBSRH1";
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            $vnp_ResponseCode = $request->vnp_ResponseCode;
            $vnp_TxnRef = $request->vnp_TxnRef;
                Log::debug($vnp_TxnRef . " vnp_TxnRef");
                Log::debug($vnp_ResponseCode . " vnp_ResponseCode");
            if ($vnp_ResponseCode == '00') {
                $order = Order::where('order_number', $vnp_TxnRef)->first();
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->status = 'Processing';
                    $order->save();

                    $payment = Payment::where('transaction_id', $vnp_TxnRef)->first();
                    if ($payment) {
                        $payment->status = 'success';
                        $payment->save();
                    }
                    // Xóa các cartItems đã được thanh toán
                    $customer_id = $order->customer_id;
                    Log::debug($customer_id);
                    $cart = Cart::where('customer_id', $customer_id)->first();
                    if ($cart) {
                        $cart->cartItems()->where("selected", 1)->delete();
                    }

                    return redirect()->away("http://localhost:5173/payment/success?transaction_id=$vnp_TxnRef&total_amount={$order->total_price}");
                } else {
                    return redirect()->away("http://localhost:5173/payment/failed?message=Đơn hàng không tồn tại");
                }
            } else {
                $order = Order::where('order_number', $vnp_TxnRef)->first();
                Log::debug($order . " order");
                if ($order) {
                    // Xóa đơn hàng
                    $order->forceDelete();

                    // Xóa thông tin thanh toán liên quan
                    $payment = Payment::where('transaction_id', $vnp_TxnRef)->first();
                    if ($payment) {
                        $payment->delete();
                    }

                    return redirect()->away("http://localhost:5173/payment/failed?message=Thanh toán thất bại");
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Đơn hàng không tồn tại'
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ]);
        }
    }
}
