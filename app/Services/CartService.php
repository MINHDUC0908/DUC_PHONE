<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\DeleteCartRepository;
use App\Repositories\StoreCartRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;

class CartService
{
    protected $cartRepo;
    protected $deleteCartRepo;

    public function __construct(StoreCartRepository $cartRepo, DeleteCartRepository $deleteCartRepo)
    {
        $this->cartRepo = $cartRepo;
        $this->deleteCartRepo = $deleteCartRepo; 
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
}
