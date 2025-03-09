<?php

namespace App\Http\Controllers\admin;

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
    
    public function index()
    {
        $customers = Customer::whereHas('messages')->get();
        return view('admin.chat.index', compact('customers'));
    }    

    public function sendMessageUser(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'success' => false, 
                'message' => 'Customer not found'
            ], 404);
        }

        $user = Auth::id();
        $message = $request->input('message');
        $sender = 'Admin';
        $chatMessage = Message::create([
            'customer_id' => $customer->id,
            'user_id' => $user,
            'message' => $message,
            'sender' => $sender,
        ]);
        try {
            broadcast(new ChatMessageSent($chatMessage->message, $chatMessage->sender))->toOthers();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Broadcasting failed: ' . $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

}
