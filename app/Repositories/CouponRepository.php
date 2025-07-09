<?php

namespace App\Repositories;
use App\Models\Coupon;

class CouponRepository
{
    public function applyCoupon($code, $total)
    {
        $coupon = Coupon::where('code', $code)->where('expires_at', '>', now())->first();
        if (!$coupon) throw new \Exception("Mã giảm giá không hợp lệ");

        // Tính số tiền được giảm: lấy nhỏ nhất giữa số tiền giảm của coupon và tổng đơn hàng
        $discount = min($coupon->discount_amount, $total);
        return [$discount, $coupon];
    }
}
