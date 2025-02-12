<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customer.list', compact('customers'));
    }
    public function update($id, Request $request)
    {
        $customer = Customer::findOrfail($id);
        $customer->selected = $request->input('selected');
        $customer->save();
        return redirect()->back()->with('Cập nhật thành công');
    }
}
