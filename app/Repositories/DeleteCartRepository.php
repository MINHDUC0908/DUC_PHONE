<?php

namespace App\Repositories;

use App\Models\CartItem;

class DeleteCartRepository
{
    public function deleteCart($cartId)
    {
        if (!$cartId) {
            throw new \Exception("Cart ID không được để trống");
        }
        $cartItem = CartItem::find($cartId);

        return $cartItem->delete();
    }
}