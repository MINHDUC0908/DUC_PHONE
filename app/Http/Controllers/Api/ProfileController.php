<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function changePassword(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Lấy ID khách hàng đang đăng nhập
        $customerId = Auth::id();
        $customer = Customer::find($customerId);

        if (!$customer) {
            return response()->json([
                'message' => 'Khách hàng không tồn tại'
            ], 404);
        }

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'message' => 'Mật khẩu không chính xác'
            ], 400);
        }

        // Cập nhật mật khẩu mới
        $customer->password = Hash::make($request->new_password);
        $customer->save();

        // Trả về phản hồi thành công
        return response()->json([
            'message' => 'Mật khẩu đã được thay đổi thành công!'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            if (!$customer)
            {
                return response()->json([
                    'message' => 'Không tìm thấy người dùng',
                ]);
            }
            $customer->name = $request->input('name');
            $customer->date = $request->input('date');
            $customer->gender = $request->input('gender');
            $customer->save();
            return response()->json([
                'message' => 'Cập nhật thành công',
                'data' => $customer,

            ], 200);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Lỗi khi cập nhật dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
