<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::whereNull('parent_id')
            ->with('replies', 'customer', 'admin')
            ->orderBy("created_at", "DESC")
            ->get();
        return view("admin.comment.list", compact('comments'));
    }

    public function reply(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);
        $comment = new Comment();
        $comment->product_id = $request->product_id;
        $comment->content = $request->content;
        $comment->parent_id = $commentId;
        $comment->admin_id = Auth::id(); // Admin trả lời
        $comment->save();

        return back()->with('success', 'Trả lời bình luận thành công!');
    }
    public function destroy($id)
    {
        $comment = Comment::find($id);
    
        if (!$comment) {
            return response()->json(['error' => 'Bình luận không tồn tại!'], 404);
        }
        $comment->replies()->delete();
        $comment->delete();
        return response()->json(['success' => 'Xóa bình luận thành công!']);
    }
}
