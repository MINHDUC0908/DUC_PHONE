<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function index()
    {
        try {
            $new = News::all();
            return response()->json([
                'message' => true,
                'data' => $new,
            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Lỗi',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function show($id)
    {
        try {
            $new = News::findOrFail($id);
            return response()->json([
                'message' => 'Tìm thấy bài viết thành công',
                'data' => $new,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Không thể tìm thấy bài viết',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function New()
    {
        try {
            $new = News::orderBy('created_at', 'DESC')
                                ->take(6)
                                ->get();
            return response()->json([
                'message' => true,
                'data' => $new,
            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết.',
                'error' =>$e->getMessage(),
            ], 500);
        }
    }
    public function limitNew()
    {
        try {
            $new = News::orderBy('created_at', 'DESC')
                                ->take(3)
                                ->get();
            return response()->json([
                'status' => true,
                'data' => $new,
            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi lấy danh sách bài viết.',
                'error' =>$e->getMessage(),
            ], 500);
        }
    }
}
