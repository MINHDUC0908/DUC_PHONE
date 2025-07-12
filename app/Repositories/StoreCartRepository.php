<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

class StoreCartRepository
{
    public function getOrCreateCart($customerId)
    {
        return Cart::firstOrCreate([
            'customer_id' => $customerId,
            'status' => 'pending',
        ]);
    }

    public function getCartItem($cartId, $productId, $colorId = null)
    {
        return CartItem::where([
            'cart_id' => $cartId,
            'product_id' => $productId,
            'color_id' => $colorId
        ])->first();
    }

    public function createCartItem($data)
    {
        return CartItem::create($data);
    }
}