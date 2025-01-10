<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Exception;
use Illuminate\Http\Request;

class NewController extends Controller
{
    public function index()
    {
        try {
            $news = News::all();
            return view('admin.new.list', compact('news'));
        } catch(Exception $e)
        {

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

}
