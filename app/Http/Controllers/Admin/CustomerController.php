<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(10);
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
