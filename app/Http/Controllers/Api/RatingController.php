<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Order; // Giả sử có bảng lưu đơn hàng
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RatingController extends Controller
{
    protected $ratingService;
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function storeReview(Request $request)
    {
        try {
            return $this->ratingService->storeReview($request);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Lỗi khi thêm đánh giá sản phẩm",
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        return $this->ratingService->getRatingsByProductId($request->input('product_id'));
    }
}
