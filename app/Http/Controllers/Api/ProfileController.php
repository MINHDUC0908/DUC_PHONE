<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\ProfileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }
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
            'status' => "success",
            'message' => 'Mật khẩu đã được thay đổi thành công!'
        ], 200);
    }
    public function update(Request $request, $id)
    {
        try {
            return $this->profileService->updateProfile($request, $id);
        } catch (Exception $e)
        {
            return response()->json([
                'message' => 'Lỗi khi cập nhật dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function image(Request $request, $id)
    {
        try {
            return $this->profileService->image($request, $id);
            return response()->json([
                'message' => 'Không có ảnh nào được tải lên!',
            ], 400);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ!',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật dữ liệu',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
