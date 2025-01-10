<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $customer = Auth::id();
        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $messages = Message::where('customer_id', $customer)->get();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Hiển thị tin nhắn thành công',
            'data' => $messages,
        ]);
    } 
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $messageContent = $request->input('message');
        $customerId = Auth::id();
        if (!$customerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please log in to send messages.',
            ], 401);
        }
        $users = User::all();
        foreach ($users as $user) {
            Message::create([
                'customer_id' => $customerId,
                'user_id' => $user->id,
                'message' => $messageContent,
                'sender' => 'Customer'
            ]);
        }

        // Phát sự kiện tin nhắn mới từ khách hàng
        broadcast(new ChatMessageSent($messageContent, 'Customer'))->toOthers();

        // Gửi tin nhắn tự động từ Admin
        $adminMessage = 'Xin chào! DUCCOMPUTER Shop rất vui được hỗ trợ bạn. Bạn cần giúp đỡ gì không ạ?';
        foreach ($users as $user) {
            Message::create([
                'customer_id' => $customerId,
                'user_id' => $user->id,
                'message' => $adminMessage,
                'sender' => 'Admin'
            ]);
        }
        broadcast(new ChatMessageSent($adminMessage, 'Admin'))->toOthers();
        return response()->json([
            'status' => 'success',
            'message' => 'Message sent!',
        ], 200);
    }
}
