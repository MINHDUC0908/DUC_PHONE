<?php

namespace App\Repositories;
use App\Models\Cart;
use App\Models\CartItem;

class CartRepository
{
    public function getSelectedItems($customerId)
    {
        // Lấy các item đã được chọn trong giỏ hàng, kèm theo thông tin sản phẩm và màu sắc
        return Cart::where('customer_id', $customerId)  
            ->first()
            ->cartItems()
            ->with('product', 'colors')
            ->where('selected', 1)
            ->get();
    }

    public function calculateTotal($items)
    {
        // Duyệt qua tất cả các item trong giỏ hàng và tính tổng:
        // (giá sản phẩm sau khi giảm * số lượng)
        return $items->sum(fn($item) =>
            $item->product->getDiscountedPrice() * $item->quantity
        );
    }

    public function clearSelectedItems($customerId)
    {
        Cart::where('customer_id', $customerId)->first()
            ->cartItems()->where('selected', 1)->delete();
    }


    public function getCart($customerId)
    {
        return Cart::where("customer_id", $customerId)
                ->where("status", "pending")
                ->with("cartItems", "cartItems.product", "cartItems.colors")
                ->first();
    }

    public function findById($id)
    {
        return CartItem::findOrFail($id);
    }

    public function save($cartItem)
    {
        return $cartItem->save();
    }

    public function findCartItemByCustomer($cartItem, $customer)
    {
        return CartItem::where("id", $cartItem)
                ->whereHas('cart', function ($query) use ($customer) {
                    $query->where('customer_id', $customer);
                })
                ->first();
    }


    public function findCartById($customer)
    {
        return Cart::where('customer_id', $customer)->first();
    }
}
