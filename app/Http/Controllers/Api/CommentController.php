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
            ->whereNull('parent_id') // Chỉ lấy bình luận gốc
            ->with([
                'customer:id,name,email,image', // Lấy thêm image của customer
                'admin:id,name,email,image',    // Lấy thêm image của admin
                'replies' => function ($query) {
                    $query->with([
                        'customer:id,name,email,image',
                        'admin:id,name,email,image',
                        'replies'
                    ]);
                }
            ])
            ->orderBy("created_at", "DESC")
            ->get()
            ->map(fn($comment) => $this->formatComment($comment));

        return response()->json(['comments' => $comments]);
    }

    private function formatComment($comment)
    {
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'author' => $comment->admin ? $comment->admin->name : ($comment->customer ? $comment->customer->name : 'Anonymous'),
            'author_type' => $comment->admin_id ? 'admin' : 'customer',
            'author_details' => $comment->admin ? [
                'id' => $comment->admin->id,
                'name' => $comment->admin->name,
                'email' => $comment->admin->email,
                'image' => $comment->admin->image, // Ảnh đại diện admin
            ] : ($comment->customer ? [
                'id' => $comment->customer->id,
                'name' => $comment->customer->name,
                'email' => $comment->customer->email,
                'image' => $comment->customer->image, // Ảnh đại diện customer
            ] : null),
            'replies' => $comment->replies->map(fn($reply) => $this->formatComment($reply))
        ];
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
