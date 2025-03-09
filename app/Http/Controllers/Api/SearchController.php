<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function searchBrand(Request $request)
    {
        $brandName = $request->input("q");

        if (!$brandName) {
            return response()->json([]); 
        }
        $products = Product::with('brand')
            ->whereHas('brand', function ($query) use ($brandName) {
                $query->where('brand_name', 'LIKE', "%{$brandName}%");
            })
            ->get();

        return response()->json([
            'message' => "Tìm thấy sản phẩm",
            "data" => $products
        ]); 
    }
    public function searchCategory(Request $request)
    {
        $categoryName = $request->input("q");

        if (!$categoryName) {
            return response()->json([]); 
        }
        $products = Product::with('category')
            ->whereHas('category', function ($query) use ($categoryName) {
                $query->where('category_name', 'LIKE', "%{$categoryName}%");
            })
            ->get();

        return response()->json([
            'message' => "Tìm thấy sản phẩm",
            "data" => $products
        ]); 
    }
}
