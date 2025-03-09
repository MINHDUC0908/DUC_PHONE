<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class NewController extends Controller
{
    public function index()
    {
        try {
            $news = News::orderBy("id", "DESC")->paginate(5);;
            return view('admin.new.list', compact('news'));
        } catch(Exception $e)
        {
            Log::debug($e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    public function create()
    {
        return view('admin.new.add');
    }
    public function store(Request $request)
    {
        try {
            $new = new News();
            $new->title = $request->input('title');
            $new->outstanding = $request->input('outstanding');

            if ($request->hasFile('images')) {
                $image = $request->file('images');
                $imageName = time() . '-' . $image->getClientOriginalName();

                // Tạo thumbnail trước khi di chuyển file
                $thumbnailPath = 'new/thumbnails/' . $imageName;
                // Đọc file ảnh trực tiếp từ temporary path
                $thumbnail = Image::make($image->getRealPath())
                    ->resize(300, 300)
                    ->blur(10);
                Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());
                $new->thumbnail = $thumbnailPath;

                $image->move(public_path('imgnew'), $imageName);
                $new->images = $imageName;
            } else {
                return back()->with('error', 'Ảnh sản phẩm bị bắt buộc.');
            }

            $new->save();
            return redirect()->route('new.list')->with('success', 'Bài viết đã được thêm thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
    public function edit($id)
    {
        $new = News::findOrFail($id);
        return view("admin.new.edit", compact("new"));
    }
    public function update(Request $request, $id)
    {
        $new = News::findOrFail($id);
        $new->title = $request->input("title");
        $new->outstanding = $request->input("outstanding");
        // Kiểm tra và xử lý ảnh thumbnail
        if ($request->hasFile('images')) {
            // Xóa ảnh cũ nếu có
            if ($new->images) {
                $imagePath = public_path('imgnew/' . $new->images);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $image = $request->file('images');
            $imageName = time() . ' - ' . $image->getClientOriginalName();

            // Tạo thumbnail trước khi di chuyển file
            $thumbnailPath = 'new/thumbnails/' . $imageName;
            // Đọc file ảnh từ temporary path
            $thumbnail = Image::make($image->getRealPath())
                ->resize(300, 300)
                ->blur(10);  // Làm mờ ảnh thumbnail
            Storage::disk('public')->put($thumbnailPath, $thumbnail->encode());

            // Lưu thumbnail vào CSDL
            $new->thumbnail = $thumbnailPath;

            // Di chuyển ảnh gốc vào thư mục imgProduct
            $image->move(public_path('imgnew'), $imageName);
            $new->images = $imageName;  // Lưu ảnh gốc vào CSDL
        }
        $new->save();
        return redirect()->route("new.list")->with("status", "Cập nhaath tin tức thành công!!!");
    }
    public function delete($id)
    {
        $new = News::findOrFail($id);
    
        // Xóa ảnh gốc nếu tồn tại
        if ($new->images) {
            $imagePath = public_path('imgnew/' . $new->images);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Xóa ảnh thumbnail nếu tồn tại
        if ($new->thumbnail) {
            Storage::disk('public')->delete($new->thumbnail);
        }    
        // Xóa bản ghi khỏi CSDL
        $new->delete();
        return redirect()->route("new.list")->with("status", "Xóa tin tức thành công!!!");
    }
}
