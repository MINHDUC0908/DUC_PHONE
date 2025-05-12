<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\UsedCoupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckOutController extends Controller
{
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->input("code");
        $coupon = Coupon::where('code', $couponCode)
            ->where('expires_at', '>', now()) // Kiểm tra hạn sử dụng
            ->first();
        $couponQuantity = Coupon::where('code', $couponCode)
                        ->where('quantity', ">", "0")
                        ->first();
        if (!$couponQuantity)
        {
            return response()->json([
                'message' => "Đã hết số lượng cho mã giảm giá này",
            ]);
        }
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
            if ($coupon && $coupon->quantity > 0)
            {   
                $coupon->quantity -=1;
                $coupon->save();
            }
            $cartItemsArray = $cartItems->toArray();
            if ($paymentMethod === 'cod') {
                Mail::to($request->user()->email)
                    ->queue((new OrderConfirmation($order, $cartItemsArray, $totalPrice))->delay(now()->addSeconds(5)));

                                 
                DB::commit();
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
            }

            // Xử lý VNPay nếu chọn phương thức thanh toán online
            if ($paymentMethod === 'Online') {
                $order->payment_method = 'Online';
                $order->save();
                DB::commit();
                $vnpay = new VNPayController();
                return $vnpay->processVNPay($order, $totalPrice);
            } else if ($paymentMethod === "ZaloPay")
            {
                $order->payment_method = 'ZaloPay';
                $order->save();
                DB::commit();
                $zalopay = new ZaloPayController();
                return $zalopay->processZaloPay($order, $totalPrice, $customer);
            } else if ($paymentMethod === "PayPal")
            {
                $order->payment_method = 'PayPal';
                $order->save();
                DB::commit();
                $paypal = new PayPalController();
                return $paypal->createOrder($order, $totalPrice);
            }
            else {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phương thức thanh toán không hợp lệ'
                ], 400);
            }
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
}
