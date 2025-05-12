<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZaloPayController extends Controller
{
    public function processZaloPay($order, $totalPrice, $customer_id)
    {
        try {
            $app_id = config('zalopay.app_id');
            $key1 = config('zalopay.key1');
            $endpoint = config('zalopay.endpoint');
            
            $customer = Customer::find($customer_id);

            // Cập nhật lại order_number
            $app_trans_id = now()->format('ymd') . '_' . $order->id . rand(1000, 9999);
            $order->order_number = $app_trans_id;
            $order->save();

            $app_time = round(microtime(true) * 1000);

            $embed_data = [
                "redirecturl" => route("zalopay.return")
            ];

            $item = json_encode([]);

            $data = implode("|", [
                $app_id,
                $app_trans_id,
                $customer->email,
                $totalPrice,
                $app_time,
                json_encode($embed_data),
                $item
            ]);

            $mac = hash_hmac("sha256", $data, $key1);

            $params = [
                "app_id" => $app_id,
                "app_trans_id" => $app_trans_id,
                "app_user" => $customer->email,
                "app_time" => $app_time,
                "amount" => (int)$totalPrice,
                "item" => $item,
                "embed_data" => json_encode($embed_data),
                "description" => "Thanh toán đơn hàng #" . $order->order_number,
                "bank_code" => "",
                "callback_url" => route("zalopay.return"),
                "mac" => $mac
            ];

            // Lưu thông tin thanh toán
            Payment::create([
                'order_id' => $order->id,
                'payment_gateway' => 'ZaloPay',
                'transaction_id' => $order->order_number,
                'amount' => $totalPrice,
                'status' => 'pending'
            ]);

            $response = Http::asForm()->post($endpoint, $params);
            $result = $response->json();

            if (isset($result['return_code']) && $result['return_code'] == 1) {
                return response()->json([
                    'status' => 'success',
                    'zalopay_url' => $result['order_url'] ?? null,
                    'app_trans_id' => $app_trans_id
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['return_message'] ?? 'Không thể tạo thanh toán ZaloPay'
            ], 400);
        } catch (\Exception $e) {
            Log::error('ZaloPay Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tạo thanh toán ZaloPay'
            ], 500);
        }
    }

    public function zalopayReturn(Request $request)
    {
        try {
            $app_id = config('zalopay.app_id');
            $key1 = config('zalopay.key1');
            $endpoint = config('zalopay.endpoint');

            $app_trans_id = $request->input('apptransid');
            if (!$app_trans_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Thiếu thông tin giao dịch'
                ], 400);
            }

            // Tạo dữ liệu gửi đi và mã hóa MAC
            $data = [
                "app_id" => $app_id,
                "app_trans_id" => $app_trans_id
            ];
            $data_string = "{$app_id}|{$app_trans_id}|{$key1}";
            $data['mac'] = hash_hmac("sha256", $data_string, $key1);

            // Gửi yêu cầu kiểm tra trạng thái giao dịch
            $response = Http::asForm()->post($endpoint, $data);
            $result = $response->json();


            $order = Order::where('order_number', $app_trans_id)->first();
            
            // Nếu giao dịch thành công
            if ($response->successful() && $result['return_code'] == 1) {
                if ($order) {
                    $order->payment_status = 'paid';
                    $order->status = 'completed';
                    $order->save();

                    $payment = Payment::where('transaction_id', $app_trans_id)->first();
                    if ($payment) {
                        $payment->status = 'success';
                        $payment->save();
                    }
                    // Xóa các sản phẩm đã thanh toán khỏi giỏ hàng
                    $cart = Cart::where('customer_id', $order->customer_id)->first();
                    if ($cart) {
                        $cart->cartItems()->where("checked", 1)->delete();
                    }
                    return redirect()->away("http://localhost:5173/payment/success?transaction_id=$app_trans_id&total_amount={$order->total}");
                }
                return redirect()->away("http://localhost:5173/payment/failed?message=Không tìm thấy đơn hàng");
            }

            // Nếu thất bại, xóa đơn hàng và payment nếu có
            if ($order) {
                $order->forceDelete();
            }
            return response()->json([
                'message' => "Thanh toán thất bại"
            ]);
        } catch (\Exception $e) {
            Log::error("ZaloPay return error: " . $e->getMessage());

            return redirect()->away("http://localhost:5173/payment/failed?message=Có lỗi xảy ra khi xử lý thanh toán");
        }
    }
}
