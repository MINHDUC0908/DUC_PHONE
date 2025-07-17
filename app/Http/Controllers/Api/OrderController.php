<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function updateQuantity(Request $request, $id)
    {
        try {
            return $this->cartService->updateQuantity($request, $id);
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
            return $this->cartService->updateSelectedItems($request, $id);
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
            return $this->cartService->updateAllSelectedItems($request);
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
            return response()->json([
                'message' => 'Lỗi khi hủy đơn hàng',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
