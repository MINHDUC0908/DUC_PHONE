<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Order; // Giả sử có bảng lưu đơn hàng
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RatingController extends Controller
{
    public function storeReview(Request $request)
    {
        try {
            $customer = Auth::id();
            if (!$customer) {
                return response()->json([
                    'message' => "Vui lòng đăng nhập để đánh giá sản phẩm",
                ], 401);
            }
    
            // Kiểm tra xem khách hàng đã mua sản phẩm chưa
            $hasPurchased = Order::where('customer_id', $customer)
                ->whereHas('orderItems', function ($query) use ($request) {
                    $query->where('product_id', $request->input("product_id"));
                })
                ->exists();
    
            if (!$hasPurchased) {
                return response()->json([
                    "status" => "error",
                    'message' => "Bạn chỉ có thể đánh giá sản phẩm đã mua",
                ], 403);
            }
            $rating = new Rating();
            $rating->customer_id = $customer;
            $rating->product_id = $request->input("product_id");
            $rating->rating = $request->input("rating");
            $rating->comment = $request->input("comment");
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/rating', $imageName);
                $rating->image = $imageName;
            }
            $rating->save();
    
            return response()->json([
                "status" => "success",
                "message" => "Đánh giá sản phẩm thành công",
                "data" => $rating
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Lỗi khi thêm đánh giá sản phẩm",
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function index(Request $request)
    {
        // Lấy product_id từ request
        $product_id = $request->input('product_id');

        // Kiểm tra nếu product_id không được gửi   
        if (!$product_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Thiếu product_id'
            ], 400);
        }

        // Lấy danh sách đánh giá
        $ratings = Rating::with('customer')
            ->where('product_id', $product_id)
            ->orderBy("created_at", "DESC")
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $ratings
        ]);
    }
}
