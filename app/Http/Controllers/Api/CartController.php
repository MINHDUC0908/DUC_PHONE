<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Color;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // public function storeCart(Request $request)
    // {
    //     try {
    //         $productId = $request->product_id;  // ID sản phẩm
    //         $quantity = $request->quantity;  // Số lượng sản phẩm
    //         $colorName = $request->color_id;  // Tên màu, ví dụ "Red", "Black"

    //         // Kiểm tra nếu màu sắc tồn tại trong cơ sở dữ liệu
    //         $color = Color::where('color', $colorName)->first();

    //         if (!$color) {
    //             return response()->json([
    //                 'error' => 'Không tìm thấy màu sắc',
    //                 'message' => "Màu sắc '$colorName' không tồn tại trong hệ thống."
    //             ], 404);
    //         }

    //         // Lấy hoặc tạo mới giỏ hàng của khách hàng
    //         $customer_id = Auth::id();
    //         $cart = Cart::firstOrCreate([
    //             'customer_id' => $customer_id,
    //             'status' => 'pending',
    //         ]);

    //         // Lấy sản phẩm từ database
    //         $product = Product::findOrFail($productId);

    //         // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    //         $existingItem = CartItem::where('cart_id', $cart->id)
    //                                 ->where('product_id', $product->id)
    //                                 ->where('color_id', $color->id)
    //                                 ->first();

    //         if ($existingItem) {
    //             // Nếu sản phẩm đã có trong giỏ hàng, cộng thêm số lượng
    //             $existingItem->quantity += $quantity;
    //             $existingItem->save();
    //         } else {
    //             // Nếu chưa có trong giỏ, thêm sản phẩm mới vào giỏ
    //             CartItem::create([
    //                 'cart_id' => $cart->id,
    //                 'product_id' => $product->id,
    //                 'color_id' => $color->id,
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
    public function storeCart(Request $request)
    {
        try {
            $productId = $request->product_id;  // ID sản phẩm
            $quantity = $request->quantity;  // Số lượng sản phẩm
            $colorName = $request->color_id;  // Tên màu (nếu có)

            // Lấy hoặc tạo mới giỏ hàng của khách hàng
            $customer_id = Auth::id();
            $cart = Cart::firstOrCreate([
                'customer_id' => $customer_id,
                'status' => 'pending',
            ]);

            // Lấy sản phẩm từ database
            $product = Product::findOrFail($productId);

            // Kiểm tra nếu có color_id và thêm màu vào giỏ nếu có
            if ($colorName) {
                // Kiểm tra nếu màu sắc tồn tại trong cơ sở dữ liệu
                $color = Color::where('color', $colorName)->first();

                if (!$color) {
                    return response()->json([
                        'error' => 'Không tìm thấy màu sắc',
                        'message' => "Màu sắc '$colorName' không tồn tại trong hệ thống."
                    ], 404);
                }

                // Kiểm tra xem sản phẩm đã có trong giỏ hàng với màu sắc cụ thể chưa
                $existingItem = CartItem::where('cart_id', $cart->id)
                                        ->where('product_id', $product->id)
                                        ->where('color_id', $color->id)
                                        ->first();
            } else {
                // Nếu không có color_id, không cần kiểm tra màu sắc, thêm vào giỏ hàng mà không có màu sắc
                $existingItem = CartItem::where('cart_id', $cart->id)
                                        ->where('product_id', $product->id)
                                        ->whereNull('color_id')  // Sử dụng `whereNull` nếu không có màu sắc
                                        ->first();
            }

            if ($existingItem) {
                // Nếu sản phẩm đã có trong giỏ hàng, cộng thêm số lượng
                $existingItem->quantity += $quantity;
                $existingItem->save();
            } else {
                // Nếu chưa có trong giỏ, thêm sản phẩm mới vào giỏ
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'color_id' => $colorName ? $color->id : null,  // Nếu có màu sắc thì thêm vào, nếu không thì null
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);
            }

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
                            ->with('cartItems', 'cartItems.product', 'cartItems.colors')
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
            $customer = Auth::id();
            if ($customer)
            {
                $cartItem = CartItem::findOrFail($id);
                if ($request->has('quantity') && $request->quantity > 0)
                {
                    $cartItem->quantity = $request->quantity;
                }
                if ($request->has('selected'))
                {
                    $cartItem->selected = $request->selected;
                }
                $cartItem->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Cart item updated successfully',
                    'data' => $cartItem
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }
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
            $customer = Auth::id();
            if ($customer)
            {
                $cartItem = CartItem::findOrFail($id);
                $cartItem->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item removed from cart successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ], 401);
            }
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
