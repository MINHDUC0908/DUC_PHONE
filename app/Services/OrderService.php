<?php

namespace App\Services;

use App\Repositories\CartRepository;
use App\Repositories\CouponRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ShippingRepository;
use App\Services\PaymentService;

class OrderService
{
    public function __construct(
        protected CartRepository     $cartRepo,
        protected CouponRepository   $couponRepo,
        protected ShippingRepository $shippingRepo,
        protected OrderRepository    $orderRepo,
        protected PaymentService     $paymentService
    ) {}

    public function checkout($request)
    {
        $customerId = auth()->id();

        // 1. Kiểm tra địa chỉ giao hàng
        $address = $this->shippingRepo->getAddress(
            $customerId,
            $request->shipping_address_id
        );

        if (!$address) {
            return $this->error("Địa chỉ không hợp lệ");
        }

        // 2. Lấy danh sách sản phẩm đã chọn trong giỏ hàng
        $cartItems = $this->cartRepo->getSelectedItems($customerId);

        if ($cartItems->isEmpty()) {
            return $this->error("Giỏ hàng trống");
        }

        // 3. Tính tổng giá đơn hàng
        $total    = $this->cartRepo->calculateTotal($cartItems);
        $discount = 0;
        $coupon   = null;

        // 4. Áp dụng mã giảm giá nếu có
        if ($request->coupon) {
            [$discount, $coupon] = $this->couponRepo->applyCoupon(
                $request->coupon,
                $total
            );

            $total -= $discount;
        }

        // 5. Tạo đơn hàng mới
        $order = $this->orderRepo->createOrder(
            $customerId,
            $address->id,
            $total,
            $request->payment_method,
            $discount,
            $coupon
        );

        // 6. Thêm sản phẩm vào đơn hàng và cập nhật tồn kho
        $this->orderRepo->addItemsAndUpdateStock(
            $order,
            $cartItems,
            $coupon,
            $customerId
        );

        // 7. Gửi email xác nhận đơn hàng
        $this->orderRepo->sendConfirmationMail(
            $request->user(),
            $order,
            $cartItems,
            $total
        );

        // 8. Nếu là COD thì mới xóa sản phẩm đã chọn khỏi giỏ hàng
        if (strtolower($request->payment_method) === 'cod') {
            $this->cartRepo->clearSelectedItems($customerId);
        }

        // 9. Xử lý thanh toán (VNPay, ZaloPay, PayPal, COD)
        return $this->paymentService->handle(
            $request->payment_method,
            $order,
            $total,
            $customerId
        );
    }

    protected function error($msg, $status = 400)
    {
        return [
            'data' => [
                'status'  => 'error',
                'message' => $msg
            ],
            'status' => $status
        ];
    }
}
