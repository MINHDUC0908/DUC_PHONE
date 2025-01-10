<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check())
        {
            return redirect()->route('login');
        }
        $name = Auth::user()->name;
        $customer = Customer::count();  
        $product = Product::count();
        $order = Order::all();
        $countOrder = Order::whereIn('status', ['Waiting for confirmation', 'Processing', 'Delivering', 'Completed'])->count();
        return view('admin.dashboard', compact('name', 'countOrder', 'customer', 'product', 'order'));
    }
}
