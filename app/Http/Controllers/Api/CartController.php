<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\Product;
use App\Services\CartService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // public function storeCart(Request $request)
    // {
    //     try {
    //         // Lấy hoặc tạo mới giỏ hàng của khách hàng
    //         $customer_id = Auth::id();
    //         $cart = Cart::firstOrCreate([
    //             'customer_id' => $customer_id,
    //             'status' => 'pending',
    //         ]);
    //         $productId = $request->product_id;  // ID sản phẩm
    //         $quantity = $request->quantity;  // Số lượng sản phẩm
    //         $color_id = $request->color_id;  // Tên màu (nếu có)
    //         $product = Product::with('colors')->findOrFail($productId);
    //         // Lấy đúng `color_id` thuộc sản phẩm
    //         $color = null;
    //         if ($color_id) {
    //             $color = $product->colors()->where('color', $color_id)->first();
    //             Log::info($color);
    //             if (!$color) {
    //                 return response()->json([
    //                     'error' => 'Không tìm thấy màu sắc cho sản phẩm',
    //                     'message' => "Màu '$color_id' không tồn tại cho sản phẩm này."
    //                 ], 404);
    //             }
    //         }

    //         // Tìm sản phẩm trong giỏ hàng với màu sắc tương ứng (nếu có)
    //         $existingItem = CartItem::where([
    //                             'cart_id' => $cart->id,
    //                             'product_id' => $product->id,
    //                             'color_id' => $color ? $color->id : null
    //                         ])->first();
    //         if ($existingItem) {
    //             // Nếu sản phẩm đã tồn tại trong giỏ hàng, tăng số lượng
    //             $existingItem->increment('quantity', $quantity);
    //         } else {
    //             // Nếu chưa có, thêm sản phẩm mới vào giỏ hàng
    //             CartItem::create([
    //                 'cart_id' => $cart->id,
    //                 'product_id' => $product->id,
    //                 'color_id' => $color ? $color->id : null,
    //                 'quantity' => $quantity,
    //                 'price' => $product->price,
    //             ]);
    //         }
    //         return response()->json([
    //             'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
    //             'data' => $cart,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => 'Đã có lỗi xảy ra khi thêm vào giỏ hàng.',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function storeCart(Request $request)
    {
        try {
            $cart = $this->cartService->addToCart($request);

            return response()->json([
                'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
                'data' => $cart,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra khi thêm vào giỏ hàng.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function viewCart()
    {
        try {
            return $this->cartService->getCart();
        } catch (Exception $e)
        {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra khi lấy giỏ hàng',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewCartPayment()
    {
        try {
            $customer = Auth::id();
            if (!$customer)
            {
                return response()->json([
                    'error' => 'Người dùng chưa đăng nhập',
                    'message' => 'Vui lòng đăng nhập để xem giỏ hàng',
                ]);
            } else {
                $cart = Cart::where('customer_id', $customer)
                    ->where('status', 'pending')
                    ->with(['cartItems' => function($query) {
                        // Lọc các cartItems có selected = 1
                        $query->where('selected', 1);
                    }, 'cartItems.product', 'cartItems.colors'])
                    ->first();

                if (!$cart)
                {
                    return response()->json([
                        'message' => 'Giỏ hàng trống',
                        'data' => []
                    ]);
                } else {
                    return response()->json([
                        'message' => 'Giỏ hàng của bạn',
                        'data' => $cart,
                    ]);
                }
            }
        } catch (Exception $e)
        {
            return response()->json([
                'error' => 'Đã có lỗi xảy ra khi lấy giỏ hàng',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function countCart()
    {
        try {
            // Kiểm tra xem người dùng đã đăng nhập chưa
            $customer = Auth::id();
            if ($customer) {
                $countCart = Cart::where('customer_id', $customer)
                                ->withCount('cartItems')
                                ->first();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cart count retrieved successfully',
                    'countCart' => $countCart ? $countCart->cart_items_count : 0,
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is not authenticated',
                    'countCart' => 0,
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
                'countCart' => 0,
            ], 500);
        }
    }
    public function update($id, Request $request)
    {
        try {
            return $this->cartService->update($id, $request);
        } catch (Exception $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function deleteAll($id)
    {

        try {
            $customer = Auth::id();
            if (!$customer)
            {
                return response()->json([
                    'message' => 'Người dùng chưa đăng nhập',
                ]);
            }
            $cart = Cart::findOrFail($id);
            $cart->delete();
            return response()->json([
                'message' => 'Xóa thành công',
            ]);
        } catch (Exception $e)
        {

        }
    }
        public function delete($id)
    {
        try {
            $result = $this->cartService->deleteCartItem($id);
            return $result;
        } catch (ModelNotFoundException $e) {
            // Nếu không tìm thấy sản phẩm
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found.',
            ], 404);  // 404 Not Found
        } catch (Exception $e) {
            // Xử lý các lỗi khác
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);  // 500 Internal Server Error
        }
    }
}
