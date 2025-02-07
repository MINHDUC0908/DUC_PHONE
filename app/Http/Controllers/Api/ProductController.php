<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $product = Product::with(['category', 'brand'])->orderBy('id', 'DESC')->get();
            return response()->json([
                'message' => 'Sản phẩm',
                'data' => $product,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Lỗi truy vấn cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            // Lấy sản phẩm và thông tin liên quan
            $product = Product::with('colors')->findOrFail($id);

            // Tăng lượt xem
            $product->increment('views');

            // Lấy các sản phẩm liên quan cùng danh mục nhưng không bao gồm sản phẩm hiện tại
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $id)
                ->limit(5) // Giới hạn số lượng sản phẩm liên quan
                ->get();

            return response()->json([
                'message' => 'Chi tiết sản phẩm',
                'data' => $product,
                'related_products' => $relatedProducts, // Thêm danh sách sản phẩm liên quan
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Lỗi truy vấn cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function incrementProduct()
    {
        try {
            $topViewedProducts = Product::orderBy('views', 'desc')
                                ->take(6)
                                ->get();
            return response()->json([
                'success' => true,
                'data' => $topViewedProducts
            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sản phẩm.',
                'error' =>$e->getMessage(),
            ], 500);
        }
    }
    public function ProductNew()
    {
        try {
            $productNew = Product::orderBy('created_at', 'DESC')
                                ->take(6)
                                ->get();
            return response()->json([
                'message' => true,
                'data' => $productNew,
            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách sản phẩm.',
                'error' =>$e->getMessage(),
            ], 500);
        }
    }
}
