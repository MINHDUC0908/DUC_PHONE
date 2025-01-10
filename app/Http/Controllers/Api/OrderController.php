<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
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

            // Bắt đầu giao dịch
            DB::beginTransaction();

            // Tính tổng giá trị đơn hàng
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                if ($item->product) { // Đảm bảo sản phẩm tồn tại
                    $totalPrice += $item->product->price * $item->quantity;
                }
            }
            if ($totalPrice <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Giá trị đơn hàng không hợp lệ'
                ], 400);
            }

            // Tạo đơn hàng
            $order = Order::create([
                'customer_id' => $customer,
                'order_number' => 'ORDER_' . uniqid(),
                'total_price' => $totalPrice,
                'status' => 'Waiting for confirmation',
                'shipping_address_id' => $shippingAddress->id
            ]);

            // // Tạo các mục trong đơn hàng và trừ số lượng sản phẩm theo màu sắc
            // foreach ($cartItems as $item) {
            //     if ($item->product) { // Đảm bảo sản phẩm tồn tại
            //         $product = $item->product;

            //         // Kiểm tra số lượng sản phẩm trong màu sắc tương ứng
            //         $color = $item->product->colors()->where('id', $item->color_id)->first();

            //         if (!$color || $color->quantity < $item->quantity) {
            //             // Nếu số lượng màu sắc không đủ, hủy đơn hàng hoặc thông báo lỗi
            //             DB::rollBack();
            //             return response()->json([
            //                 'status' => 'error',
            //                 'message' => 'Số lượng màu sắc không đủ'
            //             ], 400);
            //         }

            //         // Trừ số lượng trong bảng colors
            //         $color->quantity -= $item->quantity;
            //         $color->save();

            //         // Thêm mục đơn hàng vào bảng order_items
            //         OrderItem::create([
            //             'order_id' => $order->id,
            //             'product_id' => $item->product_id,
            //             'color_id' => $item->color_id,
            //             'quantity' => $item->quantity,
            //             'price' => $item->product->price
            //         ]);
            //     }
            // }
            foreach ($cartItems as $item) {
                if ($item->product) { // Đảm bảo sản phẩm tồn tại
                    $product = $item->product;
            
                    // Kiểm tra nếu sản phẩm có màu sắc (nếu có thì kiểm tra số lượng theo màu sắc)
                    if ($item->color_id) {
                        // Sản phẩm có màu sắc, kiểm tra số lượng trong bảng colors
                        $color = $product->colors()->where('id', $item->color_id)->first();
            
                        if (!$color || $color->quantity < $item->quantity) {
                            // Nếu số lượng màu sắc không đủ, hủy đơn hàng hoặc thông báo lỗi
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
                        // Sản phẩm không có màu sắc, tìm một màu mặc định hoặc trừ số lượng trong bảng colors
                        // Nếu không có màu sắc, kiểm tra trực tiếp trên bảng colors mà không cần điều kiện màu sắc
                        $color = $product->colors()->first(); // Lấy bất kỳ màu sắc nào (kể cả null nếu có)
            
                        if (!$color || $color->quantity < $item->quantity) {
                            // Nếu không có màu sắc hoặc số lượng không đủ, hủy đơn hàng hoặc thông báo lỗi
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Số lượng sản phẩm không đủ'
                            ], 400);
                        }
            
                        // Trừ số lượng trong bảng colors (cho sản phẩm không có màu sắc rõ ràng)
                        $color->quantity -= $item->quantity;
                        $color->save();
                    }
            
                    // Thêm mục đơn hàng vào bảng order_items
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'color_id' => $item->color_id, // Nếu không có màu sắc, sẽ là null
                        'quantity' => $item->quantity,
                        'price' => $item->product->price
                    ]);
                }
            }

            // Gửi email xác nhận đơn hàng
            Mail::to($request->user()->email)->send(new OrderConfirmation($order, $cartItems, $totalPrice));

            // Xóa giỏ hàng sau khi đặt hàng thành công
            $cart->cartItems()->where('selected', 1)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order_number' => $order->order_number,
                    'total_price' => $order->total_price
                ]
            ], 200);
        } catch (Exception $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi xử lý đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        try {
            // Kiểm tra xem người dùng đã đăng nhập hay chưa
            $customer = Auth::id();
            if (!$customer) {
                return response()->json([
                    'message' => 'Vui lòng đăng nhập để tiếp tục.',
                ], 401);
            }

            // Xác thực dữ liệu từ request
            $validatedData = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            // Tìm cart item dựa trên id và customer_id
            $cartItem = CartItem::where('id', $id)
                ->whereHas('cart', function ($query) use ($customer) {
                    $query->where('customer_id', $customer);
                })
                ->first();

            // Kiểm tra nếu không tìm thấy cart item
            if (!$cartItem) {
                return response()->json([
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
                ], 404);
            }
            $color = $cartItem->product->colors()->where('id', $cartItem->color_id)->first();
            if ($validatedData['quantity'] > $color->quantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm trong màu sắc này không đủ.',
                ], 400);
            }
            // Cập nhật số lượng
            $cartItem->quantity = $validatedData['quantity'];
            $cartItem->save();

            return response()->json([
                'message' => 'Cập nhật số lượng thành công.',
                'data' => $cartItem,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateOrder(Request $request, $id)
    {
        try {
            $customer = Auth::id();
            if (!$customer) {
                return response()->json([
                    'message' => 'Vui lòng đăng nhập tài khoản',
                ], 401);
            }
            $cartItem = CartItem::findOrFail($id);
            $cartItem->selected = $request->input('selected', 0);
            $cartItem->save();
    
            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $cartItem,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình xử lý',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateAllOrders(Request $request)
    {
        try {
            $customer = Auth::id();
            $cart = Cart::where('customer_id', $customer)->first();
            
            if (!$cart) {
                return response()->json([
                    'message' => 'Không tìm thấy giỏ hàng.',
                ], 404);
            }
            
            // Cập nhật các cart_items thuộc cart này
            $selected = $request->input('selected', 0);
            CartItem::where('cart_id', $cart->id)
                ->update(['selected' => $selected]);
            
            // Lấy lại các cart items sau khi update
            $cartItems = CartItem::where('cart_id', $cart->id)
                ->with(['product', 'colors'])
                ->get();
            
            return response()->json([
                'message' => 'Cập nhật tất cả sản phẩm thành công.',
                'data' => $cartItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function order()
    {
        try {
            $orders = Order::with('customer', 'shippingAddress')
                ->orderBy('id', 'DESC')
                // ->get();
                ->paginate(15);
            if ($orders->isEmpty()) {
                return response()->json([
                    'message' => 'Không có đơn hàng chưa hoàn thành.',
                ], 404);
            }
    
            return response()->json([
                'message' => 'Lấy đơn hàng chưa hoàn thành thành công',
                'data' => $orders,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy dữ liệu đơn hàng',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrdersByStatus($status)
    {
        try {
            $validStatuses = ['Waiting for confirmation', 'Processing', 'Delivering', 'Completed', 'Cancel'];
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ.',
                ], 400);
            }
            $orders = Order::with('customer', 'shippingAddress')
                ->where('status', $status)
                ->orderBy('id', 'DESC')
                ->paginate(5);

            return response()->json([
                'message' => "Lấy đơn hàng có trạng thái '$status' thành công",
                'data' => $orders,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy dữ liệu đơn hàng',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelOrder($id)
    {
        try {
            $order = Order::findOrFail($id);
            if ($order->status === 'Cancel') {
                return response()->json([
                    'message' => 'Đơn hàng đã bị hủy trước đó.',
                ], 400);
            }
            $order->status = 'Cancel';
            $order->save();

            return response()->json([
                'message' => 'Hủy đơn hàng thành công',
                'data' => $order,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng',
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi hủy đơn hàng',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function showOrder($id)
    {
        try {
            $orderItems = OrderItem::with(['order', 'product', 'color'])
            ->where('order_id', $id)
            ->get();
            return response()->json([
                'message' => 'Chi tiết đơn hàng',
                'data' => $orderItems,
            ]);
        } catch (Exception $e)
        {

        }
    }
}
