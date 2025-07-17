<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Repositories\CartRepository;
use App\Repositories\DeleteCartRepository;
use App\Repositories\StoreCartRepository;
use Illuminate\Support\Facades\Auth;
use Exception;

class CartService
{
    protected $getCartRepository;
    protected $cartRepo;
    protected $deleteCartRepo;

    public function __construct(StoreCartRepository $cartRepo, DeleteCartRepository $deleteCartRepo, CartRepository $getCartRepository)
    {
        $this->cartRepo = $cartRepo;
        $this->deleteCartRepo = $deleteCartRepo; 
        $this->getCartRepository = $getCartRepository;
    }

    public function addToCart($request)
    {
        $customerId = Auth::id();
        $cart = $this->cartRepo->getOrCreateCart($customerId);  

        $product = Product::with('colors')->findOrFail($request->product_id);

        // Tìm color (nếu có)
        $colorId = null;
        if ($request->color_id) {
            $color = $product->colors()->where('color', $request->color_id)->first();
            // SELECT * FROM colors WHERE product_id = 1 AND color = 'Đỏ' LIMIT 1;

            if (!$color) {
                throw new \Exception("Màu '{$request->color_id}' không tồn tại cho sản phẩm này.");
            }

            $colorId = $color->id;
        }

        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $existingItem = $this->cartRepo->getCartItem($cart->id, $product->id, $colorId);

        if ($existingItem) {
            $existingItem->increment('quantity', $request->quantity);
        } else {
            $this->cartRepo->createCartItem([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'color_id' => $colorId,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return $cart;
    }


    public function deleteCartItem($cartId)
    {
        $customerId = Auth::id();

        if (!$customerId) {
            throw new Exception("Bạn cần đăng nhập để xóa sản phẩm khỏi giỏ hàng.");
        }
        $cart = $this->deleteCartRepo->deleteCart($cartId, $customerId);

        return response()->json([
            'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng',
            "status" => "success",
            'data' => $cart,
        ]);
    }



    public function getCart()
    {
        $customerId = Auth::id();
        if (!$customerId) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để xem giỏ hàng.',
                'data' => null,
            ], 401);
        }
        $cart = $this->getCartRepository->getCart($customerId);
        if (!$cart) {
            return response()->json([
                'message' => 'Giỏ hàng trống',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Giỏ hàng của bạn',
            'data' => $cart,
        ], 200);
    }


    public function update($cartItemId, $request)
    {
        $customerId = Auth::id();
        if (!$customerId)
        {
            return response()->json([
                'error' => 'Bạn cần đăng nhập để cập nhật giỏ hàng.',
                'message' => 'Vui lòng đăng nhập để tiếp tục.',
            ], 401);
        }
        $cartItem = $this->getCartRepository->findById($cartItemId);
        if (!$cartItem) {
            return response()->json([
                'error' => 'Thông tin không đầy đủ.',
                'message' => 'Vui lòng cung cấp ID sản phẩm và số lượng.',
            ], 400);
        }

        if ($request->has("quantity") && $request->quantity > 0)
        {
            $cartItem->quantity = $request->quantity;
        }

        if ($request->has('selected')) {
            $cartItem->selected = $request->selected;
        }

        $this->getCartRepository->save($cartItem);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item updated successfully',
            'data' => $cartItem
        ]);
    }



    public function updateQuantity($request, $id)
    {
        $customerId = Auth::id();
        if (!$customerId) {
            return response()->json([
                'message' => 'Bạn cần đăng nhập để cập nhật số lượng sản phẩm.',
            ], 401);
        }
        // Xác thực dữ liệu từ request
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = $this->getCartRepository->findCartItemByCustomer($id, $customerId);
        if (!$cartItem) {
            return response()->json([
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
            ], 404);
        }

        if ($cartItem->color_id) {
            $color = $cartItem->product->colors()->where('id', $cartItem->color_id)->first();
            if ($validatedData['quantity'] > $color->quantity) {
                return response()->json([
                    'message' => 'Số lượng sản phẩm trong màu sắc này không đủ.',
                ], 400);
            }
        }

        // Cập nhật số lượng
        $cartItem->quantity = $validatedData['quantity'];
        $this->getCartRepository->save($cartItem);

        return response()->json([
            'message' => 'Cập nhật số lượng thành công.',
            'data' => $cartItem,
        ]);
    }



    public function updateSelectedItems($request, $id)
    {
        $customer = Auth::id();
        if (!$customer) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập tài khoản',
            ], 401);
        }

        $cartItem = $this->getCartRepository->findById($id);

        if (!$cartItem) {
            return response()->json([
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.',
            ], 404);
        }
        $cartItem->selected = $request->input('selected', 0);
        $this->getCartRepository->save($cartItem);

        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $cartItem,
        ]);
    }



    public function updateAllSelectedItems($request)
    {
        $customer = Auth::id();
        if (!$customer) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập tài khoản',
            ], 401);
        }
        $cart = $this->getCartRepository->findCartById($customer);
        $cart->cartItems()->update(['selected' => $request->input('selected', 0)]);
        $cartItems = $cart->cartItems()->with(['product', 'colors'])->get();
        return response()->json([
            'message' => 'Cập nhật tất cả sản phẩm thành công.',
            'data' => $cartItems,
        ]);
    }
}
