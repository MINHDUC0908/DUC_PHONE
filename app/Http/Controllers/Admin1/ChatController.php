<?php

namespace App\Http\Controllers\Admin;

use App\Events\ChatMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{  
    public function show($id)
    {
        $customers = Customer::whereHas('messages')->get();
        $user = Auth::user()->id;
        $customer = Customer::findOrFail($id);
        $messages = Message::where('customer_id', $customer->id)
                            ->where('user_id', $user)
                            ->orderBy('created_at', 'asc') 
                            ->get();
        return view('admin.chat.chat', compact('customer', 'messages', 'customers'));
    }
    
    public function sendMessage(Request $request, $id)
    {
        $message = $request->input('message');
    
        // Kiểm tra nếu không có tin nhắn
        if (empty($message)) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty']);
        }
        
        // Kiểm tra khách hàng
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }
        
        $user = Auth::user()->id;
        $sender = 'Admin';
        $mess = Message::create([
            'customer_id' => $customer->id,
            'user_id' => $user,
            'message' => $message,
            'sender' => $sender,
        ]);

        // Phát sự kiện chat
        broadcast(new ChatMessageSent($message, $sender))->toOthers();

        return redirect()->json([
            'success' => true, 
            'message' => $message,
        ]);
    }
}
