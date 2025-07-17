<?php

namespace App\Services;

use App\Repositories\RatingRepository;
use Illuminate\Support\Facades\Auth;

class RatingService
{
    protected $ratingRepository;

    public function __construct(RatingRepository $ratingRepository)
    {
        $this->ratingRepository = $ratingRepository;
    }

    public function storeReview($request)
    {
        $customer = Auth::id();
        if (!$customer) {
            return response()->json([
                "status" => "error",
                "message" => "Bạn cần đăng nhập để đánh giá sản phẩm"
            ], 401);
        }

        // Kiểm tra xem khách hàng đã mua sản phẩm chưa
        $hasPurchased = $this->ratingRepository->checkPurchased($customer, $request->input("product_id"));

        if (!$hasPurchased) {
            return response()->json([
                "status" => "error",
                "message" => "Bạn cần mua sản phẩm trước khi đánh giá"
            ], 403);
        }
        $rating = $this->ratingRepository->store($request, $customer);
        return response()->json([
            "status" => "success",
            "message" => "Đánh giá sản phẩm thành công",
            "data" => $rating
        ], 201);
    }


    public function getRatingsByProductId($productId)
    {
        $rating = $this->ratingRepository->getRatingsByProductId($productId);
        return response()->json([
            "status" => "success",
            "data" => $rating
        ]);
    }
}