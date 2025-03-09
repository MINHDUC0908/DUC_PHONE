<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        $status = $request->input('status', 'Waiting for confirmation');
        $orders = Order::where('status', $status)->orderBy("created_at", "DESC")->paginate(25);
        return view('admin.order.order', compact('orders', 'status'));
    }
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();
        return redirect()->back()->with('success', 'Trạng thái đơn hàng đã được cập nhật');
    }
    public function show($id)
    {
        $order = Order::with('payments')->findOrFail($id);
    
        return view('admin.order.show', compact('order'));
    }    
}
