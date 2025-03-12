<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            'status' => "success",
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
            $customer->gender = $request->gender ? 1 : 0;
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
    public function image(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);

            if ($request->hasFile('image')) {
                // $file = $request->file('image');
                // // ✅ Xóa ảnh cũ nếu có
                // if (!empty($customer->image)) {
                //     Storage::delete("public/imgCustomer/{$customer->image}");
                // }
                // // ✅ Tạo tên file mới & lưu vào thư mục
                // $filename = time() . '.' . $file->getClientOriginalExtension();
                // $file->storeAs('public/imgCustomer', $filename);
                if ($customer->image) {
                    $imagePath = public_path('imgCustomer/' . $customer->image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $image = $request->file('image');
                $filename = time() . " - " . $image->getClientOriginalName();
                $image->move(public_path("imgCustomer"), $filename);
                $customer->image = $filename;
                $customer->save();

                return response()->json([
                    'message' => 'Cập nhật ảnh thành công!',
                    'image' => asset("imgCustomer/{$filename}"),
                    'status' => "success",
                ]);
            }
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
