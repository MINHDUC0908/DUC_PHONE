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
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input("code");
        $coupon = Coupon::where('code', $couponCode)
            ->where('expires_at', '>', now()) // Kiểm tra hạn sử dụng
            ->where('is_used', false) // Chưa sử dụng
            ->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'
            ], 400);
        }
        $customer = Auth::id();
        if (UsedCoupon::where("customer_id", $customer)->where("coupon_id", $coupon->id)->exists()) {
            return response()->json([
                'message' => 'Mã giảm giá đã được sử dụng!'
            ], 400);
        }
        // Tính số tiền giảm
        $discount = min($coupon->discount_amount, $request->cart_total);
        return response()->json([
            'success' => true,
            'discount' => $discount,
        ]);
    }
    public function checkout(Request $request)
    {
        try {
            // Kiểm tra đăng nhập
            $customer = Auth::id();
            if (!$customer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Người dùng chưa đăng nhập'
                ], 401);
            }

            // Xác thực địa chỉ giao hàng
            $shippingAddressId = $request->shipping_address_id;
            $shippingAddress = ShippingAddress::where('customer_id', $customer)
                ->where('id', $shippingAddressId)
                ->first();

            if (!$shippingAddress) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Địa chỉ giao hàng không hợp lệ'
                ], 400);
            }

            // Lấy các sản phẩm trong giỏ hàng
            $cart = Cart::where('customer_id', $customer)->first();
            if (!$cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có sản phẩm trong giỏ hàng'
                ], 400);
            }

            $cartItems = $cart->cartItems()->with(['product', 'colors'])->where('selected', 1)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giỏ hàng trống'
                ], 400);
            }
            
            // Tính tổng giá trị đơn hàng
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                if ($item->product) {
                    $totalPrice += $item->product->getDiscountedPrice() * $item->quantity;
                }
            }
            
            if ($totalPrice <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giá trị đơn hàng không hợp lệ'
                ], 400);
            }

            $paymentMethod = $request->input('payment_method', 'Cod');
            

            $couponCode = $request->input('coupon'); // Mã giảm giá (nếu có)
            $discount = 0;
            $coupon = null;
            if ($couponCode)
            {
                $coupon = Coupon::where('code', $couponCode)
                        ->where('expires_at', '>', now())
                        ->where('is_used', false)
                        ->first();
                if (!$coupon) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!'
                    ], 400);
                }
                // Tính số tiền giảm giá, không vượt quá tổng giá trị đơn hàng
                $discount = min($coupon->discount_amount, $totalPrice);

                // Cập nhật tổng giá trị sau khi giảm giá
                $totalPrice -= $discount;
            }
            // Tạo đơn hàng trong database
            DB::beginTransaction();
            $order = Order::create([
                'customer_id' => $customer,
                'order_number' => 'ORDER_' . uniqid(),
                'coupon_id' => $coupon ? $coupon->id : null,
                'total_price' => $totalPrice,
                'status' => 'Waiting for confirmation',
                'shipping_address_id' => $shippingAddress->id,
                "discount_amount" => $discount,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'Cod' ? 'Unpaid' : 'Paid', 
            ]);
            foreach ($cartItems as $item) {
                if ($item->product) { // Đảm bảo sản phẩm tồn tại
                    $product = $item->product;
            
                    // Kiểm tra nếu sản phẩm có màu sắc (nếu có thì kiểm tra số lượng theo màu sắc)
                    if ($item->color_id) {
                        $color = $product->colors()->where('id', $item->color_id)->first();
            
                        if (!$color || $color->quantity < $item->quantity) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Số lượng màu sắc không đủ'
                            ], 400);
                        }
            
                        // Trừ số lượng trong bảng colors
                        $color->quantity -= $item->quantity;
                        $color->save();
                    } else {
                        // Nếu không có màu sắc
                        $color = $product->colors()->first();
            
                        if (!$color || $color->quantity < $item->quantity) {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Số lượng sản phẩm không đủ'
                            ], 400);
                        }
                        $color->quantity -= $item->quantity;
                        $color->save();
                    }
            
                    // Thêm mục đơn hàng vào bảng order_items
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'color_id' => $item->color_id, 
                        'quantity' => $item->quantity,
                        'price' => $item->product->price
                    ]);
                    if ($coupon) {
                        UsedCoupon::create([
                            'customer_id' => $customer,
                            'coupon_id' => $coupon->id
                        ]);
                    }                    
                }
            }
            $cartItemsArray = $cartItems->toArray();
            if ($paymentMethod === 'cod') {
                Log::debug("Vào được if Cod");
                Mail::to($request->user()->email)
                    ->queue((new OrderConfirmation($order, $cartItemsArray, $totalPrice))->delay(now()->addSeconds(5)));
            }
            
            DB::commit();

            // Xử lý VNPay nếu chọn phương thức thanh toán online
            if ($paymentMethod === 'Online') {
                $order->payment_method = 'Online';
                $order->save();
                return $this->processVNPay($order, $totalPrice);
            }

            // Xóa giỏ hàng sau khi đặt hàng thành công
            $cart->cartItems()->where('selected', 1)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order_number' => $order->order_number,
                    'total_price' => $order->total_price
                ]
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::debug($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xử lý đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function processVNPay($order, $totalPrice)
    {
        // Thông tin cấu hình VNPay
        $vnp_TmnCode = "79J37G5G";  // Mã website của bạn
        $vnp_HashSecret = "G6RRX221335F3YUNDITPW1UO6BIBSRH1";  // Chuỗi bí mật
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";  // URL của VNPay
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
        return response()->json([
            'status' => 'success',
            'vnpay_url' => $vnp_Url
        ]);
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

            if ($vnp_ResponseCode == '00') {
                $payment = Payment::where('transaction_id', $vnp_TxnRef)->first();
                
                if ($payment) {
                    DB::beginTransaction();
                    try {
                        // Update payment status
                        $payment->status = 'success';
                        $payment->save();

                        // Update order status
                        $order = Order::find($payment->order_id);
                        if ($order) {
                            $order->payment_status = 'paid';
                            $order->save();
                        } else {
                            throw new \Exception('Order not found');
                        }
                        $cart = Cart::where('customer_id', $order->customer_id)->first();
                        Log::debug('Cart: ' . $cart); 
                        if ($cart) {
                            $cartItemsArray = $cart->cartItems()->where('selected', 1)->get()->toArray();
                            $cart->cartItems()->where('selected', 1)->delete();
                        }
                        
                        Mail::to($order->customer->email)
                        ->queue(new OrderConfirmation($order, $cartItemsArray, $payment->amount));


                        DB::commit();
                        return redirect("http://localhost:5173/profiles/Delivery-history")
                            ->with('success', 'Payment processed successfully');
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('VNPay payment processing error: ' . $e->getMessage());
                    }
                } else {
                    Log::error('Payment record not found for transaction: ' . $vnp_TxnRef);
                    return redirect('http://localhost:5173/payment/failed')
                        ->with('error', 'Payment record not found');
                }
            } else {
                $payment = Payment::where('transaction_id', $vnp_TxnRef)->first();
                Log::info('Payment:', [$payment]);
                if ($payment) {
                    $payment->status = 'failed';
                    $payment->save();

                    $order = Order::find($payment->order_id);
                    Log::info($order);
                    if ($order) {
                        $order->forceDelete();
                        Log::debug("Xóa thành công");
                    }
                }
                return redirect('http://localhost:5173/payment')
                    ->with('error', 'Payment was not successful');
            }
        } else {
            return redirect('http://localhost:5173/payment/failed')
                ->with('error', 'Invalid payment response');
        }
    } 
}
