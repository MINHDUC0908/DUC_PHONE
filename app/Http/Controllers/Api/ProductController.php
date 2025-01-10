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
            $product = Product::with(['colors'])->findOrFail($id);
            $product->increment('views');
            return response()->json([
                'message' => 'Chi tiết sản phẩm',
                'data' => $product,
            ]);
        } catch (QueryException $e) {
            // Xử lý lỗi truy vấn cơ sở dữ liệu
            return response()->json([
                'message' => 'Lỗi truy vấn cơ sở dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Xử lý lỗi khi không tìm thấy sản phẩm
            return response()->json([
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        } catch (\Exception $e) {
            // Xử lý các lỗi khác
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
