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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
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
            if ($cartItem->color_id)
            {
                $color = $cartItem->product->colors()->where('id', $cartItem->color_id)->first();
                Log::debug($color);
                if ($validatedData['quantity'] > $color->quantity) {
                    return response()->json([
                        'message' => 'Số lượng sản phẩm trong màu sắc này không đủ.',
                    ], 400);
                }
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
            $customer = Auth::id();
            $orders = Order::with('customer', 'shippingAddress')->where("customer_id", $customer)
                ->orderBy('id', 'DESC')
                ->get();
                // ->paginate(15);
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
            $customer = Auth::id();
            $validStatuses = ['Waiting for confirmation', 'Processing', 'Delivering', 'Completed', 'Cancel'];
            if (!in_array($status, $validStatuses)) {
                return response()->json([
                    'message' => 'Trạng thái không hợp lệ.',
                ], 400);
            }
            $orders = Order::with('customer', 'shippingAddress')->where("customer_id", $customer)
                ->where('status', $status)
                ->orderBy('id', 'DESC')
                // ->paginate(5);
                ->get();
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
                'status' => 'success',
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
