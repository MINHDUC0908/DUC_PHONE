<?php

namespace App\Repositories;
use App\Models\Cart;

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
}
