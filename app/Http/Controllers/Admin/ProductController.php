<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $name = Auth::user()->name;
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
                    ->join('brands', 'brands.id', '=', 'products.brand_id')
                    ->orderBy('products.updated_at', 'DESC')
                    ->select('products.*', 'categories.category_name', 'brands.brand_name')
                    ->paginate(15);
        return view('admin.product.list', compact('products', 'name'));
    }
    public function create()
    {   
        $categories = Category::all();
        $brands = Brand::all();
        $name = Auth::user()->name;
        return view('admin.product.create', compact('categories', 'brands', 'name'));
    }
    public function store(ProductRequest $request)
    {
        // $products = Product::where('name', 'like', '%keyword%')->get(); // Tìm sản phẩm theo tên
        try {
            $product = new Product();
            $product->product_name = $request->input('product_name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->outstanding = $request->input('outstanding');
            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $imageName = time() . '-' . $image->getClientOriginalName();
                
                // Tạo thumbnail trước khi di chuyển file
                $thumbnailPath = 'products/thumbnails/' . $imageName;
                // Đọc file ảnh trực tiếp từ temporary path
                $thumbnail = Image::make($image->getRealPath())
                    ->resize(300, 300)
                    ->blur(10);
                Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
                $product->thumbnail = $thumbnailPath;


                // Di chuyển ảnh gốc vào thư mục imgProduct
                $image->move(public_path('imgProduct'), $imageName);
                $product->images = $imageName;
            }
            if ($request->hasFile('description_image')) {
                $addImages = [];
                foreach ($request->file('description_image') as $image) {
                    $imageName = time() . '-' . $image->getClientOriginalName();
                    $image->move(public_path('imgDescriptionProduct'), $imageName);
                    $addImages[] = $imageName;
                }
                $product->description_image = json_encode($addImages);
            }
            $product->category_id = $request->input('category_id');
            $product->brand_id = $request->input('brand_id');
            
            $product->save(); 

            return redirect()->route('product.list')->with('status', 'Sản phẩm đã được thêm thành công.');
        } catch (Exception $e) {
            Log::error('Lỗi: ' . $e->getMessage()); 
            return redirect()->route('product.create')->with('error', 'Có lỗi xảy ra khi thêm sản phẩm.');
        }
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.product.show', compact('product'));
    }
    public function edit($id)
    {
        $name = Auth::user()->name;
        $categories = Category::all();
        $product = Product::findOrFail($id);
        return view('admin.product.edit', compact('product', 'categories', 'name'));
    }
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            if(!$product)
            {
                return response()->json([
                    'error' => 'Product not found',
                ], 404);
            }
            $product->product_name = $request->input('product_name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->outstanding = $request->input('outstanding');
            $product->category_id = $request->input('category_id');
            $product->brand_id = $request->input('brand_id');
            $product->save(); 
            // Kiểm tra và xử lý ảnh thumbnail
            if ($request->hasFile('images')) {
                // Xóa ảnh cũ nếu có
                if ($product->images) {
                    $imagePath = public_path('imgProduct/' . $product->images);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $image = $request->file('images');
                $imageName = time() . ' - ' . $image->getClientOriginalName();

                // Tạo thumbnail trước khi di chuyển file
                $thumbnailPath = 'products/thumbnails/' . $imageName;
                // Đọc file ảnh từ temporary path
                $thumbnail = Image::make($image->getRealPath())
                    ->resize(300, 300)
                    ->blur(10);  // Làm mờ ảnh thumbnail
                Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

                // Lưu thumbnail vào CSDL
                $product->thumbnail = $thumbnailPath;

                // Di chuyển ảnh gốc vào thư mục imgProduct
                $image->move(public_path('imgProduct'), $imageName);
                $product->images = $imageName;  // Lưu ảnh gốc vào CSDL
            }
            if ($request->hasFile('description_image'))
            {
                if ($product->description_image)
                {
                    $description_images = json_decode($product->description_image);
                    foreach($description_images as $image)
                    {
                        $imagePath = public_path('imgDescriptionProduct/' . $image);
                        if(file_exists($imagePath))
                        {
                            unlink($imagePath);
                        }
                    }
                    $addImages = [];
                    foreach($request->file('description_image') as $image)
                    {
                        $imageName = time() . '_' . $image->getClientOriginalName();
                        $image->move(public_path('imgDescriptionProduct'),$imageName);
                        $addImages[] = $imageName;
                    }
                    $product->description_image = json_encode($addImages);
                }
            }
            $product->save();
            return redirect()->route('product.list')->with('status', 'Product updated successfully');
        } catch (Exception $e)
        {
            return redirect()->route('product.list')->with('error', 'An error occurred while updating the product: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            if(!$product)
            {
                return response()->json([
                    'error' => 'Product not found',
                ], 404);
            } else {
                if ($product->images)
                {
                    $imagePath = public_path('imgProduct/' . $product->images);
                    if(file_exists($imagePath))
                    {
                        unlink($imagePath);
                    }
                }
                if ($product->description_image)
                {
                    $description_images = json_decode($product->description_image);
                    foreach($description_images as $image)
                    {
                        $imagePath = public_path('imgDescriptionProduct/' . $image);
                        if (file_exists($imagePath))
                        {
                            unlink($imagePath);
                        }
                    }
                }
                $product->delete();
                return redirect()->route('product.list')->with('status', 'Product deleted successfully');
            }
        } catch (Exception $e)
        {
            return redirect()->route('product.list')->with('error', 'An error occurred while deleting the product: ' . $e->getMessage());
        }
    }
    public function getBrandsByCategory(Request $request)
    {
        $brands = Brand::where('category_id', $request->category_id)->get();
        return response()->json($brands);
    }
}
