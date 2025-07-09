<?php

namespace App\Repositories;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UsedCoupon;
use Illuminate\Support\Facades\Mail;

class OrderRepository
{
    public function createOrder($customerId, $addressId, $total, $method, $discount, $coupon)
    {
        return Order::create([
            'customer_id' => $customerId,
            'order_number' => 'ORDER_' . uniqid(),
            'coupon_id' => $coupon?->id,
            'total_price' => $total,
            'status' => 'Waiting for confirmation',
            'shipping_address_id' => $addressId,
            'discount_amount' => $discount,
            'payment_method' => $method,
            'payment_status' => $method === 'Cod' ? 'Unpaid' : 'Paid',
        ]);
    }

    public function addItemsAndUpdateStock($order, $items, $coupon, $customerId)
    {
        // Duyệt qua từng sản phẩm trong giỏ hàng đã chọn
        foreach ($items as $item) {
            // ✅ Lấy thông tin màu sản phẩm được chọn (nếu có color_id),
            // nếu không có thì lấy màu đầu tiên mặc định
            $color = $item->color_id
                ? $item->product->colors()->where('id', $item->color_id)->first()
                : $item->product->colors()->first();

            // ❌ Nếu không có màu hoặc số lượng tồn kho không đủ -> thông báo lỗi
            if (!$color || $color->quantity < $item->quantity) {
                throw new \Exception("Số lượng không đủ"); // dừng và ném lỗi
            }

            // ✅ Trừ số lượng tồn kho của màu tương ứng
            $color->decrement('quantity', $item->quantity);

            // ✅ Tạo bản ghi mới trong bảng order_items
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item->product_id,
                'color_id'   => $item->color_id,
                'quantity'   => $item->quantity,
                'price'      => $item->product->price, // Lưu giá gốc của sản phẩm
            ]);

            // ✅ Nếu có mã giảm giá được áp dụng
            if ($coupon) {
                // Lưu lại mã giảm giá đã dùng để không dùng lại được nữa
                UsedCoupon::create([
                    'customer_id' => $customerId,
                    'coupon_id'   => $coupon->id
                ]);

                // Trừ số lượng mã còn lại
                $coupon->decrement('quantity');
            }
        }
    }


    public function sendConfirmationMail($user, $order, $items, $total)
    {
        if (strtolower($order->payment_method) === 'cod') {
            Mail::to($user->email)->queue(
                (new OrderConfirmation($order, $items->toArray(), $total))->delay(now()->addSeconds(5))
            );
        }
    }
}
