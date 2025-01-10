<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Lấy tất cả bình luận cho sản phẩm với thông tin bình luận và câu trả lời
    public function index($productId)
    {
        $comments = Comment::where('product_id', $productId)
            ->whereNull('parent_id') // Lọc bình luận cấp 1 (không phải bình luận trả lời)
            ->with([
                'customer:id,name', // eager load customer
                'admin:id,name',     // eager load admin
                'replies.customer:id,name', // eager load replies của customer
                'replies.admin:id,name',    // eager load replies của admin
            ])
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'author' => $comment->customer ? $comment->customer->name : ($comment->admin ? $comment->admin->name : 'Anonymous'),
                    'author_type' => $comment->customer_id ? 'customer' : 'admin', // Loại tác giả
                    'replies' => $comment->replies->map(function ($reply) {
                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'author' => $reply->customer ? $reply->customer->name : ($reply->admin ? $reply->admin->name : 'Anonymous'),
                            'author_type' => $reply->customer_id ? 'customer' : 'admin', // Loại tác giả trả lời
                        ];
                    })
                ];
            });
    
        // Trả về kết quả dưới dạng JSON
        return response()->json(['comments' => $comments]);
    }

    // Tạo bình luận mới (bao gồm bình luận và câu trả lời)
    public function store(Request $request, $productId)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        $customerId = Auth::id();
        if (!$customerId) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập tài khoản',
            ], 401);
        }

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id' // Kiểm tra nếu là câu trả lời
        ]);

        // Tạo mới bình luận
        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->product_id = $productId;
        $comment->parent_id = $validated['parent_id'] ?? null;

        // Gán customer_id vào bình luận
        $comment->customer_id = $customerId;

        // Lưu bình luận
        $comment->save();

        // Trả về bình luận mới sau khi lưu, bao gồm thông tin người dùng
        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'author' => Auth::user()->name, // Tên người dùng đã đăng nhập
                'author_type' => 'customer',   // Xác định loại người dùng là customer
                'parent_id' => $comment->parent_id
            ]
        ], 201);
    }
}
